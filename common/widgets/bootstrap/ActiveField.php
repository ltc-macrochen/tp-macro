<?php
/**
 * Created by PhpStorm.
 * User: chunmao
 * Date: 2016/9/2 0002
 * Time: 下午 17:25
 */

namespace app\common\widgets\bootstrap;


use yii\captcha\CaptchaValidator;
use yii\helpers\Html;
use yii\validators\FileValidator;
use yii\validators\RequiredValidator;

/**
 * 这个类是为了定制通用的特殊显示使用
 * Class ActiveField
 * @package common\widgets
 */
class ActiveField extends \yii\bootstrap\ActiveField
{
    /**
     * 在label外面添加一个div的标签
     * @param $label string
     * @return string
     */
    protected  function addDivToLabel($label)
    {
        // 不使用默认的
        if(isset($this->options['customLabel'])) {
            return $label;
        }
        // 这里有可能会加重复,所以要判断一下
        if(strncasecmp($label, '<div class="input-label">', strlen('<div class="input-label">')) == 0) {
            return $label;
        }
        return '<div class="input-label">' . $label . '</div>';
    }

    /**
     * 重写，使得在必须要输入的项前面添加一个红色的*符号
     * @param null $label
     * @param array $options
     * @return \yii\bootstrap\ActiveField
     */
    public function label($label = null, $options = [])
    {
        $ret = parent::label($label, $options);
        if(!empty($this->attribute)) {
            $attribute = Html::getAttributeName($this->attribute);
            // 判断是否必须填写
            foreach ($this->model->getActiveValidators($attribute) as $validator) {
                if ($validator instanceof RequiredValidator) {
                    if ($validator->when === null) {
                        // 必须输入
                        $this->parts['{label}'] = '<b style="color:red">*</b> ' . $this->parts['{label}'];
                        break;
                    }
                } else if($validator instanceof FileValidator) {
                    if(!$validator->skipOnEmpty) {
                        // 必须有附近
                        $this->parts['{label}'] = '<b style="color:red">*</b> ' . $this->parts['{label}'];
                        break;
                    }
                } else if($validator instanceof  CaptchaValidator) {
                    $this->parts['{label}'] = '<b style="color:red">*</b> ' . $this->parts['{label}'];
                }
            }
        }
        $this->parts['{label}'] = $this->addDivToLabel( $this->parts['{label}']);
        return $ret;
    }
}