<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 4/16/18
 * Time: 12:17 PM
 */

use kartik\file\FileInput;
use pantera\media\models\Media;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $urlUpload array */
/* @var $model ActiveRecord */
/* @var $media Media */
/* @var $options array */
/* @var $pluginOptions array */
/* @var $pluginEvents array */
/* @var $name string */
$defaultPluginOptions = [
    'uploadUrl' => Url::to($urlUpload),
    'maxFileSize' => 2800,
    'overwriteInitial' => false,
    'initialPreviewAsData' => true,
    'otherActionButtons' => '<input type="hidden" name="media[]" value="mediaId" class="media-id" />',
    'fileActionSettings' => [
        'showZoom' => false,
    ],
];
echo FileInput::widget([
    'name' => $name,
    'options' => $options,
    'pluginOptions' => ArrayHelper::merge($defaultPluginOptions, $pluginOptions),
    'pluginEvents' => $pluginEvents,
]);
