<?php namespace App\Model\User;

use App\Core\Model;
use App\Model\History\User\Role as UserRole;
use App\Model\Role as ModelRole;
use App\Model\User;

class Role extends Model
{
    public $schema = 'cerp';
    public $table = 'user_role';
    public $caption = 'User Role';

    protected function init(): void
    {
        parent::init();

        $user = $this->hasOne('user', [
            new User(),
            'our_field' => 'user_id',
            'their_field' => 'id',
            'caption' => 'User'
        ]);

        $user->withTitle();

        $role = $this->hasOne('role', [
            new ModelRole(),
            'our_field' => 'role_id',
            'their_field' => 'id',
            'caption' => 'Role'
        ]);

        $role->withTitle();

        $status = $this->hasStatus();
        $history = $this->hasHistory(new UserRole());
    }
}
