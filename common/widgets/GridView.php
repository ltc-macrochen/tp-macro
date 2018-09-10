<?php
/**
 * Created by PhpStorm.
 * User: chunmao
 * Date: 2016/9/13 0013
 * Time: 上午 10:33
 */

namespace common\widgets;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\grid\Column;
use yii\helpers\Inflector;
use yii\grid\GridViewAsset;
use yii\helpers\Json;
use yii\helpers\Url;


class GridView extends \yii\grid\GridView
{
    public $filterPosition = parent::FILTER_POS_HEADER;
    /**
     * @var bool  是否要隐藏搜索条件
     */
    public $hideCondition = false;

    /**
     * @var array 分页组建
     */
    public $pager = [
        'class'=>'common\widgets\GridGoLinkPager',
        'firstPageLabel'=>"首页",
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
        'lastPageLabel'=>'尾页',
        'go'=>true
    ];

    public function run()
    {
        $this->view->registerCssFile("@web/css/gridview.css");
        /* 去掉自定义搜索框（改用别的方法，可以增加搜索按钮）2018/07/26 macrochen
        if ($this->filterModel !== null) {
            $content = '';
            foreach ($this->columns as $column) {
                if(!$column instanceof DataColumn || empty($column->attribute)) {
                    continue;
                }
                if(isset($column->filter) && empty($column->filter)) {
                    continue;
                }
                $label = $this->getHeaderCellLabel($column);
                $content .= "<div class=\"input_text\">";
                $content .= "<label>$label</label>";
                Html::removeCssClass($column->filterInputOptions, 'form-control');
                Html::addCssClass($column->filterInputOptions, 'input_out');
                $column->filterInputOptions['onfocus'] = "this.className='input_on';this.onmouseout=''";
                $column->filterInputOptions['onblur'] = "this.className='input_off';this.onmouseout=function(){this.className='input_out'};";
                $column->filterInputOptions['onmousemove'] = "this.className='input_move'";
                $column->filterInputOptions['onmouseout'] = "this.className='input_out'";
                $content .= $this->renderFilterCellContent($column);
                $content .= "</div>";
            }
            if(!empty($content)) {
                echo "<fieldset class='search'  id=\"{$this->filterRowOptions['id']}\"><legend class='search'><a href='' onclick=\"jQuery('#fieldset-search').toggle();return false;\">搜索条件</a></legend>";
                echo "<div id='fieldset-search'";
                if($this->hideCondition) {
                    echo " hidden";
                }
                echo ">";
                echo $content;
                echo "</div>";
                echo "</fieldset>";
            }
        }
        */
        //return parent::run();

        $view = $this->getView();
        GridViewAsset::register($view);
        \yii\widgets\BaseListView::run();
    }

    /**
     * Renders the filter.
     * @return string the rendering result.
     */
    public function renderFilters()
    {
        return '';
    }

    /**
     * @param DataColumn $column
     * @return string
     */
    protected function getHeaderCellLabel($column)
    {
        $provider = $this->dataProvider;

        if ($column->label === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                /* @var $model Model */
                $model = new $provider->query->modelClass;
                $label = $model->getAttributeLabel($column->attribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model Model */
                    $label = $model->getAttributeLabel($column->attribute);
                } else {
                    $label = Inflector::camel2words($column->attribute);
                }
            }
        } else {
            $label = $column->label;
        }

        return $label;
    }

    /**
     * @param DataColumn $column
     * @return string
     */
    protected function renderFilterCellContent($column)
    {
        if (is_string($column->filter)) {
            return $column->filter;
        }

        $model = $column->grid->filterModel;

        if ($column->filter !== false && $model instanceof Model && $column->attribute !== null && $model->isAttributeActive($column->attribute)) {
            if ($model->hasErrors($column->attribute)) {
                Html::addCssClass($column->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $column->attribute, $column->grid->filterErrorOptions);
            } else {
                $error = '';
            }
            if (is_array($column->filter)) {
                $options = array_merge(['prompt' => ''], $column->filterInputOptions);
                return Html::activeDropDownList($model, $column->attribute, $column->filter, $options) . $error;
            } else {
                return Html::activeTextInput($model, $column->attribute, $column->filterInputOptions) . $error;
            }
        } else {
            return '';
        }
    }
}