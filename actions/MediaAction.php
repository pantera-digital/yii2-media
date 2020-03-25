<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions;

use Closure;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

class MediaAction extends Action
{
    /* @var ActiveRecord|Closure */
    public $model;
    /* @var Closure|null */
    public $getPrimaryKey;

    public function beforeRun()
    {
        if ($this->model instanceof Closure) {
            $this->model = call_user_func($this->model);
        }
        if (is_null($this->model)) {
            throw new InvalidConfigException('Property {model} required');
        }
        return parent::beforeRun();
    }

    public function getPrimaryKey(): ?string
    {
        if ($this->getPrimaryKey) {
            return call_user_func($this->getPrimaryKey, $this->model);
        }
        return $this->model->getPrimaryKey();
    }
}
