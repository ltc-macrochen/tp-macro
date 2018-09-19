<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GirlsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '校花管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="girls-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增校花', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('导出数据', ['download'], ['class' => 'btn btn-info']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'name',
            [
                'label' => '头像',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::img($model->head, ['style' => 'width:auto;height:50px;']);
                },
            ],
            'vote_count',
            'area',
            [
                'label' => '状态',
                'value' => 'statusName'
            ],
            [
                'label' => '创建时间',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'value' => 'created_at'
            ],
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',

            ],
        ],
    ]); ?>
</div>
