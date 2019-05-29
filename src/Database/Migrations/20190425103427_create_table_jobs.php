<?php namespace Tatter\Workflows\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_jobs extends Migration
{
	public function up()
	{
		$fields = [
			'name'           => ['type' => 'varchar', 'constraint' => 31],
			'summary'        => ['type' => 'varchar', 'constraint' => 255],
			'workflow_id'    => ['type' => 'int', 'unsigned' => true],
			'stage_id'       => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'deleted'        => ['type' => 'boolean', 'default' => 0],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('name');
		$this->forge->addKey('stage_id');

		$this->forge->createTable('jobs');
	
		// add changelog
		$fields = [
			'job_id'         => ['type' => 'int', 'unsigned' => true],
			'stage_from'     => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'stage_to'       => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'created_by'     => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'created_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey(['job_id', 'stage_from', 'stage_to']);
		$this->forge->addKey(['job_id', 'stage_to']);
		
		$this->forge->createTable('joblogs');
	}

	public function down()
	{
		$this->forge->dropTable('jobs');
		$this->forge->dropTable('joblogs');
	}
}
