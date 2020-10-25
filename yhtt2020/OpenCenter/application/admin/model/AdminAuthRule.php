<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/12
 * Time: 16:05
 * ----------------------------------------------------------------------
 */

namespace app\admin\model;

use think\Model;

class AdminAuthRule extends Model
{
    protected $table = ADMIN . 'auth_rule';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function getTree($field = true, $map = [])
    {
        $map[] = ['status', '>=', 0];
        /* 获取所有分类 */
        $list = $this->field($field)->where($map)->order('sort')->select()->toArray();
        $list = $this->toFormatTree($list);
        return $list;
    }

    /**
     * 将格式数组转换为树
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private $formatTree; //用于树型数组完成递归格式的全局变量

    private function _toFormatTree($list, $level = 0, $title = 'title')
    {
        foreach ($list as $key => $val) {
            $tmp_str = str_repeat(" ", $level * 2);
            $tmp_str .= "└";
            $val['level'] = $level;
            $val['title_show'] = $level == 0 ? $val[$title] . " " : $tmp_str . $val[$title] . " ";
            // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];
            if (!array_key_exists('_child', $val)) {
                array_push($this->formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree, $val);
                $this->_toFormatTree($tmp_ary, $level + 1, $title); //进行下一层递归
            }
        }
        return;
    }

    public function toFormatTree($list, $title = 'title', $pk = 'id', $pid = 'pid', $root = 0)
    {
        $list = list_to_tree($list, $pk, $pid, '_child', $root);
        $this->formatTree = array();
        $this->_toFormatTree($list, 0, $title);
        return $this->formatTree;
    }

    private function _toFormatAuthTree($list, $level = 0, $title = 'title')
    {
        foreach ($list as $key => $val) {
            // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];
            if (!array_key_exists('_child', $val)) {
                array_push($this->formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree, $val);
                $this->_toFormatTree($tmp_ary, $level + 1, $title); //进行下一层递归
            }
        }
        return;
    }

    public function toFormatAuthTree($list, $title = 'title', $pk = 'id', $pid = 'pid', $root = 0)
    {
        $list = list_to_tree($list, $pk, $pid, 'list', $root);
        $this->formatTree = array();
        $this->_toFormatAuthTree($list, 0, $title);
        return $this->formatTree;
    }

    public function getAuthTree($list = [])
    {
        $list = $this->toFormatAuthTree($list);
        return $list;
    }
}