<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassFeeModel extends Model
{
    protected $table            = 'class_fees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['class_id', 'academic_year_id', 'fee_type_id', 'amount'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    public function forClassAndYear(int $classId, int $academicYearId)
    {
        return $this->select('class_fees.*, fee_types.name as fee_type_name, fee_types.frequency')
            ->join('fee_types', 'fee_types.id = class_fees.fee_type_id')
            ->where('class_fees.class_id', $classId)
            ->where('class_fees.academic_year_id', $academicYearId)
            ->findAll();
    }

    public function upsert(int $classId, int $academicYearId, int $feeTypeId, float $amount): void
    {
        $existing = $this->where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->where('fee_type_id', $feeTypeId)
            ->first();

        $data = ['class_id' => $classId, 'academic_year_id' => $academicYearId, 'fee_type_id' => $feeTypeId, 'amount' => $amount];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }
}
