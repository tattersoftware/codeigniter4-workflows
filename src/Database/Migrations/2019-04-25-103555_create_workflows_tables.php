<?php namespace Tatter\Workflows\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowsTables extends Migration
{
	public function up()
	{
		/* Workflows */
		$fields = [
			'name'           => ['type' => 'varchar', 'constraint' => 63],
			'category'       => ['type' => 'varchar', 'constraint' => 63],
			'icon'           => ['type' => 'varchar', 'constraint' => 63],
			'summary'        => ['type' => 'varchar', 'constraint' => 255],
			'description'    => ['type' => 'text'],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
			'deleted_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('name');
		$this->forge->addKey(['category', 'name']);
		$this->forge->addKey(['deleted_at', 'id']);
		$this->forge->addKey('created_at');
		
		$this->forge->createTable('workflows');

		/* Actions */
		$fields = [
			'category'       => ['type' => 'varchar', 'constraint' => 63],
			'name'           => ['type' => 'varchar', 'constraint' => 63],
			'uid'            => ['type' => 'varchar', 'constraint' => 63],
			'class'          => ['type' => 'varchar', 'constraint' => 63],
			'input'          => ['type' => 'varchar', 'constraint' => 63],
			'role'           => ['type' => 'varchar', 'constraint' => 63],
			'icon'           => ['type' => 'varchar', 'constraint' => 63],
			'summary'        => ['type' => 'varchar', 'constraint' => 255],
			'description'    => ['type' => 'text'],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
			'deleted_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('name');
		$this->forge->addKey('uid');
		$this->forge->addKey(['category', 'name']);
		$this->forge->addKey(['deleted_at', 'id']);
		$this->forge->addKey('created_at');
		
		$this->forge->createTable('actions');

		/* Stages */
		$fields = [
			'action_id'        => ['type' => 'int', 'unsigned' => true],
			'workflow_id'    => ['type' => 'int', 'unsigned' => true],
			'input'          => ['type' => 'varchar', 'constraint' => 63],
			'required'       => ['type' => 'boolean', 'default' => 1],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey(['action_id', 'workflow_id']);
		$this->forge->addKey(['workflow_id', 'action_id']);
		
		$this->forge->createTable('stages');

		/* Jobs */
		$fields = [
			'name'           => ['type' => 'varchar', 'constraint' => 255],
			'summary'        => ['type' => 'varchar', 'constraint' => 255],
			'workflow_id'    => ['type' => 'int', 'unsigned' => true],
			'stage_id'       => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
			'deleted_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('name');
		$this->forge->addKey('stage_id');

		$this->forge->createTable('jobs');
	
		// Job change log
		$fields = [
			'job_id'         => ['type' => 'int', 'unsigned' => true],
			'stage_from'     => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'stage_to'       => ['type' => 'int', 'unsigned' => true, 'null' => true],
			'user_id'        => ['type' => 'int', 'unsigned' => true, 'null' => true],
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
		$this->forge->dropTable('workflows');
		$this->forge->dropTable('actions');
		$this->forge->dropTable('stages');
		$this->forge->dropTable('jobs');
		$this->forge->dropTable('joblogs');
	}
}
