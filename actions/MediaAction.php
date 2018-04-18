<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions;

use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

class MediaAction extends Action
{
    /* @var ActiveRecord|\Closure */
    public $model;

    public function init()
    {
        parent::init();
        if ($this->model instanceof \Closure) {
            $this->model = call_user_func($this->model);
        }
        if (is_null($this->model)) {
            throw new InvalidConfigException('Property {model} required');
        }
    }
}