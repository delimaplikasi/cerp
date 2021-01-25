<?php namespace App\Model;

use App\Core\Model;
use App\Model\History\Role as HistoryRole;
use App\Model\Role\Permission as RolePermission;
use App\Model\User\Role as UserRole;
use Stringy\StaticStringy;

class Role extends Model
{
    public $schema = 'cerp';
    public $table = 'role';
    public $caption = 'Role';

    public $permissions_to_add = null;
    public $permissions_to_remove = null;

    protected function init(): void
    {
        parent::init();

        $parent = $this->hasParent(new self());

        $this->addCode();
        $this->addName();
        $this->addDescription();
        $this->addNote();

        $status = $this->hasStatus();
        $history = $this->hasHistory(new HistoryRole());

        $rolePermission = $this->hasMany('rolePermission', [
            new RolePermission(),
            'our_field' => 'id',
            'their_field' => 'role_id',
            'caption' => 'Permissions'
        ]);

        $this->addField('permissions_to_add', [
            'never_persist' => true,
            'type' => 'array',
        ]);

        $this->addField('permissions_to_remove', [
            'never_persist' => true,
            'type' => 'array',
        ]);

        $userRole = $this->hasMany('userRole', [
            new UserRole(),
            'our_field' => 'id',
            'their_field' => 'role_id',
            'caption' => 'User Role'
        ]);

        $this->onHook(Model::HOOK_BEFORE_DELETE, function (Role $model, $id) {
            if ($model->schema === 'cerp') {
                foreach ($model->addCondition('id', $id)->ref('rolePermission')->action('select') as $item) {
                    $model->refModel('rolePermission')->delete($item['id']);
                }

                foreach ($model->addCondition('id', $id)->ref('userRole')->action('select') as $item) {
                    $model->refModel('userRole')->delete($item['id']);
                }
            }
        });

        $this->onHook(Model::HOOK_BEFORE_INSERT, function (Role &$model) {
            if ($model->schema === 'cerp') {
                $model->permissions_to_add = $model->get('permissions_to_add');
            }
        });

        $this->onHook(Model::HOOK_AFTER_INSERT, function (Role $model, $id) {
            if ($model->schema === 'cerp') {
                if (!is_null($model->permissions_to_add)) {
                    foreach ($model->permissions_to_add as $permission => $action) {
                        if (is_array($action)) {
                            foreach ($model->refModel('rolePermission')->refModel('permission')->addCondition('code', 'in', array_map(function ($item) use ($permission) {
                                return "{$permission}::{$item}";
                            }, $action))->action('select') as $p) {
                                $model->refModel('rolePermission')->insert([
                                    'role_id' => $id,
                                    'permission_id' => $p['id'],
                                ]);
                            }
                        } else {
                            if (StaticStringy::contains($action, '::')) {
                                foreach ($model->refModel('rolePermission')->refModel('permission')->addCondition('code', $action)->action('select') as $p) {
                                    $model->refModel('rolePermission')->insert([
                                        'role_id' => $id,
                                        'permission_id' => $p['id'],
                                    ]);
                                }
                            } else {
                                foreach ($model->refModel('rolePermission')->refModel('permission')->addCondition('code', 'in', array_map(function ($item) use ($action) {
                                    return "{$action}::{$item}";
                                }, [
                                    'browse', 'read', 'edit', 'add', 'delete'
                                ]))->action('select') as $p) {
                                    $model->refModel('rolePermission')->insert([
                                        'role_id' => $id,
                                        'permission_id' => $p['id'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        });
    }
}
