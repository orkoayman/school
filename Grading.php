<?php

namespace App\Libraries;

/**
 * Implements the standard Bangladesh SSC/JSC grading scale.
 * Percentage is calculated against each subject's own full_marks,
 * since full marks can differ per subject.
 */
class Grading
{
    /**
     * Returns ['grade' => string, 'point' => float] for a single subject.
     * If marks are below pass_marks, grade is always F / 0.00 regardless of percentage.
     */
    public static function gradeForSubject(float $marksObtained, int $fullMarks, int $passMarks): array
    {
        if ($marksObtained < $passMarks) {
            return ['grade' => 'F', 'point' => 0.00];
        }

        $percentage = ($marksObtained / $fullMarks) * 100;

        return match (true) {
            $percentage >= 80 => ['grade' => 'A+', 'point' => 5.00],
            $percentage >= 70 => ['grade' => 'A', 'point' => 4.00],
            $percentage >= 60 => ['grade' => 'A-', 'point' => 3.50],
            $percentage >= 50 => ['grade' => 'B', 'point' => 3.00],
            $percentage >= 40 => ['grade' => 'C', 'point' => 2.00],
            default            => ['grade' => 'D', 'point' => 1.00],
        };
    }

    /**
     * Calculate overall GPA for an exam from a list of subject results.
     * $results: array of ['marks_obtained' => float, 'full_marks' => int, 'pass_marks' => int]
     *
     * Bangladesh rule: failing ANY subject fails the whole result (GPA 0.00),
     * unless $manualOverride is true (admin force-pass).
     *
     * Returns ['gpa' => float, 'is_failed' => bool, 'total_marks' => float]
     */
    public static function calculateOverall(array $results, bool $manualOverride = false): array
    {
        $totalPoints = 0.0;
        $totalMarks  = 0.0;
        $anyFailed   = false;
        $count       = count($results);

        foreach ($results as $r) {
            if ($r['marks_obtained'] === null) {
                continue; // not entered yet, skip from calculation
            }

            $g = self::gradeForSubject((float) $r['marks_obtained'], (int) $r['full_marks'], (int) $r['pass_marks']);
            $totalPoints += $g['point'];
            $totalMarks  += (float) $r['marks_obtained'];

            if ($g['grade'] === 'F') {
                $anyFailed = true;
            }
        }

        if ($count === 0) {
            return ['gpa' => 0.00, 'is_failed' => false, 'total_marks' => 0.00];
        }

        $isFailed = $anyFailed && ! $manualOverride;
        $gpa      = $isFailed ? 0.00 : round($totalPoints / $count, 2);

        return [
            'gpa'         => $gpa,
            'is_failed'   => $anyFailed, // true failure state, kept even if overridden, for record-keeping
            'total_marks' => $totalMarks,
        ];
    }
}
