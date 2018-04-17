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
### Настройка загрузчика от Kartik
Необходимо добавить ашкены в контролер
```
public function actions()
{
    return [
        'file-upload' => [
            'class' => \pantera\media\actions\kartik\MediaUploadActionKartik::className(),
            'model' => function () {
                if (Yii::$app->request->get('id')) {
                    return $this->findModel(Yii::$app->request->get('id'));
                } else {
                    return new Test();
                }
            }
        ],
        'file-delete' => [
            'class' => \pantera\media\actions\kartik\MediaDeleteActionKartik::className(),
            'model' => function () {
                return \pantera\media\models\Media::findOne(Yii::$app->request->get('id'));
            }
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
### Настройка загрузчика от 2amigos
Виджет работает только в режиме мультизагрузки
Необходимо добавить ашкены в контролер
```
public function actions()
{
    return [
        'file-upload-dosamigos' => [
            'class' => \pantera\media\actions\dosamigos\MediaUploadActionDosamigos::className(),
            'deleteAction' => ['file-delete-dosamigos'],
            'model' => function () {
                if (Yii::$app->request->get('id')) {
                    return $this->findModel(Yii::$app->request->get('id'));
                } else {
                    return new Test();
                }
            }
        ],
        'file-delete-dosamigos' => [
            'class' => \pantera\media\actions\dosamigos\MediaDeleteActionDosamigos::className(),
            'model' => function () {
                return \pantera\media\models\Media::findOne(Yii::$app->request->get('id'));
            }
        ],
    ];
}
```
Во вью подключить виджет загрузки
```
<?= pantera\media\widgets\dosamigos\MediaUploadWidgetDosamigos::widget([
    'model' => $model,
    'bucket' => 'mediaOther',
    'urlUpload' => ['file-upload-dosamigos', 'id' => $model->id],
    'urlDelete' => ['file-delete'],
]) ?>
```
### Настройка загрузчика от Innostudio

Необходимо добавить ашкены в контролер
```
public function actions()
{
    return [
        'file-upload-innostudio' => [
            'class' => \pantera\media\actions\kartik\MediaUploadActionKartik::className(),
            'model' => function () {
                if (Yii::$app->request->get('id')) {
                    return $this->findModel(Yii::$app->request->get('id'));
                } else {
                    return new Test();
                }
            }
        ],
        'file-delete-innostudio' => [
            'class' => \pantera\media\actions\kartik\MediaDeleteActionKartik::className(),
            'model' => function () {
                return \pantera\media\models\Media::findOne(Yii::$app->request->post('id'));
            }
        ],
    ];
}
```
Во вью подключить виджет загрузки
```
<?= MediaUploadWidgetInnostudio::widget([
        'model' => $model,
        'bucket' => 'mediaMain',
        'urlUpload' => ['file-upload-innostudio', 'id' => $model->id],
        'urlDelete' => ['file-delete-innostudio'],
        'pluginOptions' => [
            'limit' => 1,
        ],
    ]) ?>
    <?= MediaUploadWidgetInnostudio::widget([
        'model' => $model,
        'bucket' => 'mediaOther',
        'urlUpload' => ['file-upload-innostudio', 'id' => $model->id],
        'urlDelete' => ['file-delete-innostudio'],
    ]) ?>
```
### Работа с медиа файлами
Для получения нужно вызвать свойсто модели как название бакета
```
<?= $model->mediaOther ?>
<?= $model->mediaMain ?>
```
Если бакет мультипл то результатом будет массив иначе объект медиа
