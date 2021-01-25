<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableMenuPermission extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'menu_permission', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addForeign($table, 'menu', 'cerp');
        $table = Table::addForeign($table, 'permission', 'cerp');
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'menu_permission', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addForeign($table, 'menu', false);
        $table = Table::addForeign($table, 'permission', false);
        $table = Table::addNote($table);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'menu_permission', 'cerp');
        Table::drop($this, 'menu_permission', 'cerp');
    }
}
