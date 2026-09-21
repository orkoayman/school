<?php

namespace App\Models;

use App\Libraries\Grading;
use CodeIgniter\Model;

class ExamSummaryModel extends Model
{
    protected $table            = 'exam_summaries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'enrollment_id', 'exam_id', 'total_marks_obtained', 'gpa',
        'is_failed', 'manual_pass_override', 'override_note', 'overridden_by',
    ];
    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    public function forEnrollmentAndExam(int $enrollmentId, int $examId)
    {
        return $this->where('enrollment_id', $enrollmentId)
            ->where('exam_id', $examId)
            ->first();
    }

    /**
     * Recalculate GPA/fail status for a student's exam from current results,
     * preserving any existing manual_pass_override flag unless explicitly changed.
     * Call this every time a mark is saved.
     */
    public function recalculate(int $enrollmentId, int $examId): array
    {
        $resultModel = model(ResultModel::class);
        $results     = $resultModel->forEnrollmentAndExam($enrollmentId, $examId);

        $existing        = $this->forEnrollmentAndExam($enrollmentId, $examId);
        $manualOverride  = $existing['manual_pass_override'] ?? 0;

        $calc = Grading::calculateOverall($results, (bool) $manualOverride);

        $data = [
            'enrollment_id'        => $enrollmentId,
            'exam_id'              => $examId,
            'total_marks_obtained' => $calc['total_marks'],
            'gpa'                  => $manualOverride ? max($calc['gpa'], $this->minPassGpa($results)) : $calc['gpa'],
            'is_failed'            => $calc['is_failed'],
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }

        return $data;
    }

    /**
     * When admin manually overrides a fail to pass, GPA is recalculated
     * without the fail-everything rule (so genuine grade points count).
     */
    private function minPassGpa(array $results): float
    {
        $calc = Grading::calculateOverall($results, true);

        return $calc['gpa'];
    }

    /**
     * Admin action: force-pass a failed exam result.
     */
    public function setManualOverride(int $enrollmentId, int $examId, bool $override, string $note, int $adminUserId): void
    {
        $existing = $this->forEnrollmentAndExam($enrollmentId, $examId);

        if (! $existing) {
            return;
        }

        $this->update($existing['id'], [
            'manual_pass_override' => $override ? 1 : 0,
            'override_note'        => $note,
            'overridden_by'        => $adminUserId,
        ]);

        // Recalculate GPA now that override state has changed
        $this->recalculate($enrollmentId, $examId);
    }
}
