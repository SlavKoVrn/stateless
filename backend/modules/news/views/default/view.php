<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\News $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'slug',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function($model){
                    return Html::img($model->image,['width' => '250px']);
                }
            ],
            'text:html',
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
        ],
    ]) ?>

</div>
