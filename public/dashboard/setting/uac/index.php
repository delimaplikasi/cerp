<?php

use App\Core\CardTable;
use App\Core\MasterCRUD;
use App\Dashboard;
use App\Model\User;
use App\Singleton\Database;
use Atk4\Ui\Layout\Maestro;

require_once('../../../../bootstrap.php');

$app = new Dashboard();
$app->initLayout([
    Maestro::class,
]);

$model = new User(Database::connect());

$crud = MasterCRUD::addTo($app);
$crud->setModel($model, [
    [
        '_crud' => [
            'displayFields' => [
                'code', 'name', 'email', 'roles_assigned', 'status'
            ],
        ],
        '_card' => [
            CardTable::class,
            'displayFields' => [
                'code', 'name', 'email', 'note', 'status'
            ],
        ],
    ],
    'userRole' => [
        [
            '_crud' => [
                'displayFields' => [
                    'role', 'status', 'created_at', 'updated_at'
                ],
            ],
        ],
    ],
    'history' => [
        [
            '_crud' => [
                'displayFields' => [
                    'code', 'name', 'email', 'roles_assigned', 'status', 'created_at', 'updated_at', 'deleted_at'
                ],
            ],
        ],
    ],
]);
