<?php
/**
 * User: macrochen
 * Time: 2018/7/30 11:13
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '登录';
?>
<div class="site-login p-b-200 p-t-100 bg-white site-border-box">

    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'sign-box admin-login-box']]); ?>
            <div class="sign-hd text-center">
                <h3>管理员登录</h3>
            </div>
            <br>
            <?= $form->field($model, 'username')
                ->textInput(['autofocus' => true, 'placeholder' => '请输入管理员账户名'])
                ->label('账户名') ?><br>

            <?= $form->field($model, 'password')
                ->passwordInput(['placeholder' => '请输入管理员密码'])
                ->label('密码') ?>

            <?=''; /*$form->field($model, 'rememberMe')->checkbox()->label('记住我')*/ ?>

            <br>
            <div> <?= Html::submitButton('登录 <span class="glyphicon"></span>', ['style' => 'width:100%;', 'class' => 'btn btn-primary', 'name' => 'login-button']) ?></div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>