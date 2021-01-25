<?php namespace App\Model\Role;

use App\Core\Model;
use atk4\ui\Form\Control\Lookup;
use App\Model\History\Role\Permission as RolePermission;
use App\Model\Permission as ModelPermission;
use App\Model\Role;

class Permission extends Model
{
    public $schema = 'cerp';
    public $table = 'role_permission';
    public $caption = 'Role Permission';

    protected function init(): void
    {
        parent::init();

        $role = $this->hasOne('role', [
            new Role(),
            'our_field' => 'role_id',
            'their_field' => 'id',
            'caption' => 'Role',
        ]);

        $role->withTitle();

        $permission = $this->hasOne('permission', [
            new ModelPermission(),
            'our_field' => 'permission_id',
            'their_field' => 'id',
            'caption' => 'Permission',
            'ui' => [
                'form' => [
                    Lookup::class,
                ]
            ]
        ]);

        $permission->withTitle();

        $status = $this->hasStatus();
        $history = $this->hasHistory(new RolePermission());
    }
}
