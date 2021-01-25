<?php

use App\Core\CardTable;
use App\Core\MasterCRUD;
use App\Dashboard;
use App\Model\Preference;
use App\Singleton\Database;
use Atk4\Ui\Layout\Maestro;

require_once('../../../bootstrap.php');

$app = new Dashboard();
$app->initLayout([
    Maestro::class,
]);

$model = new Preference(Database::connect());

$crud = MasterCRUD::addTo($app);
$crud->setModel($model, [
    [
        '_crud' => [
            'displayFields' => [
                'code', 'name', 'value', 'status',
            ],
        ],
        '_card' => [
            CardTable::class,
            'displayFields' => [
                'code', 'name', 'value', 'status',
            ],
        ],
    ],
    'history' => [
        [
            '_crud' => [
                'displayFields' => [
                    'code', 'name', 'value', 'status', 'created_at', 'updated_at', 'deleted_at'
                ],
            ],
        ],
    ],
]);
