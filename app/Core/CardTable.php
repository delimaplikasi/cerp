<?php

declare(strict_types=1);

namespace App\Core;

use atk4\data\Model;
use atk4\ui\Exception;
use atk4\ui\Table;

/**
 * Card class displays a single record data.
 *
 * IMPORTANT: Although the purpose of the "Card" component will remain the same, we do plan to
 * improve implementation of a card to to use https://semantic-ui.com/views/card.html.
 */
class CardTable extends Table
{
    protected $_bypass = false;

    public $displayFields = null;

    public function setModel(Model $model, $columndef = null)
    {
        if ($this->_bypass) {
            return parent::setModel($model);
        }

        if (!$model->loaded()) {
            throw (new Exception('Model must be loaded'))
                ->addMoreInfo('model', $model);
        }

        $data = [];

        $ui_values = $this->app ? $this->app->ui_persistence->typecastSaveRow($model, $model->get()) : $model->get();

        if (is_array($this->displayFields)) {
            $columndef = array_merge($this->displayFields, is_array($columndef) ? $columndef : []);

            if (count($columndef) === 0) {
                $columndef = null;
            }
        }

        foreach ($model->get() as $key => $value) {
            if (!$columndef || ($columndef && in_array($key, $columndef, true))) {
                // if ($model->getField($key)->isVisible() && !$model->getField($key)->never_persist) {
                    
                // }

                $data[] = [
                    'id' => $key,
                    'field' => $model->getField($key)->getCaption(),
                    'value' => $ui_values[$key],
                ];
            }
        }

        $this->_bypass = true;
        $mm = parent::setSource($data);
        $this->addDecorator('value', [Table\Column\Multiformat::class, function ($row, $field) use ($model) {
            $field = $model->getField($row->data['id']);
            $ret = $this->decoratorFactory(
                $field,
                $field->type === 'boolean' ? [Table\Column\Status::class,  ['positive' => [true, 'Yes'], 'negative' => [false, 'No']]] : []
            );
            if ($ret instanceof Table\Column\Money) {
                $ret->attr['all']['class'] = ['single line'];
            }

            return $ret;
        }]);
        $this->_bypass = false;

        return $mm;
    }
}
