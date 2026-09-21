<?php

namespace App\Controllers;

use App\Libraries\FeeCalculator;
use App\Models\AcademicYearModel;
use App\Models\ClassFeeModel;
use App\Models\ClassModel;
use App\Models\EnrollmentModel;
use App\Models\FeeTypeModel;
use App\Models\PaymentModel;
use App\Models\StudentFeeModel;
use App\Models\StudentModel;

class FeeController extends BaseController
{
    protected ClassModel $classModel;
    protected FeeTypeModel $feeTypeModel;
    protected ClassFeeModel $classFeeModel;
    protected StudentFeeModel $studentFeeModel;
    protected PaymentModel $paymentModel;
    protected AcademicYearModel $academicYearModel;
    protected EnrollmentModel $enrollmentModel;
    protected StudentModel $studentModel;

    public function __construct()
    {
        $this->classModel        = new ClassModel();
        $this->feeTypeModel      = new FeeTypeModel();
        $this->classFeeModel     = new ClassFeeModel();
        $this->studentFeeModel   = new StudentFeeModel();
        $this->paymentModel      = new PaymentModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->enrollmentModel   = new EnrollmentModel();
        $this->studentModel      = new StudentModel();
    }

    /**
     * Overview: every student in the current year with their total due balance.
     * This answers the core requirement - "কোন ছাত্রের কত বেতন বাকি".
     */
    public function index()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        $classId     = $this->request->getGet('class_id');
        $calculator  = new FeeCalculator();

        $rows = [];
        if ($currentYear) {
            $builder = $this->enrollmentModel
                ->select('enrollments.*, students.name, students.student_code, students.parent_phone')
                ->join('students', 'students.id = enrollments.student_id')
                ->where('enrollments.academic_year_id', $currentYear['id']);

            if ($classId) {
                $builder->where('enrollments.class_id', $classId);
            }

            $enrollments = $builder->findAll();

            foreach ($enrollments as $e) {
                $calc = $calculator->calculateForStudent((int) $e['student_id'], (int) $e['class_id'], $currentYear['id']);
                $rows[] = [
                    'student_id'   => $e['student_id'],
                    'name'         => $e['name'],
                    'student_code' => $e['student_code'],
                    'parent_phone' => $e['parent_phone'],
                    'balance'      => $calc['grand_total_balance'],
                ];
            }

            // Show highest dues first
            usort($rows, static fn ($a, $b) => $b['balance'] <=> $a['balance']);
        }

        $data = [
            'title'       => 'ফি ব্যবস্থাপনা - বকেয়ার তালিকা',
            'classes'     => $this->classModel->getOrdered(),
            'currentYear' => $currentYear,
            'selectedClassId' => $classId,
            'rows'        => $rows,
        ];

        return view('fees/index', $data, ['saveData' => false]);
    }

    public function studentLedger(int $studentId)
    {
        $student     = $this->studentModel->find($studentId);
        $currentYear = $this->academicYearModel->getCurrent();
        $yearId      = $this->request->getGet('year') ?: $currentYear['id'] ?? null;

        $enrollment = $yearId ? $this->enrollmentModel->forStudentAndYear($studentId, $yearId) : null;

        $breakdown = [];
        if ($enrollment) {
            $calculator = new FeeCalculator();
            $breakdown  = $calculator->calculateForStudent($studentId, (int) $enrollment['class_id'], (int) $yearId);
        }

        $data = [
            'title'      => 'ফি খতিয়ান - ' . ($student['name'] ?? ''),
            'student'    => $student,
            'enrollment' => $enrollment,
            'yearId'     => $yearId,
            'breakdown'  => $breakdown,
            'feeTypes'   => $this->feeTypeModel->findAll(),
            'payments'   => $yearId ? $this->paymentModel->historyForStudent($studentId, (int) $yearId) : [],
        ];

        return view('fees/ledger', $data, ['saveData' => false]);
    }

    public function recordPayment()
    {
        $data = [
            'student_id'       => $this->request->getPost('student_id'),
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'fee_type_id'      => $this->request->getPost('fee_type_id'),
            'for_month'        => $this->request->getPost('for_month') ?: null,
            'amount'           => $this->request->getPost('amount'),
            'paid_date'        => $this->request->getPost('paid_date') ?: date('Y-m-d'),
            'note'             => $this->request->getPost('note'),
            'received_by'      => session('user_id'),
        ];

        if (! $this->validate([
            'student_id'  => 'required|is_natural_no_zero',
            'fee_type_id' => 'required|is_natural_no_zero',
            'amount'      => 'required|numeric|greater_than[0]',
        ])) {
            return redirect()->back()->with('error', 'ফর্মে ভুল আছে, আবার চেষ্টা করুন।');
        }

        $this->paymentModel->insert($data);

        return redirect()->to('/fees/student/' . $data['student_id'] . '?year=' . $data['academic_year_id'])
            ->with('message', 'পেমেন্ট সংরক্ষণ করা হয়েছে।');
    }

    public function settings()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        $classId     = $this->request->getGet('class_id');

        $data = [
            'title'       => 'ফি সেটিংস',
            'classes'     => $this->classModel->getOrdered(),
            'feeTypes'    => $this->feeTypeModel->findAll(),
            'currentYear' => $currentYear,
            'selectedClassId' => $classId,
            'rates'       => ($classId && $currentYear)
                ? array_column($this->classFeeModel->forClassAndYear((int) $classId, $currentYear['id']), null, 'fee_type_id')
                : [],
        ];

        return view('fees/settings', $data, ['saveData' => false]);
    }

    public function saveStudentOverride()
    {
        $studentId = (int) $this->request->getPost('student_id');
        $yearId    = (int) $this->request->getPost('academic_year_id');
        $feeTypeId = (int) $this->request->getPost('fee_type_id');
        $amount    = (float) $this->request->getPost('amount');
        $note      = $this->request->getPost('note');

        $this->studentFeeModel->upsert($studentId, $yearId, $feeTypeId, $amount, $note);

        return redirect()->to('/fees/student/' . $studentId . '?year=' . $yearId)
            ->with('message', 'বিশেষ রেট সংরক্ষণ করা হয়েছে।');
    }

    public function createFeeType()
    {
        $name      = $this->request->getPost('name');
        $frequency = $this->request->getPost('frequency');

        if (! $name || ! in_array($frequency, ['monthly', 'one_time'], true)) {
            return redirect()->back()->with('error', 'ফি টাইপের নাম ও ধরন সঠিকভাবে দিন।');
        }

        $this->feeTypeModel->insert(['name' => $name, 'frequency' => $frequency]);

        return redirect()->back()->with('message', 'নতুন ফি টাইপ যোগ করা হয়েছে।');
    }

    public function saveSettings()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        $classId     = $this->request->getPost('class_id');
        $amounts     = $this->request->getPost('amount') ?? []; // [fee_type_id => amount]

        if (! $currentYear || ! $classId) {
            return redirect()->back()->with('error', 'ক্লাস ও চলতি শিক্ষাবর্ষ প্রয়োজন।');
        }

        foreach ($amounts as $feeTypeId => $amount) {
            if ($amount === '') {
                continue;
            }
            $this->classFeeModel->upsert((int) $classId, $currentYear['id'], (int) $feeTypeId, (float) $amount);
        }

        return redirect()->to('/fees/settings?class_id=' . $classId)->with('message', 'ফি রেট সংরক্ষণ করা হয়েছে।');
    }
}
