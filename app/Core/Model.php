<?php namespace App\Core;

use App\Config\Site;
use App\Model\Status as ModelStatus;
use atk4\core\Exception;
use atk4\data\Model as DataModel;
use atk4\data\Reference\HasMany;
use atk4\data\Reference\HasOne;
use atk4\ui\Form\Control\Lookup;
use DateTime;
use App\Singleton\Session;
use App\Singleton\Status;

class Model extends DataModel
{
    public const HOOK_BEFORE_RESTORE = self::class . '@beforeRestore';
    public const HOOK_AFTER_RESTORE = self::class . '@afterRestore';
    public const HOOK_BEFORE_SOFT_DELETE = self::class . '@beforeSoftDelete';
    public const HOOK_AFTER_SOFT_DELETE = self::class . '@afterSoftDelete';

    public $schema = null;
    public $title_field = 'name';

    public function __construct($persistence = null, $defaults = [])
    {
        if (!is_null($this->schema) && !empty($this->schema)) {
            $this->table = "{$this->schema}_{$this->table}";
        }

        parent::__construct($persistence, $defaults);
    }

    protected function init(): void
    {
        parent::init();

        $this->addField('created_at', [
            'type' => 'datetime',
            'default' => null,
            'persist_format' => 'Y-m-d H:i:s',
            'persist_timezone' => Site::$timeZone,
            'system' => true,
        ]);

        $this->addField('updated_at', [
            'type' => 'datetime',
            'default' => null,
            'persist_format' => 'Y-m-d H:i:s',
            'persist_timezone' => Site::$timeZone,
            'system'=> true,
        ]);

        $this->addField('deleted_at', [
            'type' => 'datetime',
            'default' => null,
            'persist_format' => 'Y-m-d H:i:s',
            'persist_timezone' => Site::$timeZone,
            'system' => true,
        ]);

        $this->addField('created_by', [
            'type' => 'integer',
            'default' => null,
            'system' => true,
        ]);

        $this->addField('updated_by', [
            'type' => 'integer',
            'default' => null,
            'system' => true,
        ]);

        $this->addField('deleted_by', [
            'type' => 'integer',
            'default' => null,
            'system' => true,
        ]);

        $this->onHook(Model::HOOK_BEFORE_INSERT, function (Model $model, &$data) {
            $data['created_at'] = $data['created_at'] ?? (new DateTime())->format('Y-m-d H:i:s');

            if ($this->hasField('status_id')) {
                if (!isset($data['status_id'])) {
                    $data['status_id'] = Status::get('active')['id'] ?? null;
                }
            }

            $data['created_by'] = !is_null(Session::of('App')) ? (Session::of('App')->get('actor') ?? null) : null;

            if ($this->hasRef('parent')) {
                if (!isset($data['parent_id'])) {
                    $data['parent_id'] = null;
                }
            }
        });

        $this->onHook(Model::HOOK_BEFORE_UPDATE, function (Model $model, &$data) {
            $data['updated_at'] = $data['updated_at'] ?? (new DateTime())->format('Y-m-d H:i:s');

            $data['updated_by'] = !is_null(Session::of('App')) ? (Session::of('App')->get('actor') ?? null) : null;

            if ($this->hasRef('parent')) {
                if (!isset($data['parent_id'])) {
                    $data['parent_id'] = null;
                }
            }
        });

        $this->onHook(Model::HOOK_BEFORE_DELETE, function (Model $model, $id) {
            if ($model->hasRef('history') && !$model->hasRef('master')) {
                foreach ($model->addCondition('id', $id)->ref('history')->action('select') as $item) {
                    $model->refModel('history')->delete($item['id']);
                }
            }
        });

        $this->onHook(Model::HOOK_AFTER_INSERT, function (Model $model, $id) {
            if ($model->hasRef('history') && !$model->hasRef('master')) {
                $data = $model->load($id)->get();
                $data['created_at'] = is_a($data['created_at'], DateTime::class) ? $data['created_at']->format('Y-m-d H:i:s') : $data['created_at'];
                unset($data['id']);
                $data['master_id'] = $id;

                $model->refModel('history')->insert($data);
            }
        });

        $this->onHook(Model::HOOK_AFTER_UPDATE, function (Model $model, &$data) {
            if ($model->hasRef('history') && !$model->hasRef('master')) {
                $oldData = $model->get();
                $id = $oldData['id'];
                $oldData['created_at'] = is_a($oldData['created_at'], DateTime::class) ? $oldData['created_at']->format('Y-m-d H:i:s') : $oldData['created_at'];
                $oldData['updated_at'] = is_a($oldData['updated_at'], DateTime::class) ? $oldData['updated_at']->format('Y-m-d H:i:s') : $oldData['updated_at'];
                unset($oldData['id']);

                $model->refModel('history')->insert(array_merge($oldData, $data, [
                    'master_id' => $id,
                ]));
            }
        });
    }

