<?php

namespace pantera\media\behaviors;

use yii\base\Behavior;

class MediaBehavior extends Behavior
{
    public function getMainImageUrl()
    {
        return 'http://animals.sandiegozoo.org/sites/default/files/2016-09/animals_hero_ocelot.jpg';
    }
}