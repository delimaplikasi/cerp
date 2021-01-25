<?php

use App\Core\App;
use App\Singleton\Site;

require_once('../bootstrap.php');

$app = new App();
$app->redirect([
    Site::url('dashboard/index'),
]);
