<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions;

use pantera\media\models\Media;
use yii\base\Action;
use yii\base\InvalidConfigException;

class MediaAction extends Action
{
    /* @var Media|\Closure */
    public $model;

    public function beforeRun()
    {
        if ($this->model instanceof \Closure) {
            $this->model = call_user_func($this->model);
        }
        if (is_null($this->model)) {
            throw new InvalidConfigException('Property {model} required');
        }
        return parent::beforeRun();
    }
}