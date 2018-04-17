<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 4/17/18
 * Time: 11:49 AM
 */

namespace pantera\media\widgets\innostudio;


use yii\web\AssetBundle;

class MediaUploadWidgetInnostudioAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'css/jquery.fileuploader.min.css',
    ];

    public $js = [
        'js/jquery.fileuploader.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}