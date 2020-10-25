<?php
namespace utils;


class TreeUtils
{
    public function getNodeInfo($result, $rule)
    {
//        $result = $this->field('id,title,pid')->select();
        $str = "";
        $strs = "";
//        $role = new UserType();
//        $rule = $role->getRuleById($id);

        foreach($rule as $value){
            $strs .= $value['abilities_id'].',';
        }
        if(!empty($rule)){
            $rule = explode(',', $strs);
        }
        foreach($result as $key => $vo){
            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['parent_id'] . '", "name":"' . $vo['name'].'"';

            if(!empty($rule) && in_array($vo['id'], $rule)){
                $str .= ' ,"checked":1';
            }

            $str .= '},';
        }

        return "[" . substr($str, 0, -1) . "]";
    }

    /**
     * [getMenu 根据节点数据获取对应的菜单]
     * @author [田建龙] [864491238@qq.com]
     */
    public function getMenu($nodeStr = '')
    {
        //超级管理员没有节点数组
        $where = empty($nodeStr) ? 'status = 1' : 'status = 1 and id in('.$nodeStr.')';
        $result = Db::name('auth_rule')->where($where)->order('sort')->select();
        $menu = prepareMenu($result);
        return $menu;
    }
}