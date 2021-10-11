<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EmployeesMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'varchar',
                'constraint' => 50,
                'null' => false,

            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 50,
                'null' => false,
                'unique' => true,
            ],
            'profile_image' => [
                'type' => 'varchar',
                'constraint' => 220,
                'null' => true,

            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('employees');
    }

    public function down()
    {
        $this->forge->dropTable('employees');
    }
}
