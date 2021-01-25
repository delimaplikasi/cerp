<?php namespace App\Model;

use App\Core\Model;
use App\Model\History\Permission as HistoryPermission;
use App\Model\Menu\Permission as MenuPermission;
use App\Model\Role\Permission as RolePermission;
use App\Model\User\Permission as UserPermission;

class Permission extends Model
{
    public $schema = 'cerp';
    public $table = 'permission';
    public $caption = 'Permission';

    protected function init(): void
    {
        parent::init();

        $this->addCode();
        $this->addName();
        $this->addDescription();
        $this->addNote();

        $status = $this->hasStatus();
        $history = $this->hasHistory(new HistoryPermission());

        $userPermission = $this->hasMany('userPermission', [
            new UserPermission(),
            'our_field' => 'id',
            'their_field' => 'permission_id'
        ]);

        $rolePermission = $this->hasMany('rolePermission', [
            new RolePermission(),
            'our_field' => 'id',
            'their_field' => 'permission_id'
        ]);

        $menuPermission = $this->hasMany('menuPermission', [
            new MenuPermission(),
            'our_field' => 'id',
            'their_field' => 'permission_id'
        ]);

        $this->onHook(Model::HOOK_BEFORE_DELETE, function (Permission $model, $id) {
            if ($model->schema === 'cerp') {
                foreach ($model->addCondition('id', $id)->ref('menuPermission')->action('select') as $item) {
                    $model->refModel('menuPermission')->delete($item['id']);
                }

                foreach ($model->addCondition('id', $id)->ref('rolePermission')->action('select') as $item) {
                    $model->refModel('rolePermission')->delete($item['id']);
                }

                foreach ($model->addCondition('id', $id)->ref('userPermission')->action('select') as $item) {
                    $model->refModel('userPermission')->delete($item['id']);
                }
            }
        });
    }
}
