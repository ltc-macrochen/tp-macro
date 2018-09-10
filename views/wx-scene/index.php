<?php

use yii\helpers\Html;
use common\widgets\GridView;
use yii\bootstrap\Modal;

$this->title = '带参数二维码';

// 新增带参数二维码
Modal::begin([
    'header' => '<h2>新增场景</h2>',
    'toggleButton' => false,
    'id' => 'modal-add-qrcode',
    'size' => Modal::SIZE_DEFAULT,
    'clientOptions' => ['backdrop' => 'static']
]);
echo $this->render('form-qrcode');
Modal::end();

?>
<div class="m-base-info wx-scene-index" style="padding-top:22px;">
    <div class="m-detail-head">
        <h4><?= Html::encode($this->title)?></h4>
        <a class="btn btn-primary pull-right" onclick="showQrCodeEditModal();return false;" style="margin-top:-25px;">新增</a>
    </div>
    <div class="m-detail-body">
        <div class="wallets">

        </div>

        <div class="transaction-index">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => '{items} {pager}',
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],

                    'id',
                    [
                        'attribute' => 'scene_name',
                        'contentOptions' => ['class' => 'scene-name']
                    ],
                    [
                        'attribute' => 'qrcode_url',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::img($model->qrcode_url, ['style' => 'max-height:150px;max-width:auto;']);
                        }
                    ],
                    [
                        //'label' => '',
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:Y-m-d H:i:s']
                    ],
                    // 'updated_at',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => "{update} {download} {delete}",
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="glyphicon glyphicon-pencil"></i>',
                                    'javascript:;',
                                    [
                                        'title' => '更新',
                                        'onclick' => "showQrCodeEditModal({$model->id});return false;"
                                    ]
                                );
                            },
                            'download' => function ($url, $model, $key) {
                                return Html::a('<i class="glyphicon glyphicon-download-alt"></i>',
                                    [
                                        'wx-scene/download-qrcode',
                                        'img-url' => $model->qrcode_url,
                                        'scene-name' => $model->scene_name
                                    ],
                                    ['title' => '下载二维码']
                                );
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<script>
    var g_modal_id_qrcode_edit = 'modal-add-qrcode';

    /** 显示场景编辑弹层*/
    function showQrCodeEditModal(recordId) {
        if (undefined !== recordId) {
            $('#'+g_modal_id_qrcode_edit+' .modal-header h2').text('更新场景');
            $('#'+g_modal_id_qrcode_edit+' .record-id').val(recordId);
            var sceneName = $('tbody tr[data-key='+recordId+'] .scene-name').text();
            $('#'+g_modal_id_qrcode_edit+' .scene-name').val(sceneName);
            $('#'+g_modal_id_qrcode_edit+' .btn-success').text('更新');
        } else {
            $('#'+g_modal_id_qrcode_edit+' .modal-header h2').text('新增场景');
            $('#'+g_modal_id_qrcode_edit+' .record-id').val('');
            $('#'+g_modal_id_qrcode_edit+' .scene-name').val('');
            $('#'+g_modal_id_qrcode_edit+' .btn-success').text('新增');
        }
        $('#'+g_modal_id_qrcode_edit).modal();
    }
</script>
