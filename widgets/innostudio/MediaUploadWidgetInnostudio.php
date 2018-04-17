<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 4/17/18
 * Time: 11:48 AM
 */

namespace pantera\media\widgets\innostudio;


use pantera\media\models\Media;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class MediaUploadWidgetInnostudio extends Widget
{
    /* @var string Настройка к какой групе будет относится */
    public $bucket;
    /* @var array Url адрес для загрузки */
    public $urlUpload;
    /* @var array Url адрес для удаления */
    public $urlDelete;
    /* @var ActiveRecord Модель к которой относится загрузчик */
    public $model;
    /* @var array Массив опция для плагина загрузчика */
    public $pluginOptions = [];
    /* @var string Название для файлового инпута */
    public $name = 'file';

    public function run()
    {
        parent::run();
        return Html::fileInput($this->name, null, [
            'id' => $this->getId(),
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
        $this->urlUpload = Url::to($this->urlUpload);
        $this->urlDelete = Url::to($this->urlDelete);
        MediaUploadWidgetInnostudioAsset::register($this->view);
        $this->initPlugin();
    }

    /**
     * Инициализация плагина
     */
    protected function initPlugin()
    {
        $onSuccess = new JsExpression('function(result, item) {
                var data = {},
                    nameWasChanged = false;
                
                try {
                    data = JSON.parse(result);
                } catch (e) {
                    data.hasWarnings = true;
                }
                
                // get the new file name
                if(data.isSuccess && data.files[0]) {
                    nameWasChanged = item.name != data.files[0].name;
                    
                    item.name = data.files[0].name;
                }
                
                // make HTML changes
                if(nameWasChanged)
                    item.html.find(".column-title div").animate({opacity: 0}, 400);
                item.html.find(".column-actions").append("<a class=\"fileuploader-action fileuploader-action-remove fileuploader-action-success\" title=\"Remove\"><i></i></a>");
                setTimeout(function() {
                    item.html.find(".column-title div").attr("title", item.name).text(item.name).animate({opacity: 1}, 400);
                    item.html.find(".progress-bar2").fadeOut(400);
                }, 400);
                item.html.append("<input type=\"hidden\" name=\"media[]\" value=\"" + result.mediaId + "\" />");
            }');
        $onError = new JsExpression('function(item) {
                var progressBar = item.html.find(".progress-bar2");
                
                // make HTML changes
                if(progressBar.length > 0) {
                    progressBar.find("span").html(0 + "%");
                    progressBar.find(".fileuploader-progressbar .bar").width(0 + "%");
                    item.html.find(".progress-bar2").fadeOut(400);
                }
                
                item.upload.status != "cancelled" && item.html.find(".fileuploader-action-retry").length == 0 ? item.html.find(".column-actions").prepend(
                    "<a class=\"fileuploader-action fileuploader-action-retry\" title=\"Retry\"><i></i></a>"
                ) : null;
            }');
        $onProgress = new JsExpression('function(data, item) {
                var progressBar = item.html.find(".progress-bar2");
				
				// make HTML changes
                if(progressBar.length > 0) {
                    progressBar.show();
                    progressBar.find("span").html(data.percentage + "%");
                    progressBar.find(".fileuploader-progressbar .bar").width(data.percentage + "%");
                }
            }');
        $onRemove = new JsExpression('function(item) {
			$.post("' . $this->urlDelete . '", {
				id: item.data.id,
			});
		}');
        $config = [
            'inputNameBrackets' => false,
            'upload' => [
                'url' => $this->urlUpload,
                'data' => null,
                'type' => 'POST',
                'enctype' => 'multipart/form-data',
                'start' => true,
                'synchron' => true,
                'onSuccess' => $onSuccess,
                'onError' => $onError,
                'onProgress' => $onProgress,
            ],
            'onRemove' => $onRemove,
            'files' => $this->initFiles(),
        ];
        $config = ArrayHelper::merge($config, $this->pluginOptions);
        $this->view->registerJs('$("#' . $this->getId() . '").fileuploader(' . Json::encode($config) . ')');
    }

    /**
     * Подготовка уже загруженных файлов для отображения в плагине
     * @return array
     */
    protected function initFiles(): array
    {
        $preview = [];
        if (is_array($this->model->{$this->bucket})) {
            foreach ($this->model->{$this->bucket} as $media) {
                $preview[] = $this->prepareFile($media);
            }
        } elseif ($this->model->{$this->bucket}) {
            $preview = $this->prepareFile($this->model->{$this->bucket});
        }
        return $preview;
    }

    /**
     * Подготовка файла из медиа для отображения в плагине
     * @param Media $media
     * @return array
     */
    protected function prepareFile(Media $media): array
    {
        return [
            'id' => $media->id,
            'name' => $media->name,
            'size' => 1024,
            'file' => $media->getUrl(),
            'type' => $media->type,
            'data' => [
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl(),
                'id' => $media->id,
            ]
        ];
    }
}