<?php
/**
 * Created by PhpStorm.
 * User: chunmao
 * Date: 2017/6/22 0022
 * Time: 上午 10:40
 */

namespace common\widgets;

use \Yii;
use yii\helpers\Url;


class Accordion
{
    /**
     * 生成一个菜单项
     * @param array $item 数组元素具有这几个元素：class：图标class，label：名称，url，subItems，active：是否active,number:提示的数字
     */
    public static function menuItemBegin(&$item)
    {
        $active = empty($item['active']) ? '' : 'active';
        $url = empty($item['url']) ? '#' : Url::to($item['url']);
        $icon = empty($item['class']) ? '' : $item['class'];
        $label = empty($item['label']) ? '' : $item['label'];
        $expandClass = $active === 'active' ? 'class="submenu-indicator-minus"' : '';
        echo "<li class='$active'><a href='$url' $expandClass><i class='$icon'></i>$label </a>\n";
        if(!empty($item['number'])) {
            echo "<span class=\"jquery-accordion-menu-label\">${item['number']} </span>";
        }
    }

    public static function menuItemEnd() {
        echo "</li>\n";
    }

    /**
     * 将菜单中active的链设置为active，相当于展开active树
     * @param array $items
     * @param string $queryRoute
     * @return bool 成功设置active属性返回true
     */
    public static function parseActive(&$items, &$queryRoute)
    {
        foreach($items as &$item) {
            if(isset($item['items'])) {
                // 这个节点有子节点，只要任何一个子节点有设置active属性为true，那么设置这个节点active属性为true
                if(static::parseActive($item['items'], $queryRoute)) {
                    $item['active'] = true;
                    return true;
                }
            } else {
                if(isset($item['active']) && $item['active'] == true) {
                    return true;
                }
                // 如果路由一样，设置节点为true
                if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
                    $route = $item['url'][0];
                    if ($route[0] !== '/' && Yii::$app->controller) {
                        $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
                    }
                    if($queryRoute == $route) {
                        $item['active'] = true;
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 生成菜单
     * @param array $config  数组元素具有这几个元素：class：图标class，label：名称，url，items，active：是否active
     */
    public static function renderItems(&$config)
    {
        // 先分析展开树
        $queryRoute = Yii::$app->controller->getRoute();
        if(strlen($queryRoute) > 0 && $queryRoute[0] != '/') {
            $queryRoute = '/' . $queryRoute;
        }
        static::parseActive($config, $queryRoute);
        //\yii\helpers\Vardumper::dump($config);die;
        echo '<ul id="menu-list">';
        foreach($config as $item) {
            static::menuItemBegin($item);
            if(isset($item['items'])) {
                static::renderSubItem($item['items'], 1, $item);
            }
            static::menuItemEnd();
        }
        echo "</ul>";
    }

    /** 生成一个菜单子项
     * @param array $config
     * @param int $level
     * @param array $parent
     */
    public static function renderSubItem(&$config, $level, &$parent = [])
    {
        if(isset($parent['active']) && $parent['active'] == true) {
            echo '<ul class="submenu submenu-' . $level . '" style="display:block">';
        } else {
            echo '<ul class="submenu submenu-' . $level . '">';
        }
        foreach($config as $item) {
            if(isset($item['visible']) && !$item['visible']) {
                continue;
            }
            static::menuItemBegin($item);
            if(isset($item['items'])) {
                static::renderSubItem($item['items'], $level + 1, $item);
            }
            static::menuItemEnd();
        }
        echo "</ul>";
    }

    /**
     *
     * @param $routes
     * @param $controllers
     * @return bool
     */
    public static function isItemActive($routes, $controllers)
    {
        $requestedRoute = Yii::$app->requestedRoute;
        if (is_array($routes) && in_array($requestedRoute, $routes)) {
            return true;
        } else if ($requestedRoute == $routes) {
            return true;
        }
        if (in_array(Yii::$app->controller->id, $controllers)) {
            return true;
        }
        return false;
    }
}