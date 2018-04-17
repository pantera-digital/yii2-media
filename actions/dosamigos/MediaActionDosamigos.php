<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions\dosamigos;

use pantera\media\models\Media;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\Url;

class MediaActionDosamigos extends Action
{
    /* @var Media|\Closure */
    public $model;

    public function beforeRun()
    {
        if ($this->model instanceof \Closure) {
            $this->model = call_user_func($this->model);
        }
        if (is_null($this->model)) {
            throw new InvalidConfigException('Property {model} required');
        }
        return parent::beforeRun();
    }

    /**
     * Подготавливает данные медия для отдачи их плагину
     * @param Media $media
     * @param array $deleteAction
     * @return array
     */
    protected function prepareFileData(Media $media, array $deleteAction): array
    {
        $url = $media->getUrl();
        $thumb = false;
        $extension = substr($media->file, strrpos($media->file, '.') + 1);
        if (in_array($extension, ['png', 'jpg', 'gif', 'jpeg'])) {
            $thumb = $url;
        }
        $item = [
            'id' => $media->id,
            'name' => $media->name,
            'size' => $media->size,
            'url' => $url,
            'thumbnailUrl' => $thumb,
        ];
        if ($deleteAction) {
            $deleteAction['id'] = $media->id;
            $item['deleteUrl'] = Url::to($deleteAction);
            $item['deleteType'] = 'POST';
        }
        return $item;
    }
}