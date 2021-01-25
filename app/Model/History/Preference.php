<?php namespace App\Model\History;

use App\Model\Preference as ModelPreference;

class Preference extends ModelPreference
{
    public $schema = 'cerp_history';
    public $caption = 'Preference History';

    protected function init(): void
    {
        parent::init();

        $this->hasMaster(new ModelPreference());
    }
}
