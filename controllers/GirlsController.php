<?php

namespace app\controllers;

use app\common\models\User;
use common\components\refactor\filters\AccessControl;
use common\components\UploadImgComponent;
use Yii;
use common\models\Girls;
use common\models\GirlsSearch;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * GirlsController implements the CRUD actions for Girls model.
 */
class GirlsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::USER_ROLE_ADMIN]
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Girls models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GirlsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Girls model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Girls model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Girls();
        $uploadModel = new UploadImgComponent();

        $posts = Yii::$app->request->post();
        if ($model->load($posts)) {
            $loadSuccess = $uploadModel->load($posts);
            $uploadModel->imageFile = UploadedFile::getInstance($uploadModel, 'imageFile');
            if ($loadSuccess && $uploadModel->validate() && $uploadModel->upload()) {
                $model->head = empty($uploadModel->imagePath) ? $model->head : $uploadModel->imagePath;
            } else {
                Yii::$app->session->setFlash('error', '上传用户头像失败，请稍后再试');
                var_dump($loadSuccess);
                var_dump('上传用户头像失败，请稍后再试');return;
                return $this->refresh();
            }

            $model->created_at = time();
            if (!$model->save()) {
                Yii::$app->session->setFlash('error', '新增用户失败：' . VarDumper::dumpAsString($model->errors));
                return $this->refresh();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'uploadModel' => $uploadModel
            ]);
        }
    }

    /**
     * Updates an existing Girls model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Girls model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Girls model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Girls the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Girls::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
