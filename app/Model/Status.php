<?php namespace App\Model;

use App\Core\Model;
use App\Model\History\Status as HistoryStatus;

class Status extends Model
{
    public $schema = 'cerp';
    public $table = 'status';
    public $caption = 'Status';

    protected function init(): void
    {
        parent::init();

        $this->hasParent(new self());

        $this->addCode();
        $this->addName();
        $this->addDescription();
        $this->addNote();

        $history = $this->hasHistory(new HistoryStatus());
    }
}
