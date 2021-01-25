<?php namespace App\Model\History;

use App\Model\Role as ModelRole;

class Role extends ModelRole
{
    public $schema = 'cerp_history';
    public $caption = 'Role History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new ModelRole());
    }
}
