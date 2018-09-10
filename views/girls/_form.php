<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Girls;

/* @var $this yii\web\View */
/* @var $model common\models\Girls */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="girls-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($uploadModel, 'imageFile')->fileInput()->label('头像') ?>

    <?= $form->field($model, 'status')->dropDownList(Girls::getAllGirlsStatus()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
