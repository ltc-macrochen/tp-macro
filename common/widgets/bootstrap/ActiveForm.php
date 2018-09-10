<?php
/**
 * Created by PhpStorm.
 * User: chunmao
 * Date: 2016/9/2 0002
 * Time: 下午 17:21
 */

namespace app\common\widgets\bootstrap;


/**
 * 这个类是为了定制通用的特殊显示使用
 * Class ActiveForm
 * @package common\widgets
 */
class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $fieldClass = 'app\common\widgets\bootstrap\ActiveField';
}
