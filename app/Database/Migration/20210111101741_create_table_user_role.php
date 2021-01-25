<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableUserRole extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'user_role', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addForeign($table, 'user', 'cerp');
        $table = Table::addForeign($table, 'role', 'cerp');
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'user_role', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addForeign($table, 'user', false);
        $table = Table::addForeign($table, 'role', false);
        $table = Table::addNote($table);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'user_role', 'cerp');
        Table::drop($this, 'user_role', 'cerp');
    }
}
