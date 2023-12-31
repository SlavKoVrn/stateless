<?php

use common\models\Category;
use common\models\Tag;
use common\widgets\IonRangeSliderWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
\common\assets\DataTableAsset::register($this);
\common\assets\IziToastAsset::register($this);
\frontend\assets\VueAssets::register($this);
?>
<div style="margin-top: 50px"></div>
<?php if (false): ?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'description') ?>
<?= $form->field($model, 'category_id')->dropDownList(array_merge([0=>''],Category::getCategories())) ?>
<?= $form->field($model, 'tags')->dropDownList(Tag::getAllArray(),['multiple' => true]) ?>
<?= $form->field($model, 'price')->widget(IonRangeSliderWidget::class,[
    'min' => $price_min,
    'max' => $price_max,
    'from' => $price_min,
    'to' => $price_max,
]) ?>
<div class="form-group">
    <?= Html::resetButton('Сброс', ['id'=>'search_reset','class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php endif; ?>

<div class="container">
    <div id="app"></div>
</div>
