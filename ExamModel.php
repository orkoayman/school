<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table            = 'exams';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['academic_year_id', 'name', 'order_no'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    /** Standard 3 exams created for every academic year */
    public const STANDARD_EXAMS = [
        1 => '১ম সাময়িক',
        2 => '২য় সাময়িক (অর্ধবার্ষিক)',
        3 => 'বার্ষিক',
    ];

    public function forYear(int $academicYearId)
    {
        return $this->where('academic_year_id', $academicYearId)
            ->orderBy('order_no', 'ASC')
            ->findAll();
    }

    /**
     * Create the standard 3 exams for a newly created academic year.
     */
    public function createStandardExamsForYear(int $academicYearId): void
    {
        foreach (self::STANDARD_EXAMS as $order => $name) {
            $this->insert([
                'academic_year_id' => $academicYearId,
                'name'             => $name,
                'order_no'         => $order,
            ]);
        }
    }
}
