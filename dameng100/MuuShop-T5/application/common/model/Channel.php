<?php
namespace app\common\model;

use think\Model;

/**
 * 分类模型
 */
class Channel extends Model
{
    /**
     * 获取导航列表，支持多级导航
     * @param  boolean $field 要列出的字段
     * @return array          导航树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function lists($field = true, $tree = false)
    {
        $map = array('status' => 1);
        $list = collection($this->field($field)->where($map)->order('sort')->select())->toArray();
        foreach($list as &$v){
            $v['_'] = '';
        }
        unset($v);

        $tree = list_to_tree($list, 'id', 'pid', '_');
        
        return $tree;
    }
}
