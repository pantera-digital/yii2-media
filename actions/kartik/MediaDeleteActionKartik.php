<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions\kartik;

use pantera\media\actions\MediaAction;
use yii\web\NotFoundHttpException;

class MediaDeleteActionKartik extends MediaAction
{
    public function run()
    {
        if (is_null($this->model)) {
            throw new NotFoundHttpException();
        };
        $this->model->delete();
        return $this->controller->asJson([
            'status' => 'success',
        ]);
    }
}