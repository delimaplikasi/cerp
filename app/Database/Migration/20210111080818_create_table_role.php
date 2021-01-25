<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableRole extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'role', 'cerp');
        $table = Table::addParent($table);
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addCode($table);
        $table = Table::addName($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'role', 'cerp');
        $table = Table::addParent($table);
        $table = Table::addStatus($table, false);
        $table = Table::addCode($table, false);
        $table = Table::addName($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();

        Table::createTreeView($this, 'role', 'cerp', [
            'id', 'parent_id', 'status_id', 'code', 'name', 'description', 'note', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', '1 AS level', 'TO_JSON(id)::TEXT AS id_path', 'TO_JSON(code)::TEXT AS code_path', 'TO_JSON(name)::TEXT AS name_path'
        ], [
            'j.id', 'j.parent_id', 'j.status_id', 'j.code', 'j.name', 'j.description', 'j.note', 'j.created_at', 'j.updated_at', 'j.deleted_at', 'j.created_by', 'j.updated_by', 'j.deleted_by', 'level + 1', "CONCAT(t.id_path, ', ', TO_JSON(j.id)::TEXT)", "CONCAT(t.code_path, ', ', TO_JSON(j.code)::TEXT)", "CONCAT(t.name_path, ', ', TO_JSON(j.name)::TEXT)"
        ], [
            'id', 'parent_id', 'status_id', 'code', 'name', 'description', 'note', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'level', "CAST('[' || id_path || ']' as JSON) AS id_path", "CAST('[' || code_path || ']' as JSON) AS code_path", "CAST('[' || name_path || ']' as JSON) AS name_path"
        ]);
    }

    public function down()
    {
        Table::dropTreeView($this, 'role', 'cerp');
        Table::dropHistory($this, 'role', 'cerp');
        Table::drop($this, 'role', 'cerp');
    }
}
