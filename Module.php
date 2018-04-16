<?php

namespace pantera\media;

use Yii;

class Module extends \yii\base\Module
{
    /* @var array Массив ролей которым доступна админка */
    public $permissions = ['@'];
    public $mediaUrlAlias = '@web/uploads/media/';
    public $mediaFileAlias = '@webroot/uploads/media/';

    public function init()
    {
        parent::init();
        Yii::setAlias('@mediaFileAlias', $this->mediaFileAlias);
        Yii::setAlias('@mediaUrlAlias', $this->mediaUrlAlias);
    }
}