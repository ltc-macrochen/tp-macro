<?php
/**
 * Created by PhpStorm.
 * User: chunmao
 * Date: 2018/3/30 0030
 * Time: 上午 11:58
 */

namespace app\controllers;

use app\common\models\form\AdminLoginForm;
use app\common\models\form\LoginForm;
use app\common\components\UserComponent;
use app\common\models\form\UserForm;
use app\common\models\form\WalletForm;
use app\common\models\User;
use common\components\refactor\filters\AccessControl;
use common\mail\EmailSender;
use yii\helpers\VarDumper;
use yii\web\Controller;
use Yii;

class UserController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['logout']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['admin-login']
                    ]
                ],
            ]
        ];
    }

    /**
     * 超级管理员登录
     * @return string|\yii\web\Response
     */
    public function actionAdminLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect([Yii::$app->defaultRoute]);
        }

        return $this->render('admin-login', [
            'model' => $model
        ]);
    }

    /**
     * 退出登录
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        \Yii::$app->user->logout();
        return $this->goHome();
    }
}