    public function softDelete()
    {
        if (!$this->loaded()) {
            throw (new Exception('Model must be loaded before soft deleting'))->addMoreInfo('model', $this);
        }

        $id = $this->getId();
        if ($this->hook(Model::HOOK_BEFORE_SOFT_DELETE) === false) {
            return $this;
        }

        $rs = $this->reload_after_save;
        $this->reload_after_save = false;
        $this->save([
            'deleted_at' => (new DateTime())->format('Y-m-d H:i:s'),
            'deleted_by' => !is_null(Session::of('App')) ? (Session::of('App')->get('actor') ?? null) : null,
            'status_id' => Status::get('inactive')['id'] ?? null,
        ])->unload();
        $this->reload_after_save = $rs;

        $this->hook(Model::HOOK_AFTER_SOFT_DELETE, [$id]);
        return $this;
    }

    public function restore(Model $model)
    {
        if (!$model->loaded()) {
            throw (new Exception('Model must be loaded before restoring'))->addMoreInfo('model', $model);
        }

        $id = $model->getId();
        if ($model->hook($this::HOOK_BEFORE_RESTORE) === false) {
            return $model;
        }

        $rs = $model->reload_after_save;
        $model->reload_after_save = false;
        $model->save([
            'deleted_at' => null,
            'deleted_by' => null,
            'status_id' => Status::get('active')['id'] ?? null,
        ])->unload();
        $model->reload_after_save = $rs;

        $model->hook($this::HOOK_AFTER_RESTORE, [$id]);

        return $model;
    }

    public function hasStatus(): HasOne
    {
        $status = $this->hasOne('status', [
            new ModelStatus(),
            'their_field' => 'id',
            'our_field' => 'status_id',
            'caption' => 'Status',
            'ui' => [
                'form' => [
                    Lookup::class
                ]
            ]
        ]);

        $status->withTitle();

        return $status;
    }

    public function hasParent(Model $model): HasOne
    {
        $parent = $this->hasOne('parent', [
            $model,
            'our_field' => 'parent_id',
            'their_field' => 'id',
            'caption' => 'Parent',
            'ui' => [
                'form' => [
                    Lookup::class
                ]
            ]
        ]);

        if ($model->hasField($model->title_field)) {
            kint(get_class($this));
            $parent->withTitle();
        }

        return $parent;
    }

    public function hasMaster(Model $model): HasOne
    {
        $master = $this->hasOne('master', [
            $model,
            'our_field' => 'master_id',
            'their_field' => 'id',
            'caption' => 'Master',
            'ui' => [
                'form' => [
                    Lookup::class
                ]
            ]
        ]);

        if ($model->hasField($model->title_field)) {
            $master->withTitle();
        }

        return $master;
    }

    public function hasHistory(Model $model): HasMany
    {
        $history = $this->hasMany('history', [
            $model,
            'our_field' => 'id',
            'their_field' => 'master_id',
            'caption' => 'History',
        ]);

        return $history;
    }

    public function setOrder($field, string $direction = 'asc')
    {
        // fields passed as array
        if (is_array($field)) {
            if (func_num_args() > 1) {
                throw (new Exception('If first argument is array, second argument must not be used'))
                    ->addMoreInfo('arg1', $field)
                    ->addMoreInfo('arg2', $direction);
            }

            foreach (array_reverse($field) as $key => $direction) {
                if (is_int($key)) {
                    if (is_array($direction)) {
                        // format [field, direction]
                        $this->setOrder(...$direction);
                    } else {
                        // format "field"
                        $this->setOrder($direction);
                    }
                } else {
                    // format "field" => direction
                    $this->setOrder($key, $direction);
                }
            }

            return $this;
        }

        $direction = strtolower($direction);
        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw (new Exception('Invalid order direction, direction can be only "asc" or "desc"'))
                ->addMoreInfo('field', $field)
                ->addMoreInfo('direction', $direction);
        }

        // finally set order
        $this->order[] = [$field, $direction];

        return $this;
    }

    public function addCode()
    {
        return $this->addField('code', [
            'type' => 'string',
            'required' => true,
            'mandatory' => true,
        ]);
    }

    public function addName()
    {
        return $this->addField('name', [
            'type' => 'string',
            'required' => true,
            'mandatory' => true,
        ]);
    }

    public function addDescription()
    {
        return $this->addField('description', [
            'type' => 'text',
            'default' => null,
        ]);
    }

    public function addNote()
    {
        return $this->addField('note', [
            'type' => 'text',
            'default' => null,
        ]);
    }

    public function addSequencePosition()
    {
        return $this->addField('sequence_position', [
            'type' => 'integer',
            'default' => 99,
        ]);
    }
}
