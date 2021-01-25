<?php namespace App\Model\History\User;

use App\Model\User\Permission as UserPermission;

class Permission extends UserPermission
{
    public $schema = 'cerp_history';
    public $caption = 'User Permission History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new UserPermission());
    }
}
