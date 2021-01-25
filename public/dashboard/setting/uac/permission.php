<?php

use App\Core\MasterCRUD;
use App\Dashboard;
use App\Model\Permission;
use App\Singleton\Database;
use Atk4\Ui\Layout\Maestro;

require_once('../../../../bootstrap.php');

$app = new Dashboard();
$app->initLayout([
    Maestro::class,
]);

$model = new Permission(Database::connect());

$crud = MasterCRUD::addTo($app);
$crud->setModel($model, [
    [
        '_crud' => [
            'displayFields' => [
                'code', 'name', 'status'
            ],
        ],
    ],
    'history' => [
        [
            '_crud' => [
                'displayFields' => [
                    'code', 'name', 'status', 'created_at', 'updated_at', 'deleted_at'
                ],
            ],
        ],
    ],
]);
