<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use Phinx\Migration\AbstractMigration;

final class CreateTableActivity extends AbstractMigration
{
    public function up()
    {
        $table = Table::prepare($this, 'activity', 'cerp');
        $table = Table::addCode($table);
        $table = Table::addName($table);
        $table = Table::addDescription($table);
        $table = Table::addNote($table);
        $table->addColumn('data', 'json', [
            'null' => true
        ]);
        $table->create();
    }

    public function down()
    {
        Table::drop($this, 'activity', 'cerp');
    }
}
