<?php

namespace App\Models;

use CodeIgniter\Model;

class ResultModel extends Model
{
    protected $table            = 'results';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['enrollment_id', 'exam_id', 'subject_id', 'marks_obtained', 'grade', 'grade_point'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Get all results for one enrollment+exam, joined with subject info
     * (needed for GPA calculation and result card display).
     */
    public function forEnrollmentAndExam(int $enrollmentId, int $examId)
    {
        return $this->select('results.*, subjects.name as subject_name, subjects.full_marks, subjects.pass_marks')
            ->join('subjects', 'subjects.id = results.subject_id')
            ->where('results.enrollment_id', $enrollmentId)
            ->where('results.exam_id', $examId)
            ->findAll();
    }

    /**
     * Insert or update a single subject's mark (upsert by enrollment+exam+subject).
     */
    public function saveMark(int $enrollmentId, int $examId, int $subjectId, ?float $marks, string $grade = null, float $gradePoint = null): void
    {
        $existing = $this->where('enrollment_id', $enrollmentId)
            ->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->first();

        $data = [
            'enrollment_id'  => $enrollmentId,
            'exam_id'        => $examId,
            'subject_id'     => $subjectId,
            'marks_obtained' => $marks,
            'grade'          => $grade,
            'grade_point'    => $gradePoint,
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }
}
