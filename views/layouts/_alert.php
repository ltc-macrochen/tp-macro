<?php
/**
 * User: macrochen
 * Time: 2018/8/6 11:28
 */

use yii\bootstrap\Modal;

Modal::begin([
    'header' => '<h2>温馨提示</h2>',
    'toggleButton' => false,
    'id' => 'modal-alert-msg',
    'size' => Modal::SIZE_DEFAULT,
    'clientOptions' => ['backdrop' => 'static'],
    //'footer' => '<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>',
]);
echo "<div><p class='msg text-center' style='padding: 15px;font-size:16px;'>提示信息</p></div>
<div class='clearfix'>
    <a href='javascript:;' class='btn btn-success pull-right btn-confirm'>确定</a>
</div>";
Modal::end();
?>