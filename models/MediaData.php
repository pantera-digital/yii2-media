<?php

namespace pantera\media\models;

use yii\base\Model;

class MediaData extends Model
{
    /* @var string */
    public $extension;
    /* @var string */
    public $type;
    /* @var int */
    public $size;
    /* @var string */
    public $name;
    /* @var string */
    public $path;

    public function rules()
    {
        return [
            [['extension', 'type', 'size', 'name', 'path'], 'required'],
            [['extension', 'type', 'name', 'path'], 'string'],
            [['size'], 'integer'],
        ];
    }
}
