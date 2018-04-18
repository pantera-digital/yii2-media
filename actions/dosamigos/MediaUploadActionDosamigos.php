<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions\dosamigos;

use pantera\media\actions\MediaUploadTrait;
use pantera\media\models\Media;
use Yii;
use yii\web\UploadedFile;
use function array_walk;

class MediaUploadActionDosamigos extends MediaActionDosamigos
{
    /* @var array|null Ссылка для удаления */
    public $deleteAction;

    use MediaUploadTrait;

    public function run()
    {
        $file = UploadedFile::getInstanceByName($this->name);
        if ($file) {
            $res = $this->upload($file);
            return $this->controller->asJson($res);
        } elseif (Yii::$app->request->get('id')) {
            $files = Media::find()
                ->where(['=', 'model_id', Yii::$app->request->get('id')])
                ->andWhere(['=', 'bucket', Yii::$app->request->get('bucket')])
                ->all();
            array_walk($files, function (&$file) {
                $file = $this->prepareFileData($file, $this->deleteAction);
            });
            return $this->controller->asJson(['files' => $files]);
        }
    }
}