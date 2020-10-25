<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */

namespace app\common\controller;

use think\Controller;

/**
 * 项目公共控制器
 * @package app\common\controller
 */
class Common extends Controller
{
    /**
     * [getMap 获取筛选条件]
     * @Author   Jerry
     * @DateTime 2017-04-13T11:32:54+0800
     * @Example  eg:
     * @return   [type]                   [description]
     */
    final protected function getMap()
    {
        $search_field     = input('param.search_field/s', '');
        $keyword          = input('param.keyword/s', '');
        $filter           = input('param._filter/s', '');
        $filter_content   = input('param._filter_content/s', '');
        $filter_time      = input('param._filter_time/s', '');
        $filter_time_from = input('param._filter_time_from/s', '');
        $filter_time_to   = input('param._filter_time_to/s', '');

        $map = [];

        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[$search_field] = ['like', "%$keyword%"];
        }

        // 时间段搜索
        if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
            $map[$filter_time] = ['between time', [$filter_time_from, $filter_time_to]];
        }

        // 下拉筛选
        if ($filter != '') {
            $filter         = array_filter(explode('|', $filter), 'strlen');
            $filter_content = array_filter(explode('|', $filter_content), 'strlen');
            foreach ($filter as $key => $item) {
                $map[$item] = ['in', $filter_content[$key]];
            }
        }
        return $map;
    }

    /**
     * 获取字段排序
     * @Author   Jerry
     * @DateTime 2017-04-13T11:33:30+0800
     * @Example  eg:
     * @param    string                   $extra_order [description]
     * @param    boolean                  $before      [description]
     * @return   [type]                                [description]
     */
    final protected function getOrder($extra_order = '', $before = false)
    {
        $order = input('param._order/s', '');
        $by    = input('param._by/s', '');
        if ($order == '' || $by == '') {
            return $extra_order;
        }
        if ($extra_order == '') {
            return $order. ' '. $by;
        }
        if ($before) {
            return $extra_order. ',' .$order. ' '. $by;
        } else {
            return $order. ' '. $by . ',' . $extra_order;
        }
    }
    /**
     * [pluginView 渲染插件模板]
     * @Author   Jerry
     * @DateTime 2017-04-13T11:33:48+0800
     * @Example  eg:
     * @param    string                   $template [description]
     * @param    string                   $suffix   [description]
     * @param    array                    $vars     [description]
     * @param    array                    $replace  [description]
     * @param    array                    $config   [description]
     * @return   [type]                             [description]
     */
       final protected function pluginView($template = '', $suffix = '', $vars = [], $replace = [], $config = [])
    {
        $plugin_name = input('param.plugin_name');

        if ($plugin_name != '') {
            $plugin = $plugin_name;
            $action = 'index';
        } else {
            $plugin = input('param._plugin');
            $action = input('param._action');
        }
        $suffix = $suffix == '' ? 'html' : $suffix;
        $template = $template == '' ? $action : $template;
        $template_path = config('plugin_path'). "{$plugin}/view/{$template}.{$suffix}";
        return parent::fetch($template_path, $vars, $replace, $config);
    }
}