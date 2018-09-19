<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Girls;
use common\widgets\UploadProgress;

echo UploadProgress::widget(['id' => 'fileUploadProgress'])
?>

<div class="girls-form">

    <?php $form = ActiveForm::begin(['id' => 'uploadFileForm']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请输入姓名']) ?>

    <?= UploadProgress::uploadImageFileField($form, $uploadModel, 'imageFile', '头像') ?>

    <?php $inputField = $form->field($uploadModel, 'videoFile')
        ->fileInput(['multiple' => false, 'accept' => 'video/mp4, video/mpeg', 'class' => 'file-input hidden'])
        ->label('视频（MP4）');
        $uploadButton = '<a href="javascript:;" class="m-upload-btn v-upload-btn">选择文件</a>';
        $inputField->template = "{label}\n{input}{$uploadButton}\n{hint}\n{error}";
        echo $inputField;
    ?>

    <?= $form->field($model, 'area')->textInput(['placeholder' => '请输入赛区'])?>

    <?= $form->field($model, 'vote_count')->textInput(['placeholder' => '请输入票数，可以不设置，默认为0'])?>

    <?= $form->field($model, 'status')->dropDownList(Girls::getAllGirlsStatus()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("$('#uploadFileForm').on('beforeSubmit', function(){startUploading('fileUploadProgress', 'uploadFileForm');  return false;})");
?>
