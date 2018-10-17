<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 9/26/18
 * Time: 11:15 AM
 */

namespace pantera\media\widgets\syncedOwls;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class OwlCarouserAsset extends AssetBundle
{
    public $sourcePath = '@bower/owl.carousel/dist';

    public $css = [
        'assets/owl.carousel.min.css'
    ];

    public $js = [
        'owl.carousel.min.js'
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}