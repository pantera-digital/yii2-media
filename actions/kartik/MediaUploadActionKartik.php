<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 11/29/17
 * Time: 1:40 PM
 */

namespace pantera\media\actions\kartik;

use pantera\media\actions\MediaAction;
use pantera\media\actions\MediaUploadTrait;
use yii\web\UploadedFile;

class MediaUploadActionKartik extends MediaAction
{
    use MediaUploadTrait;

    public function run()
    {
        $file = UploadedFile::getInstanceByName($this->name);
        $res = $this->upload($file);
        return $this->controller->asJson($res);
    }
}