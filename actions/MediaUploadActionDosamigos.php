<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions;

use pantera\media\models\Media;
use Yii;
use yii\web\UploadedFile;

class MediaUploadActionDosamigos extends MediaAction
{
    /* @var string Название файла */
    public $name = 'file';

    public function run()
    {
        $file = UploadedFile::getInstanceByName($this->name);
        if ($file) {
            $media = new Media();
            $media->bucket = Yii::$app->request->get('bucket');
            $result = $media->linkMedia($file, $this->model::className(), $this->model->getPrimaryKey());
            if (is_object($result)) {
                $res = [
                    'status' => 'success',
                    'name' => $file->name,
                    'mediaId' => $result->id,
                ];
            } else {
                $res = [
                    'status' => 'error',
                    'message' => $result,
                ];
            }
            return $this->controller->asJson($res);
        } elseif (Yii::$app->request->get('id')) {
            $files = [];
            $_files = Media::find()
                ->where(['=', 'model_id', Yii::$app->request->get('id')])
                ->all();
            foreach ($_files as $file) {
                $url = $file->getUrl();
                $thumb = false;
                $extension = substr($file->file, strrpos($file->file, '.') + 1);
                if (in_array($extension, ['png', 'jpg', 'gif', 'jpeg'])) {
                    $thumb = $url;
                }
                $item = [
                    'name' => $file->name,
                    'size' => $file->size,
                    'url' => $url,
                    'thumbnailUrl' => $thumb,
                    'deleteUrl' => '/reserves/file/delete?id=' . $file->id,
                    'deleteType' => 'POST',
                ];
                $files[] = $item;
            }
            return $this->controller->asJson(['files' => $files]);
        }
    }
}