<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use \yii\helpers\Url;

app\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->params['applicationName']?> | <?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <base href="/" />
    <style type="text/css">
        *{box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;}
        /* 修改container的属性*/
        .container {
            width: 100%;
        }
    </style>
    <script>
        /**
         * 统一提示方法
         * msg 提示信息
         * jumpUrl mix 传入要跳转的url，则点击弹层“确定”按钮会进行跳转，传入true，则点击弹层“确定”按钮会刷新页面。也可以不穿此参数，则不作处理
         * callback 回调函数，提示信息的同时做一些处理
         */
        function alertMsg(msg, jumpUrl, callback) {
            $("#modal-alert-msg .msg").text(msg);
            $("#modal-alert-msg").modal();

            if (undefined != jumpUrl && jumpUrl) {
                $("#modal-alert-msg .btn-confirm").unbind('click').on('click', function(){
                    $("#modal-alert-msg").modal('hide');
                    if (typeof(jumpUrl) === 'string') {
                        window.location.href = jumpUrl;
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                $("#modal-alert-msg .btn-confirm").unbind('click').on('click', function(){
                    $("#modal-alert-msg").modal('hide');
                });
            }

            if (typeof(callback) === 'function') {
                callback();
            }
        }
    </script>
</head>
<body>
<?php $this->beginBody(); ?>
<div id="wrapper" class="bg-white">
    <div class="clearfix bg-img">
        <!--header begin-->
        <header id="header">
            <div class="bd">
                <div class="logo"><a href="<?= Url::to(['/']) ?>"><?= Yii::$app->params['applicationName']?></a></div>
                <div class="login">
                    <?php if(Yii::$app->user->isGuest): ?>
                        <span><?=Html::a('登录', ['user/login']) ?></span>
                        <span class="header-line">|</span>
                        <span><?=Html::a('注册', ['user/sign-up']) ?></span>
                    <?php else: ?>
                        <span><?=Html::a(Yii::$app->user->identity->nickname, 'javascript:;') ?></span>
                        <span class="header-line">|</span>
                        <span><?=Html::a('退出', ['user/logout']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <!--header end-->

        <div class="container" style="/*width:1170px;*/">
            <div class="">
                <?= Breadcrumbs::widget([
                    'homeLink' => false,
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </div>
            <div class="row">
                <?= Alert::widget() ?>
            </div>
            <?= $content ?>
            <?= $this->render('_alert');?>
        </div>

    </div>
</div>
<footer>
    <div class="container text-center">
        <div class="wrap clearfix">


        </div>

    </div>
</footer>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
