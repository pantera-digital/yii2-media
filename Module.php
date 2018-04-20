<?php

namespace pantera\media;

use Yii;
use yii\web\Application;

class Module extends \yii\base\Module
{
    /* @var array Массив ролей которым доступна админка */
    public $permissions = ['@'];
    /* @var string Название таблицы */
    public $tableName = '{{media}}';
    public $mediaUrlAlias = '@web/uploads/media/';
    public $mediaFileAlias = '@webroot/uploads/media/';

    public function init()
    {
        parent::init();
        if (Yii::$app instanceof Application) {
            Yii::setAlias('@mediaFileAlias', $this->mediaFileAlias);
            Yii::setAlias('@mediaUrlAlias', $this->mediaUrlAlias);
        }
    }
}