# yii2-media
Module for media resources (images, videos, files) management for any entities


## Install
Preferred way via composer:
```
$ composer require pantera-digital/yii2-media "dev-master"
```
## Настройка
Подключить модуль
```
'modules' => [
    'media' => [
        'class' => pantera\media\Module::className(),
        'permissions' => ['admin'],
    ],
],
```
Возможные настройки модуля

```
permissions => ['@'] //Массив ролей RBAC которым доступна админка
mediaUrlAlias => '@web/uploads/media/' //Алиас для доступа к файлу по url
mediaFileAlias => '@webroot/uploads/media/' //Алиас полного пути до файла
```
Модуль установит эти алиасы
Необходимо добавить ашкены в контролер
```
public function actions()
{
    return [
        'file-upload' => [
            'class' => \pantera\media\actions\MediaUploadAction::className(),
            'model' => function () {
                if (Yii::$app->request->get('id')) {
                    return $this->findModel(Yii::$app->request->get('id'));
                } else {
                    return new Test();
                }
            }
        ],
        'file-delete' => [
            'class' => \pantera\media\actions\MediaUploadAction::className(),
            'model' => function () {
                return \pantera\media\models\Media::findOne(Yii::$app->request->get('id'));
            }
        ],
    ];
}
```
В модель добавить поведение
```
public function behaviors()
{
    return [
        [
            'class' => \pantera\media\behaviors\MediaUploadBehavior::className(),
            'buckets' => [
                'mediaMain' => [],
                'mediaOther' => [
                    'multiple' => true,
                ],
            ],
        ],
    ];
}
```
Во вью подключить виджет загрузки
```
<?= pantera\media\widgets\kartik\MediaUploadWidgetKartik::widget([
    'model' => $model,
    'bucket' => 'mediaMain',
    'urlUpload' => ['file-upload', 'id' => $model->id],
    'urlDelete' => ['file-delete'],
]) ?>
<?= pantera\media\widgets\kartik\MediaUploadWidgetKartik::widget([
    'model' => $model,
    'bucket' => 'mediaOther',
    'urlUpload' => ['file-upload', 'id' => $model->id],
    'urlDelete' => ['file-delete'],
    'options' => [
        'multiple' => true,
    ],
]) ?>
```