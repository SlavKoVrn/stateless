<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
\common\assets\IziToastAsset::register($this);
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?php // $form->field($model, 'rememberMe')->checkbox() ?>

                <!--div class="my-1 mx-0" style="color:#999;">
                    забыли пароль <?= Html::a('Переустановить', ['site/request-password-reset']) ?>.
                    <br>
                    повторить верификацию по почте <?= Html::a('Вышлите письмо', ['site/resend-verification-email']) ?>
                </div-->

                <div class="form-group">
                    <?= Html::button('Вход', [
                        'id' => 'btn-login',
                        'class' => 'btn btn-primary',
                        'name' => 'login-button',
                    ]) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$js=<<<JS
    $(document).on('click','#btn-login',function(e){
        e.preventDefault();
        $.ajax({
            type:'post',
            url: '/auth',
            data:{
                username:$('#loginform-username').val(),
                password:$('#loginform-password').val(),
            },
            success: function(data, status, jqXHR) {
                console.log(data);
                iziToast.success({
                    title: 'Успешная регистрация',
                    message: 'перенаправление к продуктам',
                    timeout: 8000
                });
                document.location = '/product';
            },
            error: function (data) {
                $('.invalid-feedback').remove();
                $.each(data.responseJSON, function(key, val) {
                    $("#loginform-"+val.field).after("<div class=\"invalid-feedback\" style=\"color:red\">"+val.message+"</div>");
                }); 
                iziToast.error({
                    title: 'Нет регистрации',
                    message: 'будьте внимательны при вводе логина и пароля',
                    timeout: 8000
                });
            },
        });
        return false;
    })
JS;
$this->registerJS($js);