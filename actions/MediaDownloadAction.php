<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions;

use pantera\media\models\Media;
use Yii;
use yii\web\NotFoundHttpException;

class MediaDownloadAction extends MediaAction
{
    /* @var Media */
    public $model;
    /* @var bool Флаг что нужно открыть файл в браузе если это возможно */
    public $inline = false;

    public function run()
    {
        if ($this->model->issetMedia()) {
            return Yii::$app->response->sendFile($this->model->getPath(), $this->model->name, [
                'inline' => $this->inline,
            ]);
        }
        throw new NotFoundHttpException();
    }
}