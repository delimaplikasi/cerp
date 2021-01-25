<?php namespace App\Model;

use App\Core\Model;
use App\Model\History\Preference as HistoryPreference;

class Preference extends Model
{
    public $table = 'preference';
    public $schema = 'cerp';
    public $caption = 'Preference';

    protected function init(): void
    {
        parent::init();

        $this->addCode();
        $this->addName();
        $this->addField('value', [
            'type' => 'string',
            'default' => null,
        ]);
        $this->addDescription();
        $this->addNote();

        $this->hasStatus();

        $this->hasHistory(new HistoryPreference());
    }
}
