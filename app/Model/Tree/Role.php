<?php namespace App\Model\Tree;

use atk4\ui\Table\Column\Labels;
use App\Model\Role as ModelRole;
use App\Core\Table\Column\JsonToTree;

class Role extends ModelRole
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

        $this->addExpression(
            'permissions',
            [
                $this->reflink('rolePermission')->ref('permission')->action('field', [
                    $this->reflink('rolePermission')->ref('permission')->expr("JSON_AGG(TO_JSON([name]))")
                ]),
                'serialize' => 'json',
                'ui' => [
                    'table' => [
                        Labels::class,
                    ]
                ]
            ]
        );
    }
}
