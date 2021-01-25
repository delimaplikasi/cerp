<?php namespace App\Model\History\User;

use App\Model\User\Role as UserRole;

class Role extends UserRole
{
    public $schema = 'cerp_history';
    public $caption = 'User Role History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new UserRole());
    }
}
