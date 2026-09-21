<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeeTypesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100, // Tuition, Admission, Exam Fee, Session Fee...
            ],
            'frequency' => [
                'type'       => 'ENUM',
                'constraint' => ['monthly', 'one_time'],
                'default'    => 'monthly',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('fee_types');
    }

    public function down()
    {
        $this->forge->dropTable('fee_types');
    }
}
