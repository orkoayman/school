<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table            = 'enrollments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'student_id', 'academic_year_id', 'class_id', 'roll_in_class', 'section', 'promoted',
    ];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    /**
     * Get a student's enrollment for a specific academic year.
     */
    public function forStudentAndYear(int $studentId, int $academicYearId)
    {
        return $this->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->first();
    }

    /**
     * Get full academic history for a student, newest year first,
     * joined with year and class names.
     */
    public function historyForStudent(int $studentId)
    {
        return $this->select('enrollments.*, academic_years.year as year_name, classes.name as class_name')
            ->join('academic_years', 'academic_years.id = enrollments.academic_year_id')
            ->join('classes', 'classes.id = enrollments.class_id')
            ->where('enrollments.student_id', $studentId)
            ->orderBy('academic_years.year', 'DESC')
            ->findAll();
    }

    /**
     * Get all enrollments for a given class + academic year (i.e. the class roster).
     */
    public function rosterForClassAndYear(int $classId, int $academicYearId)
    {
        return $this->select('enrollments.*, students.name, students.student_code, students.card_number, students.parent_phone')
            ->join('students', 'students.id = enrollments.student_id')
            ->where('enrollments.class_id', $classId)
            ->where('enrollments.academic_year_id', $academicYearId)
            ->orderBy('enrollments.roll_in_class', 'ASC')
            ->findAll();
    }
}
