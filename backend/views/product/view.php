<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Продукты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить продукт ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category.name',
            'price',
            'name',
            'slug',
            'description:ntext',
            [
                'attribute'=>'tags',
                'format'=>'raw',
                'value'=>function($model){
                    $tags = $model->getSelectedTagsName();
                    $table = '<table>';
                    foreach ($tags as $tag){
                        $table .= "<tr><td>$tag</td></tr>";
                    }
                    return $table.'</table>';
                }
            ]
        ],
    ]) ?>

</div>
