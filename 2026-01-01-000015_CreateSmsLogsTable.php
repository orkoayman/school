<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmsLogsTable extends Migration
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
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['attendance', 'fee_reminder', 'other'],
                'default'    => 'attendance',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['sent', 'failed'],
                'default'    => 'sent',
            ],
            'response' => [
                'type' => 'TEXT',
                'null' => true, // raw gateway response for debugging
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('student_id', 'students', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('sms_logs');
    }

    public function down()
    {
        $this->forge->dropTable('sms_logs');
    }
}
