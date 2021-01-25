<?php namespace App\Model\History;

use App\Model\Permission as ModelPermission;

class Permission extends ModelPermission
{
    public $schema = 'cerp_history';
    public $caption = 'Permission History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new ModelPermission());
    }
}
