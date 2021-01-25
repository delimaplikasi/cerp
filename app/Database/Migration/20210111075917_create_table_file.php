<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableFile extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'file', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addCode($table);
        $table = Table::addName($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->addColumn('location', 'text', [
            'null' => true,
        ]);
        $table->addColumn('property', 'json', [
            'null' => true,
        ]);
        $table->create();

        $table = Table::prepareHistory($this, 'file', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addCode($table, false);
        $table = Table::addName($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->addColumn('location', 'text', [
            'null' => true,
        ]);
        $table->addColumn('property', 'json', [
            'null' => true,
        ]);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'file', 'cerp');
        Table::drop($this, 'file', 'cerp');
    }
}
