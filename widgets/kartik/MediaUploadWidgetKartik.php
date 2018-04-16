<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 4/16/18
 * Time: 12:16 PM
 */

namespace pantera\media\widgets\kartik;


use pantera\media\models\Media;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class MediaUploadWidgetKartik extends Widget
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
        if ($this->bucket) {
            $this->urlUpload['bucket'] = $this->bucket;
        }
        $this->options['id'] = $this->getId();
        $this->initPluginOptions();
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
    }

    /**
     * Инициализация всех превьюшек
     */
    private function initPluginOptions()
    {
        $preview = [];
        if (is_array($this->model->{$this->bucket})) {
            foreach ($this->model->{$this->bucket} as $media) {
                $_preview = $this->initPluginOptionsPreview($media);
                $preview = ArrayHelper::merge($preview, $_preview);
            }
        } elseif ($this->model->{$this->bucket}) {
            $preview = $this->initPluginOptionsPreview($this->model->{$this->bucket});
        }
        $this->pluginOptions = ArrayHelper::merge($this->pluginOptions, $preview);
    }

    /**
     * Инициализация конкретной превьюшки
     * @param Media $media
     * @return array
     * @throws InvalidConfigException
     * @throws \himiklab\thumbnail\FileNotFoundException
     */
    private function initPluginOptionsPreview(Media $media)
    {
        $preview = [];
        $this->urlDelete['id'] = $media->getPrimaryKey();
        $preview['initialPreview'][] = $media->image();
        $preview['initialPreviewConfig'][] = [
            'caption' => $media->name,
            'size' => $media->size,
            'url' => Url::to($this->urlDelete),
        ];
        $preview['initialPreviewThumbTags'][] = [
            'mediaId' => $media->id,
        ];
        return $preview;
    }
}