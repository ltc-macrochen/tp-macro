<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Girls */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'У������', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="girls-view">

    <p>
        <?= Html::a('����', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('ɾ��', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '��ȷ��Ҫɾ��������¼��',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'head',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::img($model->head, ['style' => 'height:200px;width:auto;']);
                }
            ],
            'vote_count',
            [
                'attribute' => 'statusName',
                'label' => '״̬'
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
