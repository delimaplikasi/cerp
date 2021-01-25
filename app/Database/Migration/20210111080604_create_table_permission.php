<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTablePermission extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'permission', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addCode($table);
        $table = Table::addName($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'permission', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addCode($table, false);
        $table = Table::addName($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'permission', 'cerp');
        Table::drop($this, 'permission', 'cerp');
    }
}
