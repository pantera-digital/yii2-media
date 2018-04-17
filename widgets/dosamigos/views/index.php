<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 4/16/18
 * Time: 12:17 PM
 */

use dosamigos\fileupload\FileUploadUI;
use pantera\media\models\Media;
use yii\db\ActiveRecord;
use yii\web\View;

/* @var $this View */
/* @var $urlUpload array */
/* @var $model ActiveRecord */
/* @var $name string */

echo FileUploadUI::widget([
    'downloadTemplateView' => '@pantera/media/widgets/dosamigos/views/_files',
    'name' => $name,
    'load' => !$model->isNewRecord,
    'url' => $urlUpload,
    'gallery' => true,
    'clientOptions' => [
        'autoUpload' => true,
    ],
]);
