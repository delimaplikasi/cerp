<?php namespace App\Model\History;

use App\Model\Status as ModelStatus;

class Status extends ModelStatus
{
    public $schema = 'cerp_history';
    public $caption = 'Status History';

    protected function init(): void
    {
        parent::init();

        $master = $this->hasMaster(new ModelStatus());
    }
}
