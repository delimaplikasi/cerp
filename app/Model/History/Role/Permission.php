<?php namespace App\Model\History\Role;

use App\Model\Role\Permission as RolePermission;

class Permission extends RolePermission
{
    public $schema = 'cerp_history';
    public $caption = 'Role Permission History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new RolePermission());
    }
}
