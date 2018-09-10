<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VoteHistory */

$this->title = 'Create Vote History';
$this->params['breadcrumbs'][] = ['label' => 'Vote Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vote-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
