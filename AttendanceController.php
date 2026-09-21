<?php

namespace App\Controllers;

use App\Libraries\SmsService;
use App\Models\AcademicYearModel;
use App\Models\AttendanceModel;
use App\Models\ClassModel;
use App\Models\StudentModel;

class AttendanceController extends BaseController
{
    protected AttendanceModel $attendanceModel;
    protected ClassModel $classModel;
    protected AcademicYearModel $academicYearModel;
    protected StudentModel $studentModel;

    public function __construct()
    {
        $this->attendanceModel   = new AttendanceModel();
        $this->classModel        = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->studentModel      = new StudentModel();
    }

    public function index()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        $classId     = $this->request->getGet('class_id');
        $date        = $this->request->getGet('date') ?: date('Y-m-d');

        $roster = [];
        if ($currentYear && $classId) {
            $roster = $this->attendanceModel->rosterForDate((int) $classId, $currentYear['id'], $date);
        }

        $data = [
            'title'       => 'উপস্থিতি',
            'classes'     => $this->classModel->getOrdered(),
            'currentYear' => $currentYear,
            'selectedClassId' => $classId,
            'date'        => $date,
            'roster'      => $roster,
        ];

        return view('attendance/index', $data, ['saveData' => false]);
    }

    /**
     * Teacher submits the whole roster's status at once.
     * Expects: date, class_id, status[student_id] = present|absent
     */
    public function mark()
    {
        $date      = $this->request->getPost('date');
        $classId   = $this->request->getPost('class_id');
        $statuses  = $this->request->getPost('status') ?? []; // [student_id => present|absent]
        $teacherId = session('user_id');
        $smsService = new SmsService();

        if (! $date || ! $classId) {
            return redirect()->back()->with('error', 'তারিখ ও ক্লাস নির্বাচন করুন।');
        }

        $sentCount = 0;

        foreach ($statuses as $studentId => $status) {
            $studentId = (int) $studentId;
            $before    = $this->attendanceModel->forStudentAndDate($studentId, $date);
            $wasAlreadySms = ! empty($before['sms_sent']);

            $record = $this->attendanceModel->markManual($studentId, $date, $status, (int) $teacherId);

            // Send SMS only once per day per student, only when marked present,
            // and only if not already sent (avoids re-sending on repeated form submits).
            if ($status === 'present' && ! $wasAlreadySms) {
                $student = $this->studentModel->find($studentId);
                if ($student) {
                    $time    = date('h:i A', strtotime($record['in_time'] ?? 'now'));
                    $message = $smsService->attendanceMessage($student['name'], 'present', $time);
                    $ok      = $smsService->send($student['parent_phone'], $message, 'attendance', $studentId);

                    if ($ok) {
                        $this->attendanceModel->update($record['id'], ['sms_sent' => 1]);
                        $sentCount++;
                    }
                }
            }
        }

        return redirect()->to('/attendance?class_id=' . $classId . '&date=' . $date)
            ->with('message', "উপস্থিতি সংরক্ষণ করা হয়েছে। {$sentCount} টি SMS পাঠানো হয়েছে।");
    }

    /**
     * Future endpoint for the card-punch hardware to call directly.
     * Expects: card_number (from the RFID reader)
     * Not behind the 'auth' filter set here on purpose - the physical device
     * cannot log in - protect this with a shared device API key before going live.
     */
    public function punch()
    {
        $cardNumber = $this->request->getPost('card_number');
        if (! $cardNumber) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'card_number is required']);
        }

        $student = $this->studentModel->findByCard($cardNumber);
        if (! $student) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'unknown card']);
        }

        $today  = date('Y-m-d');
        $record = $this->attendanceModel->recordCardPunch($student['id'], $today);

        $isFirstPunch = empty($record['out_time']);
        if ($isFirstPunch && empty($record['sms_sent'])) {
            $smsService = new SmsService();
            $time       = date('h:i A');
            $message    = $smsService->attendanceMessage($student['name'], 'present', $time);
            $ok         = $smsService->send($student['parent_phone'], $message, 'attendance', $student['id']);

            if ($ok) {
                $this->attendanceModel->update($record['id'], ['sms_sent' => 1]);
            }
        }

        return $this->response->setJSON(['status' => 'ok', 'student' => $student['name'], 'record' => $record]);
    }
}
