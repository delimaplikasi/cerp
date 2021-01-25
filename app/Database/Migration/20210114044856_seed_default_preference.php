<?php
declare(strict_types=1);

use App\Model\Preference;
use App\Singleton\Database;
use Phinx\Migration\AbstractMigration;

final class SeedDefaultPreference extends AbstractMigration
{
    public function up()
    {
        $model = new Preference(Database::connect());

        $model->insert([
            'code' => 'siteName',
            'name' => 'Site Name',
            'value' => 'Custom ERP',
        ]);
    }

    public function down()
    {
        $model = new Preference(Database::connect());

        foreach ($model->addCondition('code', 'siteName')->action('select') as $p) {
            $model->delete($p['id']);
        }
    }
}
