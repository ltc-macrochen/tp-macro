<?php
/**
 * User: macrochen
 * Time: 2018/8/14 15:39
 */

use yii\helpers\Url;
?>

<input type="text" class="hidden record-id"/>
<div class="form-group">
    <div class="input-label">
        <label class="control-label">场景名称：</label>
    </div>
    <input type="text" class="form-control scene-name" placeholder="请输入场景名称"/>
</div>
<div class="clearfix" style="padding-top:22px;">
    <a href="javascript:;" class="btn btn-success pull-right">新增</a>
</div>

<script>
    /** 场景保存*/
    function qrcodeSave() {
        var id = $('#'+g_modal_id_qrcode_edit+' .record-id').val();
        var sceneName = $('#'+g_modal_id_qrcode_edit+' .scene-name').val();
        if ($.trim(sceneName).length === 0) {
            alertMsg('请输入场景名称');
            return;
        }

        var data = {id:id,sceneName:sceneName};
        $.post('<?= Url::to(['wx-scene/scene-save'])?>', data, function (res) {
            var jumpUrl;
            if (res.err == 0) {
                jumpUrl = true;
            }
            alertMsg(res.msg, jumpUrl);
        });
    }

    $(function () {
        $('#'+g_modal_id_qrcode_edit+' .btn').on('click', function () {
            qrcodeSave();
        });
    })
</script>