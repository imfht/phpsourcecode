<?php
namespace app\common\model;

use think\Model;

class AuthRule extends Model
{
    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
    public function getLevelTurnAttr($value, $data)
    {
        $turnArr = [1=>'项目', 2=>'模块', 3=>'操作'];
        return $turnArr[$data['level']];
    }
    public function getIsmenuTurnAttr($value, $data)
    {
        $turnArr = [0=>'否', 1=>'是'];
        return $turnArr[$data['ismenu']];
    }

    public function treeList($module = '', $status = '')
    {
    	$where = [];
        if ($module != ''){
            $where = [
                'module' => $module
            ];
        }
        if ($status != ''){
            $where['status'] = $status;
        }
        $list = $this->where($where)->order('sorts ASC,id ASC')->select();
        $treeClass = new \expand\Tree();
        $list = $treeClass->create($list);
        return $list;
    }
}