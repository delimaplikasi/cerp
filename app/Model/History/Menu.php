<?php namespace App\Model\History;

use App\Model\Menu as ModelMenu;

class Menu extends ModelMenu
{
    public $schema = 'cerp_history';
    public $caption = 'Menu History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new ModelMenu());
    }
}
