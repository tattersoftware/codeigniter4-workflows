<?php namespace Tatter\Workflows\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_stages extends Migration
{
	public function up()
	{
		$fields = [
			'task_id'        => ['type' => 'int', 'unsigned' => true],
			'workflow_id'    => ['type' => 'int', 'unsigned' => true],
			'required'       => ['type' => 'boolean', 'default' => 0],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey(['task_id', 'workflow_id']);
		$this->forge->addKey(['workflow_id', 'task_id']);
		
		$this->forge->createTable('stages');
	}

	public function down()
	{
		$this->forge->dropTable('stages');
	}
}
