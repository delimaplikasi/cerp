<?php namespace App\Core;

use App\Config\Debug as ConfigDebug;
use App\Helper\File;
use App\Singleton\Database;
use App\Singleton\Debug;
use App\Singleton\Preference;
use App\Singleton\Site;
use Atk4\Ui\App as UiApp;
use Atk4\Ui\Exception;
use Atk4\Ui\Exception\ExitApplicationException;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpression;

class App extends UiApp
{
    public const HOOK_AFTER_RENDER = self::class . '@afterRender';
    public const HOOK_BEFORE_OUTPUT = self::class . '@beforeOutput';
    public $exit_called = false;
    public $title = 'Custom ERP';

    public function __construct($default = [])
    {
        $this->template_dir[] = RESOURCE_PATH . '/template';

        parent::__construct($default);

        $this->db = Database::connect();

        $this->title = Preference::get('siteName')['value'] ?? 'Custom CERP';

        $this->init();
    }

    protected function init(): void
    {
        parent::init();

        if (ConfigDebug::$enable && !$this->isJsUrlRequest()) {
            $standardPhpDebugBarObj = Debug::getBar();

            $this->onHook(App::HOOK_BEFORE_RENDER, function (App $app) use ($standardPhpDebugBarObj) {
                if (!is_null($standardPhpDebugBarObj)) {
                    $assetsBasePath = $standardPhpDebugBarObj->getJavascriptRenderer()->getBasePath();
                    File::recursiveRead($assetsBasePath, function ($source) use ($assetsBasePath) {
                        $target = PUBLIC_PATH . '/assets/debugbar' . str_replace($assetsBasePath, '', $source);
                        if (!file_exists(dirname($target))) {
                            @mkdir(dirname($target), 0755, true);
                        }
                        @copy($source, $target);
                    });
                }
            });

            $this->onHook(App::HOOK_BEFORE_RENDER, function (App $app) {
                if (count(Debug::get()) > 0) {
                    $app->html->js(
                        true,
                        new JsExpression((new Jquery('div.kint-rich.kint-folder'))->prop('style', 'bottom:unset;top:0;')->jsRender()),
                    );
                }
            });

            $this->onHook(App::HOOK_BEFORE_OUTPUT, function (App $app) use ($standardPhpDebugBarObj) {
                $debug = '';
                foreach (Debug::get() as $var) {
                    $debug .= $var;
                }

                if (!empty(trim($debug))) {
                    $app->html->template->appendHtml('kint', $debug);
                }

                if (!is_null($standardPhpDebugBarObj)) {
                    $app->requireCss(Site::url('assets/debugbar/vendor/font-awesome/css/font-awesome.min.css'));
                    $app->requireCss(Site::url('assets/debugbar/vendor/highlightjs/styles/github.css'));
                    $app->requireCss(Site::url('assets/debugbar/openhandler.css'));
                    $app->requireCss(Site::url('assets/debugbar/debugbar.css'));
                    $app->requireCss(Site::url('assets/debugbar/widgets.css'));
                    $app->requireCss(Site::url('assets/debugbar/widgets/mails/widget.css'));
                    $app->requireCss(Site::url('assets/debugbar/widgets/sqlqueries/widget.css'));
                    $app->requireCss(Site::url('assets/debugbar/widgets/templates/widget.css'));

                    $app->requireJs(Site::url('assets/debugbar/vendor/highlightjs/highlight.pack.js'));
                    $app->requireJs(Site::url('assets/debugbar/debugbar.js'));
                    $app->requireJs(Site::url('assets/debugbar/widgets.js'));
                    $app->requireJs(Site::url('assets/debugbar/widgets/mails/widget.js'));
                    $app->requireJs(Site::url('assets/debugbar/widgets/sqlqueries/widget.js'));
                    $app->requireJs(Site::url('assets/debugbar/widgets/templates/widget.js'));
                    $app->requireJs(Site::url('assets/debugbar/openhandler.js'));
    
                    $app->html->template->appendHtml('phpDebugBar', $standardPhpDebugBarObj->getJavascriptRenderer()->render());
                }
            });
        }
    }

    protected function outputResponseHtml(string $data, array $headers = []): void
    {
        $this->outputResponse(
            $data,
            array_merge($this->normalizeHeaders($headers), ['content-type' => 'text/html'])
        );
    }

    public function run()
    {
        $isExitException = false;
        try {
            $this->run_called = true;
            $this->hook(self::HOOK_BEFORE_RENDER);
            $this->is_rendering = true;

            // if no App layout set
            if (!isset($this->html)) {
                throw new Exception('App layout should be set.');
            }

            $this->html->template->set('title', $this->title);
            $this->html->renderAll();
            $this->html->template->appendHtml('HEAD', $this->html->getJs());
            $this->is_rendering = false;
            $this->hook(self::HOOK_BEFORE_OUTPUT);

            if (isset($_GET['__atk_callback']) && $this->catch_runaway_callbacks) {
                throw new Exception('Callback requested, but never reached. You may be missing some arguments in request URL.');
            }

            $output = $this->html->template->render();
        } catch (ExitApplicationException $e) {
            $output = '';
            $isExitException = true;
        }

        if (!$this->exit_called) { // output already send by terminate()
            if ($this->isJsUrlRequest()) {
                $this->outputResponseJson($output);
            } else {
                $this->outputResponseHtml($output);
            }
        }

        if ($isExitException) {
            $this->callExit();
        }
    }
}
