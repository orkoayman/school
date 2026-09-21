<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentFeeModel extends Model
{
    protected $table            = 'student_fees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['student_id', 'academic_year_id', 'fee_type_id', 'amount', 'note'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    /**
     * Get any custom overrides for this student this year, keyed by fee_type_id.
     */
    public function overridesForStudentAndYear(int $studentId, int $academicYearId): array
    {
        $rows = $this->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->findAll();

        $keyed = [];
        foreach ($rows as $row) {
            $keyed[$row['fee_type_id']] = $row;
        }

        return $keyed;
    }

    public function upsert(int $studentId, int $academicYearId, int $feeTypeId, float $amount, ?string $note): void
    {
        $existing = $this->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('fee_type_id', $feeTypeId)
            ->first();

        $data = [
            'student_id'       => $studentId,
            'academic_year_id' => $academicYearId,
            'fee_type_id'      => $feeTypeId,
            'amount'           => $amount,
            'note'             => $note,
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }
}
