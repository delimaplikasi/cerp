<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableUser extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'user', 'cerp');
        $table = Table::addStatus($table, 'cerp');
        $table = Table::addCode($table);
        $table = Table::addName($table);
        $table->addColumn('email', 'string', [
            'null' => false,
            'limit' => 255
        ]);
        $table->addColumn('password', 'string', [
            'null' => false,
            'limit' => 255
        ]);
        $table->addIndex('email', [
            'unique' => true,
        ]);
        $table = Table::addNote($table);
        $table->create();

        $table = Table::prepareHistory($this, 'user', 'cerp');
        $table = Table::addStatus($table, false);
        $table = Table::addCode($table, false);
        $table = Table::addName($table);
        $table->addColumn('email', 'string', [
            'null' => false,
            'limit' => 255
        ]);
        $table->addColumn('password', 'string', [
            'null' => false,
            'limit' => 255
        ]);
        $table = Table::addNote($table);
        $table->create();
    }

    public function down()
    {
        Table::dropHistory($this, 'user', 'cerp');
        Table::drop($this, 'user', 'cerp');
    }
}
