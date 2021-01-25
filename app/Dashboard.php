<?php namespace App;

use App\Core\App;
use Atk4\Ui\Layout\NavigableInterface;
use App\Model\Tree\Menu;
use App\Singleton\Database;
use App\Singleton\Http\IncomingRequest;
use App\Singleton\Session;
use App\Singleton\Site;

class Dashboard extends App
{
    protected function init(): void
    {
        parent::init();

        if (is_null(Session::of('Dashboard')->get('user'))) {
            $this->getApp()->redirect([
                Site::url('login'),
                'redirect' => IncomingRequest::url()->get(),
            ]);
        }
    }

    public function initLayout($seed)
    {
        parent::initLayout($seed);
        $this->buildMenu();
    }

    public function buildMenu()
    {
        $layout = $this->layout;

        if ($layout instanceof NavigableInterface) {
            /** @var \Atk4\Ui\Menu */
            $menuRight = $layout->menuRight;
            $menuPengguna = $menuRight->addMenu([
                Session::of('Dashboard')->get('user')['name']
            ]);

            $menuPengguna->addItem(['Log Out'], [
                Site::url('dashboard/logout')
            ]);

            $menuTree = new Menu(Database::connect());
            $menus = $menuTree
                ->addCondition($menuTree->expr('[code_path]->>0 = []', [
                    'dashboard'
                ]))
                ->addCondition('level', '>', '0')
                ->setOrder($menuTree->expr('[sequence_position_path]::TEXT'))
                ->action('select')->get();

            $menuByLevel = [];
            array_walk($menus, function ($menu) use (&$menuByLevel) {
                $menuByLevel[$menu['level']][$menu['id']] = $menu;
            });

            $menuTree = [];
            array_walk($menuByLevel, function ($menus, $level) use ($menuByLevel, &$menuTree, $layout) {
                array_walk($menus, function ($menu) use ($level, $menuByLevel, &$menuTree, $layout) {
                    $isParent = false;
                    if (($level + 1) <= count($menuByLevel)) {
                        $nextMenu = $menuByLevel[($level + 1)];
                        array_walk($nextMenu, function ($m) use (&$isParent, $menu) {
                            if ($m['parent_id'] == $menu['id']) {
                                $isParent = true;
                            }
                        });
                    }

                    if ($isParent) {
                        if (array_key_exists(intval($menu['parent_id']), $menuTree)) {
                            $menuTree[$menu['id']] = $menuTree[intval($menu['parent_id'])]->addGroup([
                                $menu['name'],
                            ]);
                        } else {
                            $menuTree[$menu['id']] = $layout->addMenuGroup([
                                $menu['name'],
                            ]);
                        }
                    } else {
                        if (array_key_exists(intval($menu['parent_id']), $menuTree)) {
                            $menuTree[intval($menu['parent_id'])]->addItem([
                                $menu['name'],
                            ], [
                                Site::url($menu['url']),
                            ]);
                        } else {
                            $layout->addMenuItem([
                                $menu['name'],
                            ], [
                                Site::url($menu['url']),
                            ]);
                        }
                    }
                });
            });
        }
    }
}
