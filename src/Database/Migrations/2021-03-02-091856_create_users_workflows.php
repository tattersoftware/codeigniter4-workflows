<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowsUsers extends Migration
{
	public function up()
	{
		$fields = [
			'user_id'     => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'workflow_id' => ['type' => 'varchar', 'constraint' => 255],
			'permitted'   => ['type' => 'bool', 'default' => 1],
			'created_at'  => ['type' => 'datetime', 'null' => true],
		];

		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey(['user_id', 'workflow_id']);
		$this->forge->addKey(['workflow_id', 'user_id']);

		$this->forge->createTable('users_workflows');

		// Add the "role" column to workflows for broader restrictions
		$this->forge->addColumn('workflows', [
			'role' => ['type' => 'varchar', 'constraint' => 63],
		]);
	}

	public function down()
	{
		$this->forge->dropTable('users_workflows');
		$this->forge->dropColumn('workflows', 'role');
	}
}
