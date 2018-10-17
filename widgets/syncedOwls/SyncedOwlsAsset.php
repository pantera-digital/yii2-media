<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 10/17/18
 * Time: 4:10 PM
 */

namespace pantera\media\widgets\syncedOwls;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SyncedOwlsAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'css/style.css',
    ];

    public $js = [
        'js/script.js',
    ];

    public $depends = [
        JqueryAsset::class,
        OwlCarouserAsset::class,
    ];
}