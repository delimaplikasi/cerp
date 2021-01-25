<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableUserPermission extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'user_permission', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addForeign($table, 'user', 'cerp');
        $table = Table::addForeign($table, 'permission', 'cerp');
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'user_permission', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addForeign($table, 'user', false);
        $table = Table::addForeign($table, 'permission', false);
        $table = Table::addNote($table);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'user_permission', 'cerp');
        Table::drop($this, 'user_permission', 'cerp');
    }
}
