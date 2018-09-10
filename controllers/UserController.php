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
                        'actions' => ['create-wallet', 'logout']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['login', 'login-stub', 'admin-login']
                    ]
                ],
            ]
        ];
    }

    public function actionSignUp()
    {return 0;
        $form = new UserForm();
        if(\Yii::$app->request->isPost) {
            $form->load(\Yii::$app->request->post());
            if($form->user_password != $form->re_password) {
                \Yii::$app->session->setFlash('danger', '密码和确认密码不一样');
            } else {
                $form->user_password = md5($form->user_password);
                if ($form->save()) {
                    \Yii::$app->session->setFlash('success', "成功注册用户");
                    return $this->render("login", ['model' => new User()]);
                } else {
                    \Yii::$app->session->setFlash('danger', "注册用户失败:" . VarDumper::dumpAsString($form->getErrors()));
                }
            }
        }
        return $this->render("signup", ['model' => $form]);
    }

    /**
     * 用户登录
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        return $this->render('login');
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