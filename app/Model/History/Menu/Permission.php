<?php namespace App\Model\History\Menu;

use App\Model\Menu\Permission as MenuPermission;

class Permission extends MenuPermission
{
    public $schema = 'cerp_history';
    public $caption = 'Menu Permission History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new MenuPermission());
    }
}
