<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 5/18/18
 * Time: 12:35 PM
 */

namespace pantera\media\components\api;


use pantera\media\models\Media;
use yii\base\Component;
use yii\db\ActiveRecord;

class MediaApi extends Component
{
    /* @var Media */
    private $_media;

    /**
     * Инициализация создания нового
     * @param ActiveRecord $owner
     * @param string $bucket
     * @return MediaApi
     */
    public function initNewMedia(ActiveRecord $owner, string $bucket): MediaApi
    {
        $this->_media = new Media();
        $this->_media->model = $owner::className();
        $this->_media->model_id = $owner->getPrimaryKey();
        $this->_media->bucket = $bucket;
        return $this;
    }

    public function setFile(array $file): MediaApi
    {
        $this->_media->media = $file;
        return $this;
    }

    public function create(): Media
    {
        $this->_media->save();
        return $this->_media;
    }
}