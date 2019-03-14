<?php
/**
 * Created by PhpStorm.
 * User: singletonn
 * Date: 10/17/18
 * Time: 3:54 PM
 */

use pantera\media\models\Media;
use yii\web\View;

/* @var $this View */
/* @var $models Media[] */
/* @var $showThumbs bool */
?>
<div class="synced-carousel-main owl-carousel">
    <?php foreach ($models as $model): ?>
        <div class="item">
            <a data-fancybox="images" href="<?= $model->image() ?>">
                <div class="image">
                    <img src="<?= $model->image(420, 250, $this->context->cropImages) ?>" alt="">
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php if ($showThumbs): ?>
    <div class="synced-carousel-thumbs owl-carousel">
        <?php foreach ($models as $model): ?>
            <div class="item">
                <div class="image">
                    <img src="<?= $model->image(90, 71, $this->context->cropThumbs) ?>" alt="">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
