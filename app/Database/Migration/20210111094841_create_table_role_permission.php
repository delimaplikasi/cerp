<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableRolePermission extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'role_permission', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addForeign($table, 'role', 'cerp');
        $table = Table::addForeign($table, 'permission', 'cerp');
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'role_permission', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addForeign($table, 'role', false);
        $table = Table::addForeign($table, 'permission', false);
        $table = Table::addNote($table);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'role_permission', 'cerp');
        Table::drop($this, 'role_permission', 'cerp');
    }
}
