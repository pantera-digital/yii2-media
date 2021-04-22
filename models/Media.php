<?php

namespace pantera\media\models;

use himiklab\thumbnail\EasyThumbnailImage;
use pantera\media\Module;
use SplFileInfo;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use function array_key_exists;
use function array_merge;
use function copy;
use function file_exists;
use function is_array;

/**
 * @property integer $id
 * @property string $file
 * @property string $name
 * @property string $type
 * @property integer $size
 * @property string $model
 * @property integer $model_id
 * @property string $created_at
 * @property string $bucket
 * @property integer $sort
 */
class Media extends ActiveRecord
{
    /* @var UploadedFile|array|null */
    public $media;
    /* @var array Динамичиские правила валидации для модели добавляются через акшен в контролере */
    private $_dynamicMediaRules = [];

    public function fields()
    {
        return [
            'id',
            'name',
            'size',
            'type',
            'url',
        ];
    }

    /**
     * Установить динамические правила валидации для модели
     * @param array|null $dynamicMediaRules
     */
    public function setDynamicFileRules($dynamicMediaRules)
    {
        $this->_dynamicMediaRules = $dynamicMediaRules;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Module::getInstance()->tableName;
    }

    /**
     * Проверяет сушествование физического файла
     * @return bool
     */
    public function issetMedia()
    {
        return file_exists(Yii::getAlias('@mediaFileAlias') . $this->file);
    }

    /**
     * Получить ссылку картинку по переданным размерам и типу трансформации
     * если отсутчстует размер файла или это svg то отдаём ссылку на оригинал
     * @param null $width Ширина изображения
     * @param null $height Высота изображения
     * @param boolean $inset Тип трансформации изображения
     * @return string
     * @throws \himiklab\thumbnail\FileNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function image($width = null, $height = null, $inset = true)
    {
        if ($this->issetMedia() === false) {
            return '';
        }
        if (!$width || !$height || strpos($this->file, '.svg')) {
            return $this->getUrl();
        }
        return EasyThumbnailImage::thumbnailFileUrl(
            $this->getPath(),
            $width,
            $height,
            $inset ? EasyThumbnailImage::THUMBNAIL_INSET : EasyThumbnailImage::THUMBNAIL_OUTBOUND,
            100
        );
    }

    /**
     * Получить ссылку на файл
     * @return string
     */
    public function getUrl()
    {
        return Yii::getAlias('@mediaUrlAlias') . $this->file;
    }

    /**
     * Получить абсолютный физичиский путь к файлу
     */
    public function getPath()
    {
        return Yii::getAlias('@mediaFileAlias') . $this->file;
    }

    /**
     * После удаления модели удалим и файл
     */
    public function afterDelete()
    {
        $this->deleteFile();
        parent::afterDelete();
    }

    /**
     * Удаление файла с диска
     */
    private function deleteFile()
    {
        if ($this->issetMedia()) {
            unlink($this->getPath());
        }
    }

    /**
     * Сохранить файл в медиа
     * @param UploadedFile $media
     * @param string $modelName
     * @param int|null $modelId
     * @return string|Media|null
     */
    public function linkMedia(UploadedFile $media, string $modelName, $modelId)
    {
        $this->media = $media;
        $this->model = $modelName;
        $this->model_id = $modelId;
        $this->sort = $this->getNextSortPositionInBucket();
        if ($this->validate() && $this->save()) {
            return $this;
        }
        if ($this->getFirstErrors()) {
            return current($this->getFirstErrors());
        }
        return null;
    }

    /**
     * Получить следуюшию позицию для сортировки внутри бакета
     * @return int
     */
    public function getNextSortPositionInBucket()
    {
        $maxSort = 0;
        if ($this->model && $this->bucket) {
            $query = Media::find()
                ->where(['=', 'model', $this->model])
                ->andWhere(['=', 'bucket', $this->bucket]);
            if (is_null($this->model_id)) {
                $query->andWhere(['IS', 'model_id', null]);
            } else {
                $query->andWhere(['=', 'model_id', $this->model_id]);
            }
            $maxSort = $query
                ->max('sort');
        }
        return $maxSort + 1;
    }

    /**
     * Перед сохранением модели сохраняем файл
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->media) {
            $module = Module::getInstance();

            if ($this->media instanceof UploadedFile) {
                if (!$this->isNewRecord) {
                    $this->deleteFile();
                }

                if(!$module->isAvailableWebp && $this->media->extension == 'webp') {
                    $fileName = uniqid('', true) . '.jpeg';

                    $rawImg = imagecreatefromwebp($this->media->tempName);
                    $path = Yii::getAlias('@mediaFileAlias') . $fileName;
                    imagejpeg($rawImg, $path);
                    imagedestroy($rawImg);

                    $jpeg = new SplFileInfo($path);
                    $this->type = $jpeg->getType();
                    $this->size = $jpeg->getSize();
                    $this->name = $this->media->name;
                } else {
                    $fileName = uniqid('', true) . '.' . $this->media->extension;
                    $this->type = $this->media->type;
                    $this->size = $this->media->size;
                    $this->name = $this->media->name;
                    $this->media->saveAs(Yii::getAlias('@mediaFileAlias') . $fileName);
                }

                $this->file = $fileName;

            } elseif (is_array($this->media) && array_key_exists('file', $this->media)) {
                $file = new SplFileInfo($this->media['file']);

                if(!$module->isAvailableWebp && $file->getExtension() == 'webp') {
                    $rawImg = imagecreatefromwebp($this->media['file']);
                    $fileName = uniqid('', true) . '.jpeg';
                    $path = Yii::getAlias('@mediaFileAlias') . $fileName;
                    imagejpeg($rawImg, $path);
                    imagedestroy($rawImg);

                    $jpeg = new SplFileInfo($path);
                    $this->file = $fileName;
                    $this->type = $jpeg->getType();
                    $this->size = $jpeg->getSize();
                } else {
                    $fileName = uniqid('', true) . '.' . $file->getExtension();
                    $this->file = $fileName;
                    $this->type = $file->getType();
                    $this->size = $file->getSize();
                }
                $this->name = ArrayHelper::getValue($this->media, 'name', $fileName);

                copy($this->media['file'], $this->getPath());
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            'model_id' => ['model_id', 'integer'],
            'size' => ['size', 'integer'],
            'model' => ['model', 'string', 'max' => 255],
            'name' => ['name', 'string', 'max' => 255],
            'bucket' => ['bucket', 'string', 'max' => 255],
            'file' => ['file', 'string'],
            'type' => ['type', 'string', 'max' => 32],
            'created_at' => ['created_at', 'safe'],
            'media' => ['media', 'file', 'skipOnEmpty' => true],
            'sort' => ['sort', 'integer', 'integerOnly' => true],
        ];
        $rules = array_merge($rules, $this->_dynamicMediaRules);
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file' => 'File',
            'name' => 'Name',
            'type' => 'Type',
            'size' => 'Size',
            'model' => 'Model',
            'model_id' => 'Model Id',
            'created_at' => 'Created At',
        ];
    }
}
