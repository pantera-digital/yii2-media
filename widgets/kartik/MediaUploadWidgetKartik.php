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
use yii\web\JsExpression;

class MediaUploadWidgetKartik extends Widget
{
    /* @var string Настройка к какой групе будет относится */
    public $bucket;
    /* @var array Url адрес для загрузки */
    public $urlUpload;
    /* @var array Url адрес для удаления */
    public $urlDelete;
    /* @var array Url адрес для сохранения сортировки */
    public $urlSort;
    /* @var ActiveRecord Модель к которой относится загрузчик */
    public $model;
    /* @var array Массив опций для загрузчика */
    public $options = [];
    /* @var array Массив опция для плагина загрузчика */
    public $pluginOptions = [];
    /* @var string Название для файлового инпута */
    public $name = 'file';
    /* @var array Массив обработчиков событий */
    public $pluginEvents = [];

    public function run()
    {
        parent::run();
        return $this->render('index', [
            'urlUpload' => $this->urlUpload,
            'model' => $this->model,
            'options' => $this->options,
            'pluginOptions' => $this->pluginOptions,
            'pluginEvents' => $this->pluginEvents,
            'name' => $this->name,
        ]);
    }

    public function init()
    {
        parent::init();
        $this->options['id'] = $this->getId();
        $this->initPluginOptions();
        $this->initPluginEvents();
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

    /**
     * Инициализация обработчиков событий плагина
     */
    protected function initPluginEvents()
    {
        $eventFileUploaded = <<<JS
            function(e, data, previewId){
                var input = $(e.target);
                $("#" + previewId).find('.media-id').val(data.response.mediaId);
            }
JS;
        $pluginEvents = [
            'fileuploaded' => new JsExpression($eventFileUploaded),
        ];
        if ($this->urlSort) {
            $sortUrl = Url::to($this->urlSort);
            $eventFileSorted = <<<JS
                function(e, params){
                    $.post("{$sortUrl}", params);
                }
JS;
            $pluginEvents['filesorted'] = new JsExpression($eventFileSorted);
        }
        $this->pluginEvents = ArrayHelper::merge($this->pluginEvents, $pluginEvents);
    }

    /**
     * Инициализация всех превьюшек
     */
    protected function initPluginOptions()
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
    protected function initPluginOptionsPreview(Media $media)
    {
        $preview = [];
        $this->urlDelete['id'] = $media->getPrimaryKey();
        $preview['initialPreview'][] = $media->image();
        $preview['initialPreviewConfig'][] = [
            'caption' => $media->name,
            'size' => $media->size,
            'url' => Url::to($this->urlDelete),
            'id' => $media->id,
        ];
        $preview['initialPreviewThumbTags'][] = [
            'mediaId' => $media->id,
        ];
        return $preview;
    }
}