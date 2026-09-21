<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['present', 'absent'],
                'default'    => 'absent',
            ],
            'in_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'set manually now; set by card punch device later',
            ],
            'out_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'set by card punch device when hardware is connected',
            ],
            'source' => [
                'type'       => 'ENUM',
                'constraint' => ['manual', 'card_punch'],
                'default'    => 'manual',
            ],
            'marked_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // teacher user id, null if from card punch
            ],
            'sms_sent' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['student_id', 'date'], false, true); // one attendance record per student per day
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('marked_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('attendances');
    }

    public function down()
    {
        $this->forge->dropTable('attendances');
    }
}
