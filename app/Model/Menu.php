<?php namespace App\Model;

use App\Core\Model;
use App\Model\History\Menu as HistoryMenu;
use App\Model\Menu\Permission;

class Menu extends Model
{
    public $schema = 'cerp';
    public $table = 'menu';
    public $caption = 'Menu';

    protected function init(): void
    {
        parent::init();

        $parent = $this->hasParent(new self());

        $this->addCode();
        $this->addName();

        $this->addField('url', [
            'type' => 'string',
            'required' => true,
            'mandatory' => true,
        ]);

        $this->addField('icon', [
            'type' => 'string',
            'default' => null,
        ]);

        $this->addSequencePosition();
        $this->addDescription();
        $this->addNote();

        $status = $this->hasStatus();
        $history = $this->hasHistory(new HistoryMenu());

        $permissions = $this->hasMany('menuPermission', [
            new Permission(),
            'our_field' => 'id',
            'their_field' => 'menu_id',
            'caption' => 'Permission'
        ]);

        $this->onHook(Model::HOOK_BEFORE_INSERT, function (Menu $model, &$data) {
            if (is_int($data['sequence_position'])) {
                $data['sequence_position'] = str_pad(strval($data['sequence_position']), 2, '0', STR_PAD_LEFT);
            } else {
                $data['sequence_position'] = '99';
            }
        });

        $this->onHook(Model::HOOK_BEFORE_DELETE, function (Menu $model, $id) {
            if ($model->schema === 'cerp') {
                foreach ($model->refModel('menuPermission')->refModel('permission')->addCondition('code', 'in', [
                    $model->get('code') . '::browse',
                    $model->get('code') . '::read',
                    $model->get('code') . '::edit',
                    $model->get('code') . '::add',
                    $model->get('code') . '::delete',
                ])->action('select') as $item) {
                    $model->refModel('menuPermission')->refModel('permission')->delete($item['id']);
                }
            }
        });

        $this->onHook(Model::HOOK_AFTER_INSERT, function (Menu $model, $id) {
            if ($model->schema === 'cerp') {
                $browsePermissionID = $model->refModel('menuPermission')->refModel('permission')->insert([
                    'code' => $model->get('code') . '::browse',
                    'name' => 'Browse ' . $model->get('name'),
                ]);

                $model->refModel('menuPermission')->insert([
                    'menu_id' => $id,
                    'permission_id' => $browsePermissionID,
                ]);

                $model->refModel('menuPermission')->refModel('permission')->insert([
                    'code' => $model->get('code') . '::read',
                    'name' => 'Read ' . $model->get('name'),
                ]);

                $model->refModel('menuPermission')->refModel('permission')->insert([
                    'code' => $model->get('code') . '::edit',
                    'name' => 'Edit ' . $model->get('name'),
                ]);

                $model->refModel('menuPermission')->refModel('permission')->insert([
                    'code' => $model->get('code') . '::add',
                    'name' => 'Add ' . $model->get('name'),
                ]);

                $model->refModel('menuPermission')->refModel('permission')->insert([
                    'code' => $model->get('code') . '::delete',
                    'name' => 'Delete ' . $model->get('name'),
                ]);
            }
        });
    }
}
