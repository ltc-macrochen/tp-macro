<?php
/**
 * User: keven
 * Date: 2016/7/8
 * Time: 11:07
 */

namespace common\components\refactor\filters;


class AccessControl extends \yii\filters\AccessControl
{
    public $ruleConfig = ['class' => 'common\components\refactor\filters\AccessRule'];

    public function beforeAction($action)
    {
        \Yii::$app->response->getHeaders()->set('Url', \Yii::$app->request->getAbsoluteUrl());
        return parent::beforeAction($action);
    }
}