<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\News $model */

$link = Url::to(['view', 'slug' => $model->slug]);
?>
<div>
    <?= date('d.m.Y H:i',strtotime($model->created_at)) ?>
    <?= Html::img($model->image,['width'=>'250px']) ?><br>
    <a href="<?= $link ?>" >
        <?= $model->title ?>
    </a>
</div>
