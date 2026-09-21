<?php

namespace App\Controllers;

use App\Models\AcademicYearModel;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $academicYearModel = new AcademicYearModel();
        $studentModel       = new StudentModel();
        $enrollmentModel    = new EnrollmentModel();

        $currentYear = $academicYearModel->getCurrent();

        $data = [
            'title'          => 'ড্যাশবোর্ড',
            'currentYear'    => $currentYear,
            'totalStudents'  => $studentModel->where('status', 'active')->countAllResults(),
            'enrolledThisYear' => $currentYear
                ? $enrollmentModel->where('academic_year_id', $currentYear['id'])->countAllResults()
                : 0,
        ];

        return view('dashboard', $data, ['saveData' => false]);
    }
}
