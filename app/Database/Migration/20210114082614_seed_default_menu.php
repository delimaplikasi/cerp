<?php
declare(strict_types=1);

use App\Model\Menu;
use App\Singleton\Database;
use Phinx\Migration\AbstractMigration;

final class SeedDefaultMenu extends AbstractMigration
{
    public function up()
    {
        $model = new Menu(Database::connect());

        $dashboardID = $model->insert([
            'code' => 'dashboard',
            'name' => 'Dashboard',
            'url' => 'dashboard',
            'sequence_position' => 0,
        ]);

        $model->insert([
            'code' => 'dashboardMain',
            'name' => 'Dashboard',
            'url' => 'dashboard/index',
            'sequence_position' => 1,
            'parent_id' => $dashboardID,
        ]);

        $settingID = $model->insert([
            'code' => 'dashboardSetting',
            'name' => 'Setting',
            'url' => 'dashboard/setting',
            'sequence_position' => 99,
            'parent_id' => $dashboardID,
        ]);

        $model->insert([
            'code' => 'dashboardSettingGeneral',
            'name' => 'General',
            'url' => 'dashboard/setting/index',
            'sequence_position' => 1,
            'parent_id' => $settingID,
        ]);

        $settingUACID = $model->insert([
            'code' => 'dashboardSettingUAC',
            'name' => 'User Access Control',
            'url' => 'dashboard/setting/uac',
            'sequence_position' => 2,
            'parent_id' => $settingID,
        ]);

        $model->insert([
            'code' => 'dashboardSettingUACUser',
            'name' => 'User',
            'url' => 'dashboard/setting/uac/index',
            'sequence_position' => 1,
            'parent_id' => $settingUACID,
        ]);

        $model->insert([
            'code' => 'dashboardSettingUACRole',
            'name' => 'Role',
            'url' => 'dashboard/setting/uac/role',
            'sequence_position' => 2,
            'parent_id' => $settingUACID,
        ]);

        $model->insert([
            'code' => 'dashboardSettingUACPermission',
            'name' => 'Permission',
            'url' => 'dashboard/setting/uac/permission',
            'sequence_position' => 3,
            'parent_id' => $settingUACID,
        ]);

        $settingDatabaseID = $model->insert([
            'code' => 'dashboardSettingDatabase',
            'name' => 'Database',
            'url' => 'dashboard/setting/database',
            'sequence_position' => 99,
            'parent_id' => $settingID,
        ]);

        $model->insert([
            'code' => 'dashboardSettingDatabasePanel',
            'name' => 'Panel',
            'url' => 'dashboard/setting/database/index',
            'sequence_position' => 1,
            'parent_id' => $settingDatabaseID,
        ]);

        $model->insert([
            'code' => 'dashboardSettingDatabaseMigration',
            'name' => 'Migration',
            'url' => 'dashboard/setting/database/migration',
            'sequence_position' => 2,
            'parent_id' => $settingDatabaseID,
        ]);
    }

    public function down()
    {
        $model = new Menu(Database::connect());

        foreach ($model->addCondition('code', 'in', [
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
        ])->action('select') as $m) {
            $model->delete($m['id']);
        }
    }
}
