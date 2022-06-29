<?php

namespace Tatter\Workflows\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Starting in v3 of Tatter\Handlers the class attributes
 * are all available from class constants so need not be
 * stored in the database.
 * This migration removes the now-duplicate database info
 * and converts Stages to use the new handler ID (string).
 */
class DropActions extends Migration
{
    use CreateActionsTrait;

    public function up(): void
    {
        // Grab Action IDs to match them when updating Stages
        $actions = [];
        foreach ($this->db->table('actions')->get()->getResultArray() as $row) {
            $actions[$row['id']] = $row['uid'];
        }

        $this->forge->dropTable('actions');

        // Update Stages to use the string handler ID
        $this->forge->modifyColumn('stages', [
            'action_id' => ['type' => 'varchar', 'constraint' => 63],
        ]);
        
        foreach ($actions as $id => $handlerId) {
            $this->db->table('stages')
                ->where('action_id', $id)
                ->update(['action_id' => $handlerId]);
        }
    }

    public function down(): void
    {
        $this->createActions();
    }
}
