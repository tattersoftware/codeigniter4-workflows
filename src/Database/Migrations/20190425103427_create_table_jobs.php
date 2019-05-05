<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_jobs extends Migration
{
	public function up()
	{
		$fields = [
			'name'           => ['type' => 'varchar', 'constraint' => 31],
			'source'         => ['type' => 'varchar', 'constraint' => 31],
			'workflow_id'    => ['type' => 'int', 'unsigned' => true],
			'task_id'        => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'summary'        => ['type' => 'varchar', 'constraint' => 255],
			'description'    => ['type' => 'text'],
			'deleted'        => ['type' => 'boolean', 'default' => 0],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('name');
		$this->forge->addKey(['workflow_id', 'task_id']);
		$this->forge->addKey(['task_id', 'workflow_id']);

		$this->forge->createTable('jobs');
	
		// add changelog
		$fields = [
			'job_id'         => ['type' => 'int', 'unsigned' => true],
			'task_from'      => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'task_to'        => ['type' => 'int', 'unsigned' => true],
			'created_by'     => ['type' => 'int', 'unsigned' => true],
			'created_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey(['job_id', 'task_from']);
		$this->forge->addKey(['job_id', 'task_to']);
		$this->forge->addKey(['task_to', 'job_id']);
		$this->forge->addKey(['task_from', 'job_id']);
		
		$this->forge->createTable('joblogs');
	}

	public function down()
	{
		$this->forge->dropTable('jobs');
		$this->forge->dropTable('joblogs');
	}
}
