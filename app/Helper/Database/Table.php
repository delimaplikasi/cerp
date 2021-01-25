<?php namespace App\Helper\Database;

use Phinx\Db\Table as DbTable;
use Phinx\Migration\AbstractMigration;

class Table
{
    public static function addForeign(DbTable $table, $name, $schema = null)
    {
        $table->addColumn("{$name}_id", 'biginteger', [
            'null' => null,
            'default' => null,
        ]);

        if ($schema !== false) {
            $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_";

            $table->addForeignKey("{$name}_id", "{$schema}{$name}", 'id', [
                'update' => 'CASCADE',
                'delete' => 'RESTRICT',
            ]);
        }

        return $table;
    }

    public static function prepare(AbstractMigration $class, $name = '', $schema = null): DbTable
    {
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_";
        $table = $class->table("{$schema}{$name}", [
            'id' => false,
            'primary_key' => 'id',
        ]);

        $table
            ->addColumn('id', 'biginteger', [
                'identity' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'null' => true
            ])
            ->addColumn('updated_at', 'timestamp', [
                'null' => true
            ])
            ->addColumn('deleted_at', 'timestamp', [
                'null' => true
            ])
            ->addColumn('created_by', 'biginteger', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('updated_by', 'biginteger', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('deleted_by', 'biginteger', [
                'null' => true,
                'default' => null,
            ]);

        return $table;
    }

    public static function prepareHistory(AbstractMigration $class, $name = '', $schema = null): DbTable
    {
        $oriSchema = $schema;
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_history_";
        $table = $class->table("{$schema}{$name}", [
            'id' => false,
            'primary_key' => 'id',
        ]);

        $table
            ->addColumn('id', 'biginteger', [
                'identity' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'null' => true
            ])
            ->addColumn('updated_at', 'timestamp', [
                'null' => true
            ])
            ->addColumn('deleted_at', 'timestamp', [
                'null' => true
            ])
            ->addColumn('created_by', 'biginteger', [
                'null' => true,
                'default' => null
            ])
            ->addColumn('updated_by', 'biginteger', [
                'null' => true,
                'default' => null
            ])
            ->addColumn('deleted_by', 'biginteger', [
                'null' => true,
                'default' => null
            ]);

        $table = Table::addMaster($table, $name, $oriSchema);

        return $table;
    }

    public static function addMaster(DbTable $table, $name = '', $schema = null)
    {
        $table->addColumn('master_id', 'biginteger', [
            'null' => true,
            'default' => null,
        ]);

        if ($schema !== false) {
            $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_";

            $table->addForeignKey("master_id", "{$schema}{$name}", 'id', [
                'update' => 'CASCADE',
                'delete' => 'RESTRICT',
            ]);
        }

        return $table;
    }

    public static function addParent(DbTable $table)
    {
        $table->addColumn('parent_id', 'biginteger', [
            'null' => true,
            'default' => null,
        ]);

        return $table;
    }

    public static function addCode(DbTable $table, $unique = true)
    {
        $table->addColumn('code', 'string', [
            'limit' => 255,
            'null' => false
        ]);

        if ($unique) {
            $table->addIndex('code', [
                'unique' => true,
            ]);
        }

        return $table;
    }

    public static function addName(DbTable $table)
    {
        $table->addColumn('name', 'string', [
            'limit' => 255,
            'null' => false
        ]);

        return $table;
    }

    public static function addSequencePosition(DbTable $table)
    {
        $table->addColumn('sequence_position', 'string', [
            'limit' => 2,
            'null' => false,
            'default' => '99',
        ]);

        return $table;
    }

    public static function addDescription(DbTable $table)
    {
        $table->addColumn('description', 'text', [
            'null' => true,
        ]);

        return $table;
    }

    public static function addNote(DbTable $table)
    {
        $table->addColumn('note', 'text', [
            'null' => true,
        ]);

        return $table;
    }

    public static function drop(AbstractMigration $class, $name = '', $schema = null)
    {
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_";
        $class->table("{$schema}{$name}")->drop()->save();
    }

    public static function dropHistory(AbstractMigration $class, $name = '', $schema = null)
    {
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_history_";
        $class->table("{$schema}{$name}")->drop()->save();
    }

    public static function addStatus(DbTable $table, $schema = null)
    {
        
        $table->addColumn('status_id', 'biginteger', [
            'null' => null,
            'default' => null,
        ]);

        if ($schema !== false) {
            $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_";

            $table->addForeignKey('status_id', "{$schema}status", 'id', [
                'update' => 'CASCADE',
                'delete' => 'RESTRICT',
            ]);
        }

        return $table;
    }

    public static function createTreeView(AbstractMigration $class, $name, $schema = null, $column1, $column2, $select)
    {
        $column1 = implode(', ', $column1);
        $column2 = implode(', ', $column2);
        $select = implode(', ', $select);
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_";

        $definition = "CREATE VIEW {$schema}tree_view_{$name} AS WITH RECURSIVE x AS (
            SELECT {$column1}
            FROM {$schema}{$name}
            WHERE parent_id is null
            UNION ALL
            SELECT {$column2}
            FROM x AS t
            JOIN {$schema}{$name} AS j ON t.id = j.parent_id
        ) SELECT {$select} FROM x";

        $class->execute($definition);
    }

    public static function dropTreeView(AbstractMigration $class, $name, $schema = null)
    {
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_tree_view_";
        $class->execute("DROP VIEW IF EXISTS {$schema}{$name}");
    }

    public static function truncate(AbstractMigration $class, $name, $schema)
    {
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_";
        $class->table("{$schema}{$name}")->truncate();
    }

    public static function truncateHistory(AbstractMigration $class, $name, $schema)
    {
        $schema = is_null($schema) || empty($schema) ? '' : "{$schema}_history_";
        $class->table("{$schema}{$name}")->truncate();
    }
}
