<?php namespace App\Model\Tree;

use App\Model\Menu as ModelMenu;

class Menu extends ModelMenu
{
    public $schema = 'cerp_tree_view';

    protected function init(): void
    {
        parent::init();

        $this->addField('level', [
            'type' => 'integer',
            'default' => 0,
        ]);

        $this->addField('id_path', [
            'type' => 'array',
            'serialize' => 'json'
        ]);

        $this->addField('code_path', [
            'type' => 'array',
            'serialize' => 'json'
        ]);

        $this->addField('name_path', [
            'type' => 'array',
            'serialize' => 'json'
        ]);

        $this->addField('url_path', [
            'type' => 'array',
            'serialize' => 'json'
        ]);

        $this->addField('sequence_position_path', [
            'type' => 'array',
            'serialize' => 'json'
        ]);
    }
}
