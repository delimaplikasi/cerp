<?php
declare(strict_types=1);

use App\Helper\Database\Table;
use App\Model\Permission;
use App\Singleton\Database;
use Phinx\Migration\AbstractMigration;

final class SeedDefaultPermission extends AbstractMigration
{
    public function up()
    {
        $model = new Permission(Database::connect());

        $model->insert([
            'code' => 'login::action',
            'name' => 'Login'
        ]);
    }

    public function down()
    {
        $model = new Permission(Database::connect());

        foreach ($model->addCondition('code', 'in', [
            'login::action'
        ])->action('select') as $p) {
            $model->delete($p['id']);
        }
    }
}
