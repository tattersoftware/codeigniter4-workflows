<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowsTables extends Migration
{
    use CreateActionsTrait;

    public function up(): void
    {
        // Workflows
        $fields = [
            'name'        => ['type' => 'varchar', 'constraint' => 63],
            'category'    => ['type' => 'varchar', 'constraint' => 63, 'default' => ''],
            'icon'        => ['type' => 'varchar', 'constraint' => 63, 'default' => ''],
            'summary'     => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'text', 'null' => true],
            'created_at'  => ['type' => 'datetime', 'null' => true],
            'updated_at'  => ['type' => 'datetime', 'null' => true],
            'deleted_at'  => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey('name');
        $this->forge->addKey(['category', 'name']);
        $this->forge->addKey(['deleted_at', 'id']);
        $this->forge->addKey('created_at');

        $this->forge->createTable('workflows');

        // Actions, see CreateActionsTrait
        $this->createActions();

        // Stages
        $fields = [
            'action_id'   => ['type' => 'int', 'unsigned' => true],
            'workflow_id' => ['type' => 'int', 'unsigned' => true],
            'input'       => ['type' => 'varchar', 'constraint' => 63, 'null' => true],
            'required'    => ['type' => 'boolean', 'default' => 1],
            'created_at'  => ['type' => 'datetime', 'null' => true],
            'updated_at'  => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey(['action_id', 'workflow_id']);
        $this->forge->addKey(['workflow_id', 'action_id']);

        $this->forge->createTable('stages');

        // Jobs
        $fields = [
            'name'        => ['type' => 'varchar', 'constraint' => 255],
            'summary'     => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'workflow_id' => ['type' => 'int', 'unsigned' => true],
            'stage_id'    => ['type' => 'int', 'unsigned' => true, 'null' => true],
            'created_at'  => ['type' => 'datetime', 'null' => true],
            'updated_at'  => ['type' => 'datetime', 'null' => true],
            'deleted_at'  => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey('name');
        $this->forge->addKey('stage_id');

        $this->forge->createTable('jobs');

        // Job change log
        $fields = [
            'job_id'     => ['type' => 'int', 'unsigned' => true],
            'stage_from' => ['type' => 'int', 'unsigned' => true, 'null' => true],
            'stage_to'   => ['type' => 'int', 'unsigned' => true, 'null' => true],
            'user_id'    => ['type' => 'int', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey(['job_id', 'stage_from', 'stage_to']);
        $this->forge->addKey(['job_id', 'stage_to']);

        $this->forge->createTable('joblogs');
    }

    public function down(): void
    {
        $this->forge->dropTable('joblogs');
        $this->forge->dropTable('jobs');
        $this->forge->dropTable('stages');
        $this->forge->dropTable('actions');
        $this->forge->dropTable('workflows');
    }
}
