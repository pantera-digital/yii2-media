<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 10/17/18
 * Time: 3:50 PM
 */

namespace pantera\media\widgets\syncedOwls;


use pantera\media\models\Media;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\JsExpression;

class SyncedOwls extends Widget
{
    /* @var Media[] */
    public $models;
    /* @var array Массив опций для контейнера */
    public $containerOptions = [];
    /* @var bool Нужно ли показывать список миниатюр */
    public $showThumbs = true;

    public function run()
    {
        parent::run();
        $content = $this->render('index', [
            'models' => $this->models,
            'showThumbs' => $this->showThumbs,
        ]);
        return Html::tag('div', $content, $this->containerOptions);
    }

    public function init()
    {
        parent::init();
        Html::addCssClass($this->containerOptions, 'synced-carousel');
        $this->containerOptions['id'] = $this->getId();
        SyncedOwlsAsset::register($this->view);
        $this->view->registerJs(new JsExpression('syncedOwls("' . $this->getId() . '")'));
    }
}