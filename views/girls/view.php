<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Girls */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '校花管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="girls-view">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '您确定要删除该条记录吗？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'area',
            [
                'attribute' => 'head',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::img($model->head, ['style' => 'height:200px;width:auto;']);
                }
            ],
            [
                'attribute' => 'video',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<video preload='true' controls='controls' src='{$model->video}' style='height: 200px;width: auto;'></video>";
                }
            ],
            'vote_count',
            [
                'attribute' => 'statusName',
                'label' => '状态'
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],
        ],
    ]) ?>

</div>
