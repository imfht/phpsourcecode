<?php
/* 2017年2月13日 星期一 系统菜单模型
 *
 */
namespace app\common\model;
use app\common\model\BaseModel;
class Menu extends BaseModel{
    protected $table = 'sys_menu';
    protected $pk = 'listno';
    public function getMenuList($groupId){
        return $this->where(['groupid'=>$groupId])->field('url,code_mk,descrip,param')->select();
    }
}