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

class CreateJobflags extends Migration
{
    public function up(): void
    {
        $fields = [
            'job_id'     => ['type' => 'int', 'unsigned' => true],
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

    public function down(): void
    {
        $this->forge->dropTable('jobflags');
    }
}
