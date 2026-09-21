<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendances';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'student_id', 'date', 'status', 'in_time', 'out_time', 'source', 'marked_by', 'sms_sent',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    public function forStudentAndDate(int $studentId, string $date)
    {
        return $this->where('student_id', $studentId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Mark attendance for a student on a date (manual entry by teacher).
     * Upserts - safe to call again if teacher corrects a mistake same day.
     */
    public function markManual(int $studentId, string $date, string $status, int $teacherId): array
    {
        $existing = $this->forStudentAndDate($studentId, $date);

        $data = [
            'student_id' => $studentId,
            'date'       => $date,
            'status'     => $status,
            'source'     => 'manual',
            'marked_by'  => $teacherId,
        ];

        if ($status === 'present' && empty($existing['in_time'])) {
            $data['in_time'] = date('Y-m-d H:i:s');
        }

        if ($existing) {
            $this->update($existing['id'], $data);

            return $this->find($existing['id']);
        }

        $id = $this->insert($data, true);

        return $this->find($id);
    }

    /**
     * Record a card punch (future hardware integration).
     * First punch of the day = in_time, second punch = out_time.
     */
    public function recordCardPunch(int $studentId, string $date): array
    {
        $existing = $this->forStudentAndDate($studentId, $date);
        $now      = date('Y-m-d H:i:s');

        if (! $existing) {
            $id = $this->insert([
                'student_id' => $studentId,
                'date'       => $date,
                'status'     => 'present',
                'in_time'    => $now,
                'source'     => 'card_punch',
            ], true);

            return $this->find($id);
        }

        if (empty($existing['out_time'])) {
            $this->update($existing['id'], ['out_time' => $now]);
        }

        return $this->find($existing['id']);
    }

    public function rosterForDate(int $classId, int $academicYearId, string $date)
    {
        $escapedDate = $this->db->escape($date); // safely quoted literal for the raw join condition

        return $this->db->table('enrollments e')
            ->select('e.student_id, s.name, s.student_code, s.card_number, s.parent_phone, a.status, a.in_time, a.out_time, a.sms_sent')
            ->join('students s', 's.id = e.student_id')
            ->join('attendances a', "a.student_id = e.student_id AND a.date = {$escapedDate}", 'left')
            ->where('e.class_id', $classId)
            ->where('e.academic_year_id', $academicYearId)
            ->orderBy('e.roll_in_class', 'ASC')
            ->get()
            ->getResultArray();
    }
}
