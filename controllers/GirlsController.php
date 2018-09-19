<?php

namespace app\controllers;

use app\common\models\User;
use common\components\refactor\filters\AccessControl;
use common\components\UploadVideoComponent;
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
     * 校花列表
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
     * 新增校花
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Girls();
        $uploadModel = new UploadVideoComponent();

        $posts = Yii::$app->request->post();
        if ($model->load($posts)) {
            $loadSuccess = $uploadModel->load($posts);
            $uploadModel->imageFile = UploadedFile::getInstance($uploadModel, 'imageFile');
            $uploadModel->videoFile = UploadedFile::getInstance($uploadModel, 'videoFile');
            if ($loadSuccess && $uploadModel->validate() && $uploadModel->upload()) {
                $model->head = empty($uploadModel->imagePath) ? $model->head : $uploadModel->imagePath;
                $model->video = empty($uploadModel->videoPath) ? $model->video : $uploadModel->videoPath;
            } else {
                Yii::$app->session->setFlash('error', '上传用户头像或视频失败，请稍后再试');
                return $this->refresh();
            }

            $model->vote_count = $model->vote_count ? $model->vote_count : 0;
            $model->created_at = time();
            if (!$model->save()) {
                // 保存用户失败，删除上传的文件
                unlink($model->head);
                unlink($model->video);

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
     * 更新校花信息
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $uploadModel = new UploadVideoComponent();
        $uploadModel->imageFile = $model->head;
        $uploadModel->videoFile = $model->video;
        $uploadModel->canEmpty = true;
        $oldVoteCount = $model->vote_count;

        // 旧文件
        $oldImage = $model->head;
        $oldVideo = $model->video;
        $delOldImage = false;
        $delOldVideo = false;

        if ($model->load(Yii::$app->request->post())) {
            $uploadModel->imageFile = UploadedFile::getInstance($uploadModel, 'imageFile');
            $uploadModel->videoFile = UploadedFile::getInstance($uploadModel, 'videoFile');

            if ($model->validate() && $uploadModel->upload()) {
                if (!empty($uploadModel->imagePath)) {
                    $model->head = $uploadModel->imagePath;
                    $delOldImage = true;
                }

                if (!empty($uploadModel->videoPath)) {
                    $model->video = $uploadModel->videoPath;
                    $delOldVideo = true;
                }
            }
            $model->updated_at = time();
            $newVoteCount = $model->vote_count;
            $ip = Yii::$app->getRequest()->getUserIP();
            if ($model->save()) {
                // 更新成功，删除旧文件
                if ($delOldImage) {
                    unlink($oldImage);
                }
                if ($delOldVideo) {
                    unlink($oldVideo);
                }

                Yii::warning("IP({$ip})将用户 {$model->name}({$id}) 的票数由({$oldVoteCount})改为($newVoteCount)", 'updateVoteCount');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', '更新失败：' . VarDumper::dumpAsString($model->errors));
            }
        }

        return $this->render('update', [
            'model' => $model,
            'uploadModel' => $uploadModel
        ]);
    }

    /**
     * Deletes an existing Girls model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = Girls::GIRLS_STATUS_DISABLE;
        if (!$model->save()) {
            Yii::$app->session->setFlash('error');
        }

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

    /**
     * 下载数据
     */
    public function actionDownload()
    {
        $allGirls = Girls::find()->where(['status' => Girls::GIRLS_STATUS_NORMAL])->asArray()->all();
        //初始化PHPExcel
        $objectPHPExcel = new \PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $pageSize = 1000;
        $count = count($allGirls);
        $pageCount = (int)($count/$pageSize) +1;
        $currentPage = 0;
        $n = 0;
        foreach ($allGirls as $item){
            if ( ($perPageIndex = $n % $pageSize) === 0 ) {
                if($currentPage>0){
                    $objectPHPExcel->createSheet();
                    $objectPHPExcel->setActiveSheetIndex($currentPage);
                }
                //报表头的输出
                $objectPHPExcel->getActiveSheet()->mergeCells('B1:G1');
                $objectPHPExcel->getActiveSheet()->setCellValue('B1','校花投票结果');

                $objectPHPExcel->setActiveSheetIndex($currentPage)->getStyle('B1')->getFont()->setSize(24);
                $objectPHPExcel->setActiveSheetIndex($currentPage)->getStyle('B1')
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objectPHPExcel->setActiveSheetIndex($currentPage)->setCellValue('B2','日期：'.date("Y年m月j日"));
                $objectPHPExcel->setActiveSheetIndex($currentPage)->setCellValue('G2','第'.($currentPage+1).'/'.$pageCount.'页');
                $objectPHPExcel->setActiveSheetIndex($currentPage)->getStyle('G2')
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                //表格头的输出
                $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $objectPHPExcel->setActiveSheetIndex($currentPage)->setCellValue('B3','ID');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
                $objectPHPExcel->setActiveSheetIndex($currentPage)->setCellValue('C3','姓名');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $objectPHPExcel->setActiveSheetIndex($currentPage)->setCellValue('D3','票数');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

                //设置居中
                $objectPHPExcel->getActiveSheet()->getStyle('B3:D3')
                    ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //设置边框
                $objectPHPExcel->getActiveSheet()->getStyle('B3:D3' )
                    ->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('B3:D3' )
                    ->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('B3:D3' )
                    ->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('B3:D3' )
                    ->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('B3:D3' )
                    ->getBorders()->getVertical()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
                //设置颜色
                $objectPHPExcel->getActiveSheet()->getStyle('B3:R3')->getFill()
                    ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');
                $currentPage = $currentPage + 1;
            }
            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('B'.($perPageIndex+4) ,$item['id']);
            $objectPHPExcel->getActiveSheet()->setCellValue('C'.($perPageIndex+4) ,$item['name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D'.($perPageIndex+4) ,$item['vote_count']);

            $n = $n +1;
        }
        //设置分页显示
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
        ob_end_clean();
        ob_start();
        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="校花数据 -'.date("Y年m月j日").'.xls"');
        $objWriter= \PHPExcel_IOFactory::createWriter($objectPHPExcel,'Excel5');
        $objWriter->save('php://output');
    }
}
