<?php
/**
 * User: macrochen
 * Time: 2018/8/14 14:07
 */

namespace app\controllers;

use abei2017\wx\Application;
use common\components\GirlsComponent;
use common\components\ZhugeComponent;
use Yii;
use yii\web\Controller;

class DevController extends Controller
{
    public function actionTest()
    {
        $res = (new GirlsComponent())->updateVoteCount(1);
        var_dump($res);
    }
}