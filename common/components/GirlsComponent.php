<?php
/**
 * User: macrochen
 * Time: 2018/9/11 13:22
 */

namespace common\components;

use common\models\Girls;
use Yii;

class GirlsComponent
{
    /**
     * 根据ID获取校花
     * @param $id
     * @return static|Girls
     */
    public function findGirlById($id)
    {
        return Girls::findOne(['id' => intval($id), 'status' => Girls::GIRLS_STATUS_NORMAL]);
    }

    /**
     * 修改票数
     * @param $id
     * @param int $count
     * @return bool
     */
    public function updateVoteCount($id, $count = 1)
    {
        if (!is_numeric($count) || $count < 1) {
            return false;
        }
        if (!is_numeric($id) || $id < 0) {
            return false;
        }
        $sql = "update t_girls set vote_count = vote_count + {$count} where id={$id}";
        $res = Yii::$app->db->createCommand($sql)->execute();
        if (!$res) {
            return false;
        }

        return true;
    }
}