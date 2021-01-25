<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableMenu extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'menu', 'cerp');
        $table = Table::addParent($table);
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addCode($table);
        $table = Table::addName($table);
        $table->addColumn('url', 'string', [
            'null' => true,
            'limit' => 255,
        ]);
        $table->addColumn('icon', 'string', [
            'null' => true,
        ]);
        $table = Table::addSequencePosition($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'menu', 'cerp');
        $table = Table::addParent($table);
        $table = Table::addStatus($table, false);
        $table = Table::addCode($table, false);
        $table = Table::addName($table);
        $table->addColumn('url', 'string', [
            'null' => true,
            'limit' => 255,
        ]);
        $table->addColumn('icon', 'string', [
            'null' => true,
        ]);
        $table = Table::addSequencePosition($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();

        Table::createTreeView($this, 'menu', 'cerp', [
            'id', 'parent_id', 'status_id', 'code', 'name', 'url', 'icon','sequence_position', 'description', 'note', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', '0 AS level', 'TO_JSON(id)::TEXT AS id_path', 'TO_JSON(code)::TEXT AS code_path', 'TO_JSON(name)::TEXT AS name_path', 'TO_JSON(url)::TEXT AS url_path', 'TO_JSON(sequence_position)::TEXT AS sequence_position_path'
        ], [
            'j.id', 'j.parent_id', 'j.status_id', 'j.code', 'j.name', 'j.url', 'j.icon', 'j.sequence_position', 'j.description', 'j.note', 'j.created_at', 'j.updated_at', 'j.deleted_at', 'j.created_by', 'j.updated_by', 'j.deleted_by', 'level + 1', "CONCAT(t.id_path, ', ', TO_JSON(j.id)::TEXT)", "CONCAT(t.code_path, ', ', TO_JSON(j.code)::TEXT)", "CONCAT(t.name_path, ', ', TO_JSON(j.name)::TEXT)", "CONCAT(t.url_path, ', ', TO_JSON(j.url)::TEXT)", "CONCAT(t.sequence_position_path, ', ', TO_JSON(j.sequence_position)::TEXT)"
        ], [
            'id', 'parent_id', 'status_id', 'code', 'name', 'url', 'icon', 'sequence_position', 'description', 'note', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'level', "CAST('[' || id_path || ']' as JSON) AS id_path", "CAST('[' || code_path || ']' as JSON) AS code_path", "CAST('[' || name_path || ']' as JSON) AS name_path", "CAST('[' || url_path || ']' as JSON) AS url_path", "CAST('[' || sequence_position_path || ']' as JSON) AS sequence_position_path"
        ]);
    }

    public function down()
    {
        Table::dropTreeView($this, 'menu', 'cerp');
        Table::dropHistory($this, 'menu', 'cerp');
        Table::drop($this, 'menu', 'cerp');
    }
}
