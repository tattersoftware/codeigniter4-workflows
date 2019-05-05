<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_workflows extends Migration
{
	public function up()
	{
		$fields = [
			'name'           => ['type' => 'varchar', 'constraint' => 31],
			'category'       => ['type' => 'varchar', 'constraint' => 31],
			'icon'           => ['type' => 'varchar', 'constraint' => 31],
			'summary'        => ['type' => 'varchar', 'constraint' => 255],
			'description'    => ['type' => 'text'],
			'deleted'        => ['type' => 'boolean', 'default' => 0],
			'created_at'     => ['type' => 'datetime', 'null' => true],
			'updated_at'     => ['type' => 'datetime', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addKey('name');
		$this->forge->addKey(['category', 'name']);
		$this->forge->addKey(['deleted', 'id']);
		$this->forge->addKey('created_at');
		
		$this->forge->createTable('workflows');
	}

	public function down()
	{
		$this->forge->dropTable('workflows');
	}
}
