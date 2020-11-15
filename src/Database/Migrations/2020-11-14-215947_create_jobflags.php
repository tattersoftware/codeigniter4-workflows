<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobflags extends Migration
{
	public function up()
	{
		$fields = [
			'job_id'     => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'name'       => ['type' => 'varchar', 'constraint' => 255],
			'created_at' => ['type' => 'datetime', 'null' => true],
		];

		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey(['job_id', 'name']);
		$this->forge->addKey(['name', 'job_id']);
		$this->forge->addKey(['job_id', 'created_at']);

		$this->forge->createTable('jobflags');
	}

	public function down()
	{
		$this->forge->dropTable('jobflags');
	}
}
