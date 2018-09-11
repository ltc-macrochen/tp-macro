<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Girls */

$this->title = '更新校花: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '校花管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="girls-update">

    <?= $this->render('_form', [
        'model' => $model,
        'uploadModel' => $uploadModel
    ]) ?>

</div>
