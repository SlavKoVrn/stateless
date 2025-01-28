<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use summernote\Summernote;

/** @var yii\web\View $this */
/** @var common\models\News $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?php if($model->image): ?>
                <?= Html::img($model->image,['width' => '250px']) ?>
            <?php endif; ?>
            <?= $form->field($model, 'imageFile')->fileInput()->label('картинка') ?>
        </div>
    </div>

    <?= $form->field($model, 'text')->widget(Summernote::class, [
        'config' => [
            'focus' => true,
            'height' => 222,
            'maxHeight' => null,
            'minHeight' => null,
            'placeholder' => 'Текст новости',
    ]]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
