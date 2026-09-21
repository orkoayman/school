<?php

namespace App\Controllers;

use App\Libraries\Grading;
use App\Models\AcademicYearModel;
use App\Models\ClassModel;
use App\Models\EnrollmentModel;
use App\Models\ExamModel;
use App\Models\ExamSummaryModel;
use App\Models\ResultModel;
use App\Models\SubjectModel;

class ResultController extends BaseController
{
    protected ClassModel $classModel;
    protected ExamModel $examModel;
    protected SubjectModel $subjectModel;
    protected EnrollmentModel $enrollmentModel;
    protected ResultModel $resultModel;
    protected ExamSummaryModel $examSummaryModel;
    protected AcademicYearModel $academicYearModel;

    public function __construct()
    {
        $this->classModel        = new ClassModel();
        $this->examModel         = new ExamModel();
        $this->subjectModel      = new SubjectModel();
        $this->enrollmentModel   = new EnrollmentModel();
        $this->resultModel       = new ResultModel();
        $this->examSummaryModel  = new ExamSummaryModel();
        $this->academicYearModel = new AcademicYearModel();
    }

    /**
     * Landing page: pick class + exam to go to entry, or view a specific enrollment's results.
     */
    public function index()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        $enrollmentId = $this->request->getGet('enrollment_id');

        $data = [
            'title'       => 'ফলাফল',
            'classes'     => $this->classModel->getOrdered(),
            'exams'       => $currentYear ? $this->examModel->forYear($currentYear['id']) : [],
            'currentYear' => $currentYear,
        ];

        if ($enrollmentId) {
            $enrollment = $this->enrollmentModel->find($enrollmentId);
            $exams      = $this->examModel->forYear($enrollment['academic_year_id']);
            $cards      = [];
            foreach ($exams as $exam) {
                $summary = $this->examSummaryModel->forEnrollmentAndExam($enrollmentId, $exam['id']);
                $cards[] = ['exam' => $exam, 'summary' => $summary];
            }
            $data['studentResults'] = $cards;
            $data['enrollment']     = $enrollment;
        }

        return view('results/index', $data, ['saveData' => false]);
    }

    public function entryForm()
    {
        $classId = $this->request->getGet('class_id');
        $examId  = $this->request->getGet('exam_id');
        $currentYear = $this->academicYearModel->getCurrent();

        $roster   = [];
        $subjects = [];

        if ($classId && $examId && $currentYear) {
            $subjects = $this->subjectModel->forClass((int) $classId);
            $roster   = $this->enrollmentModel->rosterForClassAndYear((int) $classId, $currentYear['id']);

            // Attach existing marks for each student x subject
            foreach ($roster as &$student) {
                $existing = $this->resultModel
                    ->where('enrollment_id', $student['id'])
                    ->where('exam_id', $examId)
                    ->findAll();

                $marksBySubject = [];
                foreach ($existing as $row) {
                    $marksBySubject[$row['subject_id']] = $row['marks_obtained'];
                }
                $student['marks'] = $marksBySubject;
            }
            unset($student);
        }

        $data = [
            'title'       => 'ফলাফল এন্ট্রি',
            'classes'     => $this->classModel->getOrdered(),
            'exams'       => $currentYear ? $this->examModel->forYear($currentYear['id']) : [],
            'selectedClassId' => $classId,
            'selectedExamId'  => $examId,
            'subjects'    => $subjects,
            'roster'      => $roster,
            'currentYear' => $currentYear,
        ];

        return view('results/entry', $data, ['saveData' => false]);
    }

    /**
     * Saves marks[enrollment_id][subject_id] = marks_obtained, then recalculates
     * each affected enrollment's GPA/pass-fail summary.
     */
    public function saveEntry()
    {
        $examId = $this->request->getPost('exam_id');
        $marks  = $this->request->getPost('marks') ?? []; // [enrollment_id][subject_id] = value

        if (! $examId) {
            return redirect()->back()->with('error', 'পরীক্ষা নির্বাচন করা হয়নি।');
        }

        $subjects = $this->subjectModel->findAll();
        $subjectsById = array_column($subjects, null, 'id');

        $touchedEnrollments = [];

        foreach ($marks as $enrollmentId => $subjectMarks) {
            foreach ($subjectMarks as $subjectId => $value) {
                if ($value === '' || $value === null) {
                    continue; // not entered, skip
                }

                $subject = $subjectsById[$subjectId] ?? null;
                if (! $subject) {
                    continue;
                }

                $grade = Grading::gradeForSubject((float) $value, (int) $subject['full_marks'], (int) $subject['pass_marks']);

                $this->resultModel->saveMark(
                    (int) $enrollmentId,
                    (int) $examId,
                    (int) $subjectId,
                    (float) $value,
                    $grade['grade'],
                    $grade['point']
                );

                $touchedEnrollments[$enrollmentId] = true;
            }
        }

        foreach (array_keys($touchedEnrollments) as $enrollmentId) {
            $this->examSummaryModel->recalculate((int) $enrollmentId, (int) $examId);
        }

        return redirect()->back()->with('message', 'ফলাফল সংরক্ষণ করা হয়েছে ও GPA হালনাগাদ হয়েছে।');
    }

    public function resultCard(int $enrollmentId, int $examId)
    {
        $enrollment = $this->enrollmentModel
            ->select('enrollments.*, students.name, students.student_code, classes.name as class_name, academic_years.year as year_name')
            ->join('students', 'students.id = enrollments.student_id')
            ->join('classes', 'classes.id = enrollments.class_id')
            ->join('academic_years', 'academic_years.id = enrollments.academic_year_id')
            ->find($enrollmentId);

        $exam    = $this->examModel->find($examId);
        $results = $this->resultModel->forEnrollmentAndExam($enrollmentId, $examId);
        $summary = $this->examSummaryModel->forEnrollmentAndExam($enrollmentId, $examId);

        $data = [
            'title'      => 'ফলাফল কার্ড',
            'enrollment' => $enrollment,
            'exam'       => $exam,
            'results'    => $results,
            'summary'    => $summary,
        ];

        return view('results/card', $data, ['saveData' => false]);
    }

    /**
     * Admin-only: force-pass a student who failed due to one subject,
     * per the school's rule that this is a manual exception.
     */
    public function setOverride()
    {
        $enrollmentId = (int) $this->request->getPost('enrollment_id');
        $examId       = (int) $this->request->getPost('exam_id');
        $override     = (bool) $this->request->getPost('override');
        $note         = $this->request->getPost('note') ?? '';

        $this->examSummaryModel->setManualOverride($enrollmentId, $examId, $override, $note, (int) session('user_id'));

        return redirect()->back()->with('message', 'ফলাফলের অবস্থা হালনাগাদ করা হয়েছে।');
    }
}
