<?php

use App\Config\Debug as ConfigDebug;
use App\Config\Session as ConfigSession;
use App\Singleton\Database;
use App\Singleton\Debug;
use App\Singleton\Http\IncomingRequest;
use App\Singleton\Session;
use DebugBar\OpenHandler;

define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('APP_CONFIG_PATH', APP_PATH . '/Config');
define('APP_CORE_PATH', APP_PATH . '/Core');
define('RESOURCE_PATH', ROOT_PATH . '/resource');
define('TEMP_PATH', ROOT_PATH . '/temp');
define('PUBLIC_PATH', ROOT_PATH . '/public');

define('KINT_SKIP_HELPERS', true);

require_once(ROOT_PATH . '/vendor/autoload.php');

session_save_path(ConfigSession::$savePath);

$debugBarHandler = boolval(IncomingRequest::query()->get('debugbar_handler', false));

if (!$debugBarHandler) {
    Session::start();
}

if (ConfigDebug::$enable) {
    $phpDebugBar = 'DebugBar\DebugBar';
    if (class_exists($phpDebugBar)) {
        $standardPhpDebugBar = 'DebugBar\StandardDebugBar';

        /** @var \DebugBar\StandardDebugBar */
        $standardPhpDebugBarObj = new $standardPhpDebugBar();

        if (class_exists("App\\Config\\Database")) {
            $traceablePDO = 'DebugBar\DataCollector\PDO\TraceablePDO';
            $pdoCollector = 'DebugBar\DataCollector\PDO\PDOCollector';
            $standardPhpDebugBarObj->addCollector(new $pdoCollector(new $traceablePDO(Database::connect()->connection->connection())));
        }

        $storage = 'DebugBar\Storage\FileStorage';
        $standardPhpDebugBarObj->setStorage(new $storage(TEMP_PATH . '/debugbar'));

        if (!$debugBarHandler) {
            $standardPhpDebugBarRenderer = $standardPhpDebugBarObj->getJavascriptRenderer();
            $standardPhpDebugBarRenderer->setOpenHandlerUrl(IncomingRequest::url()->get() . '?debugbar_handler=true');

            Debug::setBar($standardPhpDebugBarObj);
        } else {
            $handler = new OpenHandler($standardPhpDebugBarObj);
            $handler->handle();
            exit;
        }
    }

    if (!$debugBarHandler) {
        if (class_exists('Kint\Kint')) {
            $kint = 'Kint\Kint';
            $kintRenderer = 'Kint\Renderer\RichRenderer';
            $kint::$aliases[] = 'kint';
            $kint::$return = true;
    
            function kint(...$vars)
            {
                Debug::add('Kint\Kint'::dump(...$vars));
            }
        } else {
            function kint(...$vars) {}
        }
    }
} else {
    if (!$debugBarHandler) {
        function kint(...$vars) {}
    }
}
