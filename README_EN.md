# yii2-media
Module for media resources (images, videos, files) management for any entities

## Install
Preferred way via composer:
```
$ composer require pantera-digital/yii2-media "dev-master"
```
## Configuration
Add module to your modules config and bootstrap section
```
'bootstrap' => ['media'],
'modules' => [
    'media' => [
        'class' => pantera\media\Module::className(),
        'permissions' => ['admin'],
    ],
],
```
#### Migration
Configure console application controllerMap like below
```
'controllerMap' => [
    'migrate' => [
        'class' => yii\console\controllers\MigrateController::className(),
        'migrationPath' => [
            '@pantera/media/migrations',
        ],
    ],
],
```
Available module settings
```
permissions => ['@'] //Array of RBAC roles
mediaUrlAlias => '@web/uploads/media/' //Alias for access to file by url
mediaFileAlias => '@webroot/uploads/media/' //Alias for file full path
```
Module will set this aliases by default
You should add behavior to model that you want relate with images
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
### You can configure kartik-v uploader
Please add actions from below section to your controller for uploader work
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
        'file-sort' => [
            'class' => \pantera\media\actions\kartik\MediaSortActionKartik::className(),
            'model' => function () {
                    return $this->findModel(Yii::$app->request->get('id'));
            }
        ],
    ];
}
```
In view you should use one of available widget from section below: 
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
    'urlDelete' => ['file-sort'],
    'options' => [
        'multiple' => true,
    ],
]) ?>
```
### Configuration 2amigos uploader 
!Widget works only in multiply files mode
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
In view add uploader widget
```
<?= pantera\media\widgets\dosamigos\MediaUploadWidgetDosamigos::widget([
    'model' => $model,
    'bucket' => 'mediaOther',
    'urlUpload' => ['file-upload-dosamigos', 'id' => $model->id],
]) ?>
```
### Setting up uploader from Innostudio

Add below actions to your controller
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
        'files-sort' => [
            'class' => \pantera\media\actions\kartik\MediaSortActionKartik::className(),
            'model' => function () {
                return $this->findModel(Yii::$app->request->get('id'));
            },
        ],
    ];
}
```
Add widget to your view file
```
<?= pantera\media\widgets\innostudio\MediaUploadWidgetInnostudio::widget([
    'model' => $model,
    'bucket' => 'mediaMain',
    'urlUpload' => ['file-upload-innostudio', 'id' => $model->id],
    'urlDelete' => ['file-delete-innostudio'],
    'pluginOptions' => [
        'limit' => 1,
    ],
    'urlSort' => ['files-sort', 'id' => $model->id],
]) ?>
<?= pantera\media\widgets\innostudio\MediaUploadWidgetInnostudio::widget([
    'model' => $model,
    'bucket' => 'mediaOther',
    'urlUpload' => ['file-upload-innostudio', 'id' => $model->id],
    'urlDelete' => ['file-delete-innostudio'],
]) ?>
```
### Work with media files
For getting image you should call model property as bucket name 
```
<?= $model->mediaOther ?>
<?= $model->mediaMain ?>
```
If bucket is multiple then result will be an array type and Media object in other case
### Add custom validation rules
For add custom validation rules you need configure rules for uploader action like in example below
```
'file-upload-innostudio' => [
    'class' => \pantera\media\actions\kartik\MediaUploadActionKartik::className(),
    'model' => function () {
        if (Yii::$app->request->get('id')) {
            return $this->findModel(Yii::$app->request->get('id'));
        } else {
            return new Test();
        }
    },
    'rules' => [
        'media' => ['media', 'file', 'extensions' => 'jpeg'],
    ],
],
```
You can edit any default rules or add new
