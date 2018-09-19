<?php


/* @var $this yii\web\View */
/* @var $model common\models\Girls */

$this->title = '新增校花';
$this->params['breadcrumbs'][] = ['label' => '校花管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="girls-create">

    <?= $this->render('_form', [
        'model' => $model,
        'uploadModel' => $uploadModel
    ]) ?>

</div>
