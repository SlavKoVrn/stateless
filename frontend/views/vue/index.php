<?php

use common\models\Category;
use common\models\Tag;
use common\widgets\IonRangeSliderWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
\common\assets\DataTableAsset::register($this);
\common\assets\IziToastAsset::register($this);
?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'name') ?>
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
    <?= Html::submitButton('Поиск', ['id'=>'search_product','class' => 'btn btn-primary']) ?>
    <?= Html::resetButton('Сброс', ['id'=>'search_reset','class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>

<div class="container">
    <div id="app"></div>
</div>
<script src="/frontend/web/webpack/app.js"></script>
