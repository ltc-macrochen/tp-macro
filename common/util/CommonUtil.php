<?php
/**
 * User: macrochen
 * Time: 2018/9/11 11:26
 */

namespace common\util;

use Yii;

class CommonUtil
{

    /**
     * 检查refer是否正确
     * @return bool
     */
    public static function checkRefer()
    {
        $request = Yii::$app->getRequest();
        $refer = $request->referrer;
        $host = $request->getHostInfo();
        $parseRefer = parse_url($refer);
        if (!isset($parseRefer['host'])) {
            return false;
        }
        $parseHost = parse_url($host);
        if (!isset($parseHost['host'])) {
            return false;
        }

        if ($parseRefer['host'] == $parseHost['host']) {
            return true;
        }
        return false;
    }
}