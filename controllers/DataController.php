<?php
/**
 * User: macrochen
 * Time: 2018/9/11 11:22
 */

namespace app\controllers;

use common\components\GirlsComponent;
use common\components\VoteHistoryComponent;
use common\models\Girls;
use common\util\CommonUtil;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use Yii;

class DataController extends Controller
{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonUtil::checkRefer()) {
                //throw new ForbiddenHttpException('no right to access');
            }
            return true;
        }
        return false;
    }

    /**
     *
     * @return array
     */
    public function actionAllGirls()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $girls = Girls::find()
            ->select('id,name,head,vote_count')
            ->where(['status' => Girls::GIRLS_STATUS_NORMAL])
            ->orderBy('vote_count desc')
            ->asArray()
            ->all();

        return $this->buildResponseArray(0, 'success', $girls);
    }

    /**
     * 投票
     * @return array
     */
    public function actionDoVote()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (empty($id) || !is_numeric($id)) {
            return $this->buildResponseArray(-1, '校花ID有误');
        }

        // 检查投票对象是否存在
        $girlCpt = new GirlsComponent();
        $girl = $girlCpt->findGirlById($id);
        if (!$girl) {
            return $this->buildResponseArray(-1, '投票对象不存在，请确认');
        }

        // 次数限制
        $ip = Yii::$app->request->getUserIP();
        $cache = Yii::$app->cache;
        $cacheKey = '_do_vote_' . date('Ymd') . $ip;
        $count = $cache->get($cacheKey);
        if ($count && $count >= 5) {
            return $this->buildResponseArray(-1, '今日投票次数已达上限，请明天再来喔');
        }

        // 投票及记录投票流水
        $voteRes = $girlCpt->updateVoteCount($girl->id);
        if (!$voteRes) {
            return $this->buildResponseArray(-1, '投票失败，请稍后再试');
        }
        (new VoteHistoryComponent())->saveRecord($ip, $girl->id);

        $cache->set($cacheKey, intval($count) + 1, 24 * 60 * 60);

        return $this->buildResponseArray(0, 'success');
    }

    /**
     * 构建返回数据格式
     * @param $err
     * @param $msg
     * @param array $data
     * @return array
     */
    protected function buildResponseArray($err, $msg, $data = [])
    {
        return [
            'err' => $err,
            'msg' => $msg,
            'data' => $data
        ];
    }
}