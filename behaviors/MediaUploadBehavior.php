<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 10/25/17
 * Time: 5:30 PM
 */

namespace pantera\media\behaviors;

use pantera\media\models\Media;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\Application;

class MediaUploadBehavior extends Behavior
{
    /* @var string Название элемента в массиве который содержит идентификатор загруженой media */
    public $name = 'media';
    /* @var ActiveRecord */
    public $owner;
    /* @var array Массив груп для файлов */
    public $buckets = [];
    /* @var array Массив бакетов и их наборов медиа */
    private $_buckets = [];

    public function init()
    {
        parent::init();
        if (is_null($this->buckets)) {
            throw new InvalidConfigException('Настройка buckets обязательна');
        }
    }

    /**
     * Проверяем запрошенное свойство в бакетах
     * @param $name
     * @param bool $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if (array_key_exists($name, $this->buckets)) {
            return true;
        }
    }

    /**
     * Если запрошенное своёство есть в бакетах заполним его и вернём
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_buckets)) {
            return $this->_buckets[$name];
        }
        $owner = $this->owner;
        $query = Media::find()
            ->where(['=', 'model', $owner::className()])
            ->andWhere(['=', 'model_id', $owner->getPrimaryKey()])
            ->andWhere(['=', 'bucket', $name]);
        if (ArrayHelper::getValue($this->buckets[$name], 'multiple', false)) {
            $this->_buckets[$name] = $query->orderBy(['sort' => SORT_ASC])->all();
        } else {
            $this->_buckets[$name] = $query->orderBy(['id' => SORT_DESC])->one();
        }
        return $this->_buckets[$name];
    }

    /**
     * Подписываемся на события создания записи и удаления
     * @return array
     */
    public function events()
    {
        $events[ActiveRecord::EVENT_AFTER_DELETE] = 'eventAfterDelete';
        $events[ActiveRecord::EVENT_AFTER_INSERT] = 'save';
        $events[ActiveRecord::EVENT_AFTER_UPDATE] = 'save';
        return $events;
    }

    /**
     * После удаления модели удаляется и загруженый файл
     */
    public function eventAfterDelete()
    {
        Media::deleteAll([
            'AND',
            ['=', 'model', $this->owner::className()],
            ['=', 'model_id', $this->owner->getPrimaryKey()],
        ]);
    }

    /**
     * Сохранения файлов к модели
     */
    public function save()
    {
        if (Yii::$app instanceof Application) {
            Media::updateAll([
                'model_id' => $this->owner->getPrimaryKey(),
            ], [
                'AND',
                ['=', 'model', $this->owner::className()],
                ['IN', 'id', Yii::$app->request->post($this->name, [])],
            ]);
        }
    }
}