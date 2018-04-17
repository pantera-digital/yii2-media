<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions\dosamigos;

use pantera\media\models\Media;
use Yii;
use yii\web\UploadedFile;

class MediaUploadActionDosamigos extends MediaActionDosamigos
{
    /* @var string Название файла */
    public $name = 'file';
    /* @var array|null Ссылка для удаления */
    public $deleteAction;

    public function run()
    {
        $file = UploadedFile::getInstanceByName($this->name);
        if ($file) {
            $media = new Media();
            $media->bucket = Yii::$app->request->get('bucket');
            $result = $media->linkMedia($file, $this->model::className(), $this->model->getPrimaryKey());
            if (is_object($result)) {
                $res['files'][] = $this->prepareFileData($result, $this->deleteAction);
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
                ->andWhere(['=', 'bucket', Yii::$app->request->get('bucket')])
                ->all();
            foreach ($_files as $file) {
                $files[] = $this->prepareFileData($file, $this->deleteAction);
            }
            return $this->controller->asJson(['files' => $files]);
        }
    }
}