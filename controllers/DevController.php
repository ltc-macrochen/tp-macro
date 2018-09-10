<?php
/**
 * User: macrochen
 * Time: 2018/8/14 14:07
 */

namespace app\controllers;

use abei2017\wx\Application;
use common\components\ZhugeComponent;
use Yii;
use yii\web\Controller;

class DevController extends Controller
{
    public function actionTest()
    {
        var_dump(microtime());return;
        $res = (new ZhugeComponent())->uploadEvent("测试统计上报", time());
        if (isset($res['return_code']) && $res['return_code'] == 0) {
            // 成功
        } else {
            $errMsg = (isset($res['return_code']) ? $res['return_code'] : 'null errcode') . (isset($res['return_message']) ? $res['return_message'] : 'null msg');
            Yii::warning("[zg upload event error]{$errMsg}");
        }
        var_dump($res);
    }
}