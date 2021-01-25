<?php namespace App\Model\History;

use App\Model\User as ModelUser;

class User extends ModelUser
{
    public $schema = 'cerp_history';
    public $caption = 'User History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new ModelUser());
    }
}
