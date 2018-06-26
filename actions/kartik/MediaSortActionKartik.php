<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions\kartik;

use pantera\media\actions\MediaAction;
use pantera\media\models\Media;
use Yii;

class MediaSortActionKartik extends MediaAction
{
    public function run()
    {
        foreach (Yii::$app->request->post('stack', []) as $key => $item) {
            $media = Media::find()
                ->where(['=', 'id', $item['id']])
                ->andWhere(['=', 'model', $this->model::className()])
                ->andWhere(['=', 'model_id', $this->model->getPrimaryKey()])
                ->one();
            $media->sort = $key + 1;
            $media->save();
        }
    }
}