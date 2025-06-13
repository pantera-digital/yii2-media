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

trait MediaUploadTrait
{
    /* @var array Массив правил который будут добавлены к модели медиа */
    public $rules = [];
    /* @var string Название файла */
    public $name = 'file';

    /**
     * Сохранение файла
     * @param UploadedFile $file
     * @return array
     */
    protected function upload(UploadedFile $file): array
    {
        $media = new Media();
        $media->bucket = Yii::$app->request->get('bucket');
        $media->setDynamicFileRules($this->rules);
        $result = $media->linkMedia($file, $this->model->behaviors['media']->modelKey, $this->model->getPrimaryKey());
        if (is_object($result)) {
            return [
                'status' => 'success',
                'name' => $file->name,
                'mediaId' => $result->id,
                'media' => $result,
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $result,
            ];
        }
    }
}
