<?php

namespace Tatter\Workflows\Database\Migrations;

trait CreateActionsTrait
{
    private function createActions(): void
    {
        // Actions
        $fields = [
            'category'    => ['type' => 'varchar', 'constraint' => 63],
            'name'        => ['type' => 'varchar', 'constraint' => 63],
            'uid'         => ['type' => 'varchar', 'constraint' => 63],
            'class'       => ['type' => 'varchar', 'constraint' => 63, 'null' => true],
            'input'       => ['type' => 'varchar', 'constraint' => 63, 'null' => true],
            'role'        => ['type' => 'varchar', 'constraint' => 63, 'default' => ''],
            'icon'        => ['type' => 'varchar', 'constraint' => 63, 'default' => ''],
            'summary'     => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'description' => ['type' => 'text', 'null' => true],
            'created_at'  => ['type' => 'datetime', 'null' => true],
            'updated_at'  => ['type' => 'datetime', 'null' => true],
            'deleted_at'  => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey('name');
        $this->forge->addKey('uid');
        $this->forge->addKey(['category', 'name']);
        $this->forge->addKey(['deleted_at', 'id']);
        $this->forge->addKey('created_at');

        $this->forge->createTable('actions');
    }
}
