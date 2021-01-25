<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use App\Model\Status;
use App\Singleton\Database;
use Phinx\Migration\AbstractMigration;

final class SeedDefaultStatus extends AbstractMigration
{
    public function up()
    {
        $model = new Status(Database::connect());

        $model->insert([
            'code' => 'undefined',
            'name' => 'Undefined'
        ]);

        $model->insert([
            'code' => 'inactive',
            'name' => 'Inactive'
        ]);

        $model->insert([
            'code' => 'active',
            'name' => 'Active'
        ]);
    }

    public function down()
    {
        $model = new Status(Database::connect());

        foreach ($model->addCondition('code', 'in', [
            'siteName'
        ])->action('select') as $p) {
            $model->delete($p['id']);
        }
    }
}
