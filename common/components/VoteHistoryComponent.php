<?php
/**
 * User: macrochen
 * Time: 2018/9/11 13:29
 */

namespace common\components;

use common\models\VoteHistory;
use yii\helpers\VarDumper;
use Yii;

class VoteHistoryComponent
{
    /**
     * 保存投票记录
     * @param $ip
     * @param $girlId
     * @param int $count
     * @return bool
     */
    public function saveRecord($ip, $girlId, $count = 1)
    {
        $model = new VoteHistory();
        $model->girl_id = $girlId;
        $model->count = intval($count);
        $model->ip = $ip;
        $model->created_at = time();
        if (!$model->save()) {
            Yii::warning("保存投票记录失败：" . VarDumper::dumpAsString($model->errors), 'voteHistoryError');
            return false;
        }

        return true;
    }
}