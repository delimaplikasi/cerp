<?php namespace App\Model;

use App\Core\Model;
use Atk4\Data\Field\Email;
use atk4\ui\Table\Column\Labels;
use App\Model\History\User as HistoryUser;
use App\Model\User\Permission;
use App\Model\User\Role;

class User extends Model
{
    public $schema = 'cerp';
    public $table = 'user';
    public $caption = 'User';

    public $roles_to_add = null;
    public $roles_to_remove = null;

    protected function init(): void
    {
        parent::init();

        $this->addCode();
        $this->addName();

        $this->addField('email', [
            'type' => Email::class,
            'required' => true,
            'mandatory' => true,
        ]);

        $this->addField('password', [
            'type' => 'password',
            'required' => true,
            'mandatory' => true,
        ]);

        $this->addNote();

        $this->addField('roles_to_add', [
            'never_persist' => true,
            'type' => 'array',
        ]);

        $this->addField('roles_to_remove', [
            'never_persist' => true,
            'type' => 'array',
        ]);

        $status = $this->hasStatus();
        $history = $this->hasHistory(new HistoryUser());

        $userRole = $this->hasMany('userRole', [
            new Role(),
            'our_field' => 'id',
            'their_field' => 'user_id',
            'caption' => 'Roles'
        ]);

        $userRole->addField('roles_assigned', [
            'never_save' => true,
            'serialize' => 'json',
            'aggregate' => $this->refLink('userRole')->expr("JSON_AGG(TO_JSON([role]))"),
            'ui' => [
                'table' => [
                    Labels::class
                ]
            ]
        ]);

        $userPermission = $this->hasMany('userPermission', [
            new Permission(),
            'our_field' => 'id',
            'their_field' => 'user_id',
            'caption' => 'User Permission',
        ]);

        $this->onHook(Model::HOOK_BEFORE_INSERT, function (User &$user) {
            if ($user->hasRef('history') && !$user->hasRef('master')) {
                $user->roles_to_add = $user->get('roles_to_add');
            }
        });

        $this->onHook(Model::HOOK_AFTER_INSERT, function (User $user, $id) {
            if ($user->hasRef('history') && !$user->hasRef('master')) {
                if (!is_null($user->roles_to_add)) {
                    foreach ($user->refModel('userRole')->refModel('role')->addCondition('code', 'in', $user->roles_to_add)->action('select') as $item) {
                        $user->refModel('userRole')->insert([
                            'user_id' => $id,
                            'role_id' => $item['id'],
                        ]);
                    }
                }
            }
        });

        $this->onHook(Model::HOOK_BEFORE_DELETE, function (User $user, $id) {
            if ($user->hasRef('history') && !$user->hasRef('master')) {
                foreach ($user->addCondition('id', $id)->ref('userRole')->action('select') as $item) {
                    $user->refModel('userRole')->delete($item['id']);
                }

                foreach ($user->addCondition('id', $id)->ref('userPermission')->action('select') as $item) {
                    $user->refModel('userPermission')->delete($item['id']);
                }
            }
        });
    }
}
