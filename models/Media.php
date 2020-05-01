<?php

namespace pantera\media\models;

use himiklab\thumbnail\EasyThumbnailImage;
use himiklab\thumbnail\FileNotFoundException;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;
use pantera\media\Module;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\httpclient\Exception;
use yii\web\UploadedFile;
use function array_merge;
use function copy;
use function file_exists;

/**
 * @property integer $id
 * @property string $file
 * @property string $name
 * @property string $type
 * @property integer $size
 * @property string $model
 * @property string $model_id
 * @property string $created_at
 * @property string $bucket
 * @property integer $sort
 */
class Media extends ActiveRecord
{
    /* @var UploadedFile|array|null */
    public $media;
    /* @var array Динамичиские правила валидации для модели добавляются через акшен в контролере */
    private $dynamicMediaRules = [];

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
        $this->dynamicMediaRules = $dynamicMediaRules;
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
     * @throws FileNotFoundException
     * @throws InvalidConfigException
     * @throws Exception
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
     * @param UploadedFile $media
     * @param ActiveRecord $model
     * @param int|string $modelId
     * @return string|Media|null
     */
    public function linkMediaNew(UploadedFile $media, ActiveRecord $model, $modelId)
    {
        $this->model = get_class($model);
        $this->model_id = $modelId;
        $this->sort = $this->getNextSortPositionInBucket();
        $this->saveMedia($media, $model);
        if (!$this->validate() || !$this->save()) {
            return current($this->getFirstErrors());
        }
        return $this;
    }

    private function saveMedia(UploadedFile $media, ActiveRecord $model): bool
    {
//        var_dump($media);
//        /* @var $mediaUploadBehavior MediaUploadBehavior */
//        $mediaUploadBehavior = $model->getBehavior('media');
//        if ($mediaUploadBehavior) {
//            var_dump($mediaUploadBehavior->buckets);
//        }
//        var_dump($model);
//        die();
        $mediaData = new MediaData([
            'extension' => $media->extension,
            'type' => $media->type,
            'size' => $media->size,
            'name' => $media->name,
            'path' => $media->tempName,
        ]);
        return $this->saveFile($mediaData);
    }

    private function saveFile(MediaData $mediaData): bool
    {
        if (!$mediaData->validate()) {
            return false;
        }
        $this->file = uniqid('', true).'.'.$mediaData->extension;
        $this->type = $mediaData->type;
        $this->size = $mediaData->size;
        $this->name = $mediaData->name;
        return copy($mediaData->path, $this->getPath());
    }

    public function resize(int $width, int $height, int $quality = 100): string
    {
        ImageManagerStatic::configure(array('driver' => 'imagick'));
        $img = ImageManagerStatic::make($this->getPath());
        $result = $img->resize($width, $height, function ($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        return $this->saveThumb($result, $quality);
    }

    private function saveThumb(Image $image, int $quality = 100): string
    {
        $path = $this->getThumbPath($image, $quality);
        if (!file_exists($path)) {
            $image->save($path, $quality);
        }
        return $this->getThumbUrl($image, $quality);
    }

    private function getThumbPath(Image $image, int $quality): string
    {
        $path = $this->getThumbBasePath($image, $quality);
        $path = Yii::$app->assetManager->basePath . "/thumbs/{$path}";
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return "{$path}/{$this->name}";
    }

    private function getThumbUrl(Image $image, int $quality): string
    {
        $path = $this->getThumbBasePath($image, $quality);
        return Yii::$app->assetManager->baseUrl . "/thumbs/{$path}/{$this->name}";
    }

    private function getThumbBasePath(Image $image, int $quality): string
    {
        $path = strtolower(str_replace('\\', '-', $this->model));
        $path .= "/{$this->model_id}/{$image->width()}-{$image->height()}-{$quality}";
        return str_replace(' ', '', $path);
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

//    /**
//     * Перед сохранением модели сохраняем файл
//     * @param bool $insert
//     * @return bool
//     */
//    public function beforeSave($insert)
//    {
//        if ($this->media) {
//            if ($this->media instanceof UploadedFile) {
//                if (!$this->isNewRecord) {
//                    $this->deleteFile();
//                }
//                if ($this->media->extension == 'webp') {
//                    $fileName = uniqid('', true).'.jpeg';
//                    $rawImg = imagecreatefromwebp($this->media->tempName);
//                    imagejpeg($rawImg, Yii::getAlias('@mediaFileAlias').$fileName);
//                    imagedestroy($rawImg);
//                    $jpeg = new SplFileObject($fileName);
//                    $this->type = $jpeg->getType();
//                    $this->size = $jpeg->getSize();
//                    $this->name = $this->media->name;
//                } else {
//                    Image::configure(array('driver' => 'imagick'));
//                    $img = Image::make($this->media->tempName);
//                    $result = $img->resize(800, 800, function ($constraint) {
//                        $constraint->aspectRatio();
//                    });
//                    $result->save('awg80.'.$this->media->extension, 90);
//                    var_dump($this->media);
//                    die();
//                    $fileName = uniqid('', true).'.'.$this->media->extension;
//                    $this->type = $this->media->type;
//                    $this->size = $this->media->size;
//                    $this->name = $this->media->name;
//                    $this->media->saveAs(Yii::getAlias('@mediaFileAlias').$fileName);
//                }
//                $this->file = $fileName;
//            } elseif (is_array($this->media) && array_key_exists('file', $this->media)) {
//                $file = new SplFileInfo($this->media['file']);
//                if ($file->getExtension() == 'webp') {
//                    $rawImg = imagecreatefromwebp($this->media['file']);
//                    $fileName = uniqid('', true) . '.jpeg';
//                    imagejpeg($rawImg, Yii::getAlias('@mediaFileAlias') . $fileName);
//                    imagedestroy($rawImg);
//                    $jpeg = $file = new SplFileInfo(Yii::getAlias('@mediaFileAlias') . $fileName);
//                    $this->file = $fileName;
//                    $this->type = $jpeg->getType();
//                    $this->size = $jpeg->getSize();
//                    $this->name = ArrayHelper::getValue($this->media, 'name', $fileName);
//                } else {
//                    $fileName = uniqid('', true) . '.' . $file->getExtension();
//                    $this->file = $fileName;
//                    $this->type = $file->getType();
//                    $this->size = $file->getSize();
//                    $this->name = ArrayHelper::getValue($this->media, 'name', $fileName);
//                }
//                copy($this->media['file'], $this->getPath());
//            }
//        }
//        return parent::beforeSave($insert);
//    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            'model_id' => ['model_id', 'string', 'max' => 255],
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
        $rules = array_merge($rules, $this->dynamicMediaRules);
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
