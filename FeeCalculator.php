<?php

namespace App\Libraries;

use App\Models\ClassFeeModel;
use App\Models\PaymentModel;
use App\Models\StudentFeeModel;

/**
 * Calculates how much a student owes, per fee type, for an academic year.
 * Monthly fees: rate x number of months elapsed so far (Jan-Dec, or school's session months).
 * One-time fees: flat rate.
 * Student-specific overrides (student_fees) take priority over class defaults (class_fees).
 */
class FeeCalculator
{
    protected ClassFeeModel $classFeeModel;
    protected StudentFeeModel $studentFeeModel;
    protected PaymentModel $paymentModel;

    public function __construct()
    {
        $this->classFeeModel   = model(ClassFeeModel::class);
        $this->studentFeeModel = model(StudentFeeModel::class);
        $this->paymentModel    = model(PaymentModel::class);
    }

    /**
     * Returns an array of due breakdown per fee type:
     * [ ['fee_type_id'=>, 'fee_type_name'=>, 'frequency'=>, 'rate'=>, 'total_due'=>, 'total_paid'=>, 'balance'=>], ... ]
     * plus a 'grand_total_balance' summary.
     *
     * $monthsElapsed: how many months of the session have passed (for monthly fees).
     * Defaults to current calendar month if not given (simple approach for BD school year = Jan-Dec).
     */
    public function calculateForStudent(int $studentId, int $classId, int $academicYearId, ?int $monthsElapsed = null): array
    {
        $monthsElapsed ??= (int) date('n'); // current month number, 1-12

        $classFees   = $this->classFeeModel->forClassAndYear($classId, $academicYearId);
        $overrides   = $this->studentFeeModel->overridesForStudentAndYear($studentId, $academicYearId);

        $breakdown  = [];
        $grandTotal = 0.0;

        foreach ($classFees as $fee) {
            $feeTypeId = (int) $fee['fee_type_id'];
            $rate      = isset($overrides[$feeTypeId]) ? (float) $overrides[$feeTypeId]['amount'] : (float) $fee['amount'];

            $totalDue = $fee['frequency'] === 'monthly'
                ? $rate * $monthsElapsed
                : $rate;

            $totalPaid = $this->paymentModel->totalPaid($studentId, $academicYearId, $feeTypeId);
            $balance   = $totalDue - $totalPaid;

            $breakdown[] = [
                'fee_type_id'   => $feeTypeId,
                'fee_type_name' => $fee['fee_type_name'],
                'frequency'     => $fee['frequency'],
                'rate'          => $rate,
                'total_due'     => $totalDue,
                'total_paid'    => $totalPaid,
                'balance'       => $balance,
            ];

            $grandTotal += $balance;
        }

        return [
            'items'                => $breakdown,
            'grand_total_balance'  => $grandTotal,
        ];
    }
}
