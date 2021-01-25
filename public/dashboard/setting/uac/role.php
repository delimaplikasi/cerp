<?php

use App\Core\CardTable;
use App\Core\MasterCRUD;
use App\Dashboard;
use App\Model\Role;
use App\Singleton\Database;
use Atk4\Ui\Layout\Maestro;

require_once('../../../../bootstrap.php');

$app = new Dashboard();
$app->initLayout([
    Maestro::class,
]);

$model = new Role(Database::connect());

$crud = MasterCRUD::addTo($app);
$crud->setModel($model, [
    [
        '_crud' => [
            'displayFields' => [
                'code', 'parent', 'name', 'status',
            ],
        ],
        '_card' => [
            CardTable::class,
            'displayFields' => [
                'code', 'name', 'description', 'note', 'status'
            ],
        ],
    ],
    'rolePermission' => [],
    'history' => [],
]);
