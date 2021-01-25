<?php namespace App\Model\Menu;

use App\Core\Model;
use App\Model\History\Menu\Permission as MenuPermission;
use App\Model\Menu;
use App\Model\Permission as ModelPermission;

class Permission extends Model
{
    public $schema = 'cerp';
    public $table = 'menu_permission';
    public $caption = 'Menu Permission';

    protected function init(): void
    {
        parent::init();

        $this->hasOne('menu', [
            new Menu(),
            'our_field' => 'menu_id',
            'their_field' => 'id',
            'caption' => 'Menu'
        ])->withTitle();

        $this->hasOne('permission', [
            new ModelPermission(),
            'our_field' => 'permission_id',
            'their_field' => 'id',
            'caption' => 'Permission'
        ])->withTitle();

        $status = $this->hasStatus();
        $history = $this->hasHistory(new MenuPermission());
    }
}
