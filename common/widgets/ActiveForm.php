<?php
/**
 * Created by PhpStorm.
 * User: chunmao
 * Date: 2016/9/2 0002
 * Time: 下午 17:21
 */

namespace common\widgets;


/**
 * 这个类是为了定制通用的特殊显示使用
 * Class ActiveForm
 * @package common\widgets
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    public $fieldClass = 'common\widgets\ActiveField';
}
