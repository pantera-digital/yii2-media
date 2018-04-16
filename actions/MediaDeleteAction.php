<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions;

use yii\web\NotFoundHttpException;

class MediaDeleteAction extends MediaAction
{
    public function run()
    {
        if (is_null($this->model)) {
            throw new NotFoundHttpException();
        };
        $this->model->delete();
    }
}