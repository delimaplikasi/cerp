<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTablePreference extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'preference', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addCode($table);
        $table = Table::addName($table);
        $table->addColumn('value', 'string', [
            'limit' => 255,
            'null' => true,
        ]);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'preference', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addCode($table, false);
        $table = Table::addName($table);
        $table->addColumn('value', 'string', [
            'limit' => 255,
            'null' => true,
        ]);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'preference', 'cerp');
        Table::drop($this, 'preference', 'cerp');
    }
}
