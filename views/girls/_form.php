<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Girls;
use common\widgets\UploadProgress;

echo UploadProgress::widget(['id' => 'fileUploadProgress'])
?>

<div class="girls-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= UploadProgress::uploadImageFileField($form, $uploadModel, 'imageFile', '头像') ?>

    <?= $form->field($model, 'vote_count')->textInput()?>

    <?= $form->field($model, 'status')->dropDownList(Girls::getAllGirlsStatus()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
