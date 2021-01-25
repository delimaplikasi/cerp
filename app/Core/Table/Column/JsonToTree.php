<?php namespace App\Core\Table\Column;

use atk4\data\Model;
use atk4\ui\Table\Column;

class JsonToTree extends Column
{
    public $separator = ' / ';

    public function getHtmlTags(Model $row, $field)
    {
        $values = $this->values ?? $field->values;

        $v = $field->get();
        $v = is_string($v) ? explode(',', $v) : $v;

        $labels = [];
        foreach ((array) $v as $id) {
            $id = trim($id);

            // if field values is set, then use titles instead of IDs
            $id = $values[$id] ?? $id;

            if (!empty($id)) {
                $labels[] = $id;
            }
        }

        $labels = implode($this->separator, $labels);

        return [$field->short_name => $labels];
    }
}
