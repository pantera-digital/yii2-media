<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 4/16/18
 * Time: 12:16 PM
 */

namespace pantera\media\widgets\dosamigos;


use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\db\ActiveRecord;

class MediaUploadWidgetDosamigos extends Widget
{
    /* @var string Настройка к какой групе будет относится */
    public $bucket;
    /* @var array Url адрес для загрузки */
    public $urlUpload;
    /* @var array Url адрес для удаления */
    public $urlDelete;
    /* @var ActiveRecord Модель к которой относится загрузчик */
    public $model;
    /* @var array Массив опций для загрузчика */
    public $options = [];
    /* @var array Массив опция для плагина загрузчика */
    public $pluginOptions = [];
    /* @var string Название для файлового инпута */
    public $name = 'file';

    public function run()
    {
        parent::run();
        return $this->render('index', [
            'urlUpload' => $this->urlUpload,
            'model' => $this->model,
            'options' => $this->options,
            'pluginOptions' => $this->pluginOptions,
            'name' => $this->name,
        ]);
    }

    public function init()
    {
        parent::init();
        if (is_null($this->urlUpload)) {
            throw new InvalidConfigException('Настройка urlUpload обязательна');
        }
        if (is_null($this->urlDelete)) {
            throw new InvalidConfigException('Настройка urlDelete обязательна');
        }
        if (is_null($this->model)) {
            throw new InvalidConfigException('Настройка model обязательна');
        }
        if (is_null($this->bucket)) {
            throw new InvalidConfigException('Настройка bucket обязательна');
        }
        $this->urlUpload['bucket'] = $this->bucket;
    }
}