<?php

use common\models\News;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use frontend\modules\news\widgets\NewsLinkPager;
/** @var yii\web\View $this */
/** @var frontend\modules\news\models\NewsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <?php Pjax::begin(['timeout' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="container">

        <?= ListView::widget([
            'layout' => "\n{items}\n",
            'summary' => false,
            'options' => [ 'class' => 'row'],
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'col-sm-12 col-md-4'],
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('item',['model'=>$model]);
            },
            'pager' => false,
        ]) ?>

        <?= NewsLinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'prevPageLabel' => '<<',
            'nextPageLabel' => '>>',
        ]) ?>

    </div>

    <?php Pjax::end(); ?>

</div>
