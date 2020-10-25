<?php

namespace app\admin\model;
use think\Model;
use util\Tree;

//管理员常用菜单
class AdminMenu extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__ADMIN_MENU__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	//protected $pk = 'id';

    public static function getTreeList($map=[])
    {        
        $data_list = Tree::config(['title' => 'name'])->toList(
                self::where($map)->order('list desc,id asc')->column(true,'id')
                );
        return $data_list;
    }
}