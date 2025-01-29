<?php

use common\models\News;
use backend\modules\news\widgets\BulkButtonWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\NewsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;

$pjaxId = 'pjax-grid';
$gridId = 'table';
?>
<div class="news-index">

    <p>
        <?= Html::a('Добавить новость', ['create'], ['class' => 'btn btn-success']) ?>
        <?= BulkButtonWidget::widget(['pjaxId' => $pjaxId, 'gridId' => $gridId]) ?>
    </p>

    <?php Pjax::begin(['id' => $pjaxId,'timeout' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'title',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'filter' => false,
                'value' => function($model){
                    return Html::a(Html::img($model->image,['width' => '100px']),'/news/'.$model->slug,[
                        'target' => '_blank',
                        'data-pjax' => 0,
                    ]);
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date('d.m.Y H:i',strtotime($model->created_at));
                },
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model){
                    return date('d.m.Y H:i',strtotime($model->updated_at));
                },
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, News $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
