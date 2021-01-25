<?php
declare(strict_types=1);

use App\Model\Role;
use App\Singleton\Database;
use Phinx\Migration\AbstractMigration;

final class SeedDefaultRole extends AbstractMigration
{
    public function up()
    {
        $model = new Role(Database::connect());

        $systemID = $model->insert([
            'code' => 'system',
            'name' => 'System',
        ]);

        $developerID = $model->insert([
            'code' => 'developer',
            'name' => 'Developer',
            'parent_id' => $systemID,
            'permissions_to_add' => [
                'login::action',
                'dashboard',
                'dashboardMain',
                'dashboardSetting',
                'dashboardSettingGeneral',
                'dashboardSettingUAC',
                'dashboardSettingUACPermission',
                'dashboardSettingUACRole',
                'dashboardSettingUACUser',
                'dashboardSettingDatabase',
                'dashboardSettingDatabasePanel',
                'dashboardSettingDatabaseMigration',
            ],
        ]);

        $administratorID = $model->insert([
            'code' => 'administrator',
            'name' => 'Administrator',
            'parent_id' => $systemID,
            'permissions_to_add' => [
                'login::action',
                'dashboard',
                'dashboardMain',
                'dashboardSetting',
                'dashboardSettingGeneral',
                'dashboardSettingUAC',
                'dashboardSettingUACPermission',
                'dashboardSettingUACRole',
                'dashboardSettingUACUser',
            ],
        ]);

        $userID = $model->insert([
            'code' => 'user',
            'name' => 'User',
            'parent_id' => $systemID,
            'permissions_to_add' => [
                'login::action',
                'dashboard::browse',
            ]
        ]);
    }

    public function down()
    {
        $model = new Role(Database::connect());

        foreach ($model->addCondition('code', 'in', [
            'system', 'developer', 'administrator', 'user'
        ])->action('select') as $item) {
            $model->delete($item['id']);
        }
    }
}
