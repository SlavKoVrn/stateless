<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\News $model */

$link = Url::to(['view', 'slug' => $model->slug]);
?>
<div class="item">
    <a href="<?= $link ?>" >
        <span><?= date('d.m.Y H:i',strtotime($model->created_at)) ?></span>
        <div><?= Html::img($model->image,['width'=>'250px']) ?></div>
        <span><?= $model->title ?></span>
    </a>
</div>
<?php
$css=<<<CSS
    ul.pagination{
        margin-top:22px;
        width:100%;
        text-align:center;
    }
    ul.pagination > li > a {
        margin:10px;
        padding:10px;
    }
    ul.pagination > li > a.active {
        color:white;
        background-color:#1c84c6;
    }
    .item{
        box-shadow: 0px 5px 10px 0px rgba(0, 0, 0, 0.5);
        margin:10px;
        text-align:center;
        border-radius:22px;
    }
    .item span{
        width:100%;
    }
    .item div{
        width:100%;
    }
CSS;
$this->registerCSS($css);
