<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VoteHistory */

$this->title = 'Update Vote History: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Vote Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="vote-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
