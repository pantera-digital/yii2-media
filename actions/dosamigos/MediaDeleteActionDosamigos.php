<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions\dosamigos;

use pantera\media\models\Media;
use yii\web\NotFoundHttpException;

class MediaDeleteActionDosamigos extends MediaActionDosamigos
{
    public function run()
    {
        if (is_null($this->model)) {
            throw new NotFoundHttpException();
        };
        $this->model->delete();
        $files = [];
        $_files = Media::find()
            ->where(['=', 'model_id', $this->model->model_id])
            ->andWhere(['=', 'bucket', $this->model->bucket])
            ->all();
        foreach ($_files as $file) {
            $files[] = $this->prepareFileData($file, [$this->controller->action->id]);
        }
        return $this->controller->asJson(['files' => $files]);
    }
}