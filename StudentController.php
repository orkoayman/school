<?php

namespace App\Controllers;

use App\Models\AcademicYearModel;
use App\Models\ClassModel;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;

class StudentController extends BaseController
{
    protected StudentModel $studentModel;
    protected EnrollmentModel $enrollmentModel;
    protected ClassModel $classModel;
    protected AcademicYearModel $academicYearModel;

    public function __construct()
    {
        $this->studentModel      = new StudentModel();
        $this->enrollmentModel   = new EnrollmentModel();
        $this->classModel        = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
    }

    public function index()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        $search      = $this->request->getGet('q');
        $classId     = $this->request->getGet('class_id');

        if ($search) {
            $students = $this->studentModel->search($search);
        } elseif ($currentYear) {
            // Show current year roster, optionally filtered by class
            $builder = $this->enrollmentModel
                ->select('enrollments.*, students.name, students.student_code, students.card_number, students.parent_phone, classes.name as class_name')
                ->join('students', 'students.id = enrollments.student_id')
                ->join('classes', 'classes.id = enrollments.class_id')
                ->where('enrollments.academic_year_id', $currentYear['id']);

            if ($classId) {
                $builder->where('enrollments.class_id', $classId);
            }

            $students = $builder->orderBy('classes.numeric_order', 'ASC')
                ->orderBy('enrollments.roll_in_class', 'ASC')
                ->findAll();
        } else {
            $students = [];
        }

        $data = [
            'title'       => 'ছাত্র/ছাত্রী তালিকা',
            'students'    => $students,
            'classes'     => $this->classModel->getOrdered(),
            'currentYear' => $currentYear,
            'search'      => $search,
            'selectedClassId' => $classId,
        ];

        return view('students/index', $data, ['saveData' => false]);
    }

    public function create()
    {
        $data = [
            'title'       => 'নতুন ছাত্র/ছাত্রী যোগ করুন',
            'classes'     => $this->classModel->getOrdered(),
            'currentYear' => $this->academicYearModel->getCurrent(),
        ];

        return view('students/create', $data, ['saveData' => false]);
    }

    public function store()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        if (! $currentYear) {
            return redirect()->back()->with('error', 'প্রথমে একটি চলতি শিক্ষাবর্ষ নির্ধারণ করুন।');
        }

        $rules = [
            'name'          => 'required|min_length[2]|max_length[150]',
            'student_code'  => 'required|is_unique[students.student_code]',
            'parent_phone'  => 'required|min_length[11]',
            'class_id'      => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentId = $this->studentModel->insert([
            'name'          => $this->request->getPost('name'),
            'student_code'  => $this->request->getPost('student_code'),
            'card_number'   => $this->request->getPost('card_number') ?: null,
            'date_of_birth' => $this->request->getPost('date_of_birth') ?: null,
            'gender'        => $this->request->getPost('gender') ?: null,
            'father_name'   => $this->request->getPost('father_name'),
            'mother_name'   => $this->request->getPost('mother_name'),
            'parent_phone'  => $this->request->getPost('parent_phone'),
            'address'       => $this->request->getPost('address'),
        ], true);

        $this->enrollmentModel->insert([
            'student_id'       => $studentId,
            'academic_year_id' => $currentYear['id'],
            'class_id'         => $this->request->getPost('class_id'),
            'roll_in_class'    => $this->request->getPost('roll_in_class') ?: null,
            'section'          => $this->request->getPost('section') ?: null,
        ]);

        return redirect()->to('/students')->with('message', 'ছাত্র/ছাত্রী সফলভাবে যোগ করা হয়েছে।');
    }

    public function edit(int $id)
    {
        $student = $this->studentModel->find($id);
        if (! $student) {
            return redirect()->to('/students')->with('error', 'ছাত্র/ছাত্রী পাওয়া যায়নি।');
        }

        $currentYear = $this->academicYearModel->getCurrent();
        $enrollment  = $currentYear ? $this->enrollmentModel->forStudentAndYear($id, $currentYear['id']) : null;

        $data = [
            'title'       => 'তথ্য সম্পাদনা',
            'student'     => $student,
            'enrollment'  => $enrollment,
            'classes'     => $this->classModel->getOrdered(),
            'currentYear' => $currentYear,
        ];

        return view('students/edit', $data, ['saveData' => false]);
    }

    public function update(int $id)
    {
        $student = $this->studentModel->find($id);
        if (! $student) {
            return redirect()->to('/students')->with('error', 'ছাত্র/ছাত্রী পাওয়া যায়নি।');
        }

        $rules = [
            'name'          => 'required|min_length[2]|max_length[150]',
            'student_code'  => "required|is_unique[students.student_code,id,{$id}]",
            'parent_phone'  => 'required|min_length[11]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->studentModel->update($id, [
            'name'          => $this->request->getPost('name'),
            'student_code'  => $this->request->getPost('student_code'),
            'card_number'   => $this->request->getPost('card_number') ?: null,
            'date_of_birth' => $this->request->getPost('date_of_birth') ?: null,
            'gender'        => $this->request->getPost('gender') ?: null,
            'father_name'   => $this->request->getPost('father_name'),
            'mother_name'   => $this->request->getPost('mother_name'),
            'parent_phone'  => $this->request->getPost('parent_phone'),
            'address'       => $this->request->getPost('address'),
            'status'        => $this->request->getPost('status') ?: 'active',
        ]);

        // Update this year's enrollment (class/roll/section) if it exists
        $currentYear = $this->academicYearModel->getCurrent();
        if ($currentYear) {
            $enrollment = $this->enrollmentModel->forStudentAndYear($id, $currentYear['id']);
            $enrollData = [
                'class_id'      => $this->request->getPost('class_id'),
                'roll_in_class' => $this->request->getPost('roll_in_class') ?: null,
                'section'       => $this->request->getPost('section') ?: null,
            ];

            if ($enrollment) {
                $this->enrollmentModel->update($enrollment['id'], $enrollData);
            } elseif ($this->request->getPost('class_id')) {
                $enrollData['student_id']       = $id;
                $enrollData['academic_year_id'] = $currentYear['id'];
                $this->enrollmentModel->insert($enrollData);
            }
        }

        return redirect()->to('/students')->with('message', 'তথ্য হালনাগাদ করা হয়েছে।');
    }

    public function show(int $id)
    {
        $student = $this->studentModel->find($id);
        if (! $student) {
            return redirect()->to('/students')->with('error', 'ছাত্র/ছাত্রী পাওয়া যায়নি।');
        }

        $data = [
            'title'   => $student['name'] . ' - প্রোফাইল',
            'student' => $student,
            'history' => $this->enrollmentModel->historyForStudent($id),
        ];

        return view('students/show', $data, ['saveData' => false]);
    }
}
