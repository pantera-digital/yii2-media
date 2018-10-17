<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 10/10/18
 * Time: 3:22 PM
 */

namespace pantera\media\widgets\syncedOwls;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class FancyboxAsset extends AssetBundle
{
    public $sourcePath = '@bower/fancybox/dist';

    public $css = [
        'jquery.fancybox.css',
    ];

    public $js = [
        'jquery.fancybox.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}