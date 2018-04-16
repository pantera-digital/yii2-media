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

class MediaDownloadAction extends MediaAction
{
    public function run()
    {
        return Yii::$app->response->sendFile($this->model->getPath(), $this->model->name);
    }
}