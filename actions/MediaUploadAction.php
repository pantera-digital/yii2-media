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

class MediaUploadAction extends MediaAction
{
    /* @var string Название файла */
    public $name = 'file';

    public function run()
    {
        $file = UploadedFile::getInstanceByName($this->name);
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
    }
}