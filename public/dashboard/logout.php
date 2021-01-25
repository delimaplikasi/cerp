<?php

use App\Dashboard;
use App\Singleton\Session;
use App\Singleton\Site;

require_once('../../bootstrap.php');

Session::destroy();

$app = new Dashboard();
$app->redirect([
    Site::url('dashboard/index'),
]);
