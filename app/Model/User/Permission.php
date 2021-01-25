<?php namespace App\Model\User;

use App\Core\Model;
use App\Model\History\User\Permission as UserPermission;
use App\Model\Permission as ModelPermission;
use App\Model\User;

class Permission extends Model
{
    public $schema = 'cerp';
    public $table = 'user_permission';
    public $caption = 'User Permission';

    protected function init(): void
    {
        parent::init();

        $this->hasOne('user', [
            new User(),
            'our_field' => 'user_id',
            'their_field' => 'id',
            'caption' => 'User',
        ])->withTitle();

        $this->hasOne('permission', [
            new ModelPermission(),
            'our_field' => 'permission_id',
            'their_field' => 'id',
            'caption' => 'Permission'
        ])->withTitle();

        $status = $this->hasStatus();
        $history = $this->hasHistory(new UserPermission());
    }
}
