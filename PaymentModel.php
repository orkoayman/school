<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'student_id', 'academic_year_id', 'fee_type_id', 'for_month',
        'amount', 'paid_date', 'note', 'received_by',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'student_id' => 'required|is_natural_no_zero',
        'fee_type_id' => 'required|is_natural_no_zero',
        'amount'     => 'required|numeric|greater_than[0]',
        'paid_date'  => 'required|valid_date',
    ];

    public function historyForStudent(int $studentId, int $academicYearId)
    {
        return $this->select('payments.*, fee_types.name as fee_type_name')
            ->join('fee_types', 'fee_types.id = payments.fee_type_id')
            ->where('payments.student_id', $studentId)
            ->where('payments.academic_year_id', $academicYearId)
            ->orderBy('payments.paid_date', 'DESC')
            ->findAll();
    }

    /**
     * Total paid by a student for a specific fee type this year.
     */
    public function totalPaid(int $studentId, int $academicYearId, int $feeTypeId): float
    {
        $result = $this->selectSum('amount')
            ->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('fee_type_id', $feeTypeId)
            ->first();

        return (float) ($result['amount'] ?? 0);
    }
}
