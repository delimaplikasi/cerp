<?php

use App\Dashboard;
use Atk4\Ui\Header;
use Atk4\Ui\Layout\Maestro;

require_once('../../bootstrap.php');

$app = new Dashboard();

$app->initLayout([
    Maestro::class,
]);

Header::addTo($app, [
    'Dashboard',
    'size' => 2,
]);
