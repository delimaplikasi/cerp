<?php namespace App\Model\Tree;

use App\Model\Status as ModelStatus;
use App\Core\Table\Column\JsonToTree;

class Status extends ModelStatus
{
    public $schema = 'cerp_tree_view';

    protected function init(): void
    {
        parent::init();

        $this->addField('level', [
            'type' => 'integer',
            'default' => 99,
        ]);

        $this->addField('id_path', [
            'type' => 'array',
            'serialize' => 'json'
        ]);

        $this->addField('code_path', [
            'type' => 'array',
            'serialize' => 'json',
        ]);

        $this->addField('name_path', [
            'type' => 'array',
            'serialize' => 'json',
            'ui' => [
                'caption' => 'Name',
                'table' => [
                    JsonToTree::class,
                    'separator' => ' // '
                ]
            ]
        ]);
    }
}
