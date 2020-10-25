<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Doccate as LogicDoccate;
use app\common\logic\Groupcate as LogicGroupcate;

/**
 * 文档分类控制器
 */
class Doccate extends AdminBase
{
    
    /**
     * 文档分类逻辑
     */
	
    private static $doccateLogic = null;
    private static $groupcateLogic = null;
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$doccateLogic = get_sington_object('doccateLogic', LogicDoccate::class);
        self::$groupcateLogic = get_sington_object('groupcateLogic', LogicGroupcate::class);
    }

    
    /**
     * 文档分类列表
     */
    public function doccateList()
    {
        
        $where = self::$doccateLogic->getWhere($this->param);
       
        $lsarr=array();
        
        $groupcate_list = self::$groupcateLogic->getGroupcateList(['status'=>1], true, 'id desc',false);
        
        if(empty($groupcate_list)){
        	
        	$this->error('请先创建至少一个文档频道');
        }
        
        foreach ($groupcate_list as $k =>$v){
        
        	$lsarr[$v['id']]=$v;
        
        
        }
       
        $this->assign('groupcate_list',$lsarr);
        
        $this->assign('list', self::$doccateLogic->getDoccateList($where, true, 'id desc'));
        
       
        
        
       
       
       
       
       $this->assign('pid',  !empty($where['pid']) ? $where['pid'] : 0);
        return $this->fetch('doccate_list');
    }
    
    /**
     * 文档分类添加
     */
    public function doccateAdd()
    {
        
        IS_POST && $this->jump(self::$doccateLogic->doccateAdd($this->param));
        $this->assign('groupcate_list',self::$groupcateLogic->getGroupcateList(['status'=>1], true, 'id desc'));
        return $this->fetch('doccate_add');
    }
    /**
     * 文档分类编辑
     */
    public function doccateEdit()
    {
    	$info = self::$doccateLogic->getDoccateInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$doccateLogic->doccateEdit($this->param,$info));
    	
    	$this->assign('groupcate_list',self::$groupcateLogic->getGroupcateList(['status'=>1], true, 'id desc'));
    	$this->assign('info', $info);
    	return $this->fetch('doccate_edit');
    }
    /**
     * 文档分类批量删除
     */
    public function doccateAlldel($ids = 0)
    {
    
    	$this->jump(self::$doccateLogic->doccateAlldel($ids));
    }
    /**
     * 文档分类删除
     */
    public function doccateDel($id = 0)
    {
        
        $this->jump(self::$doccateLogic->doccateDel(['id' => $id]));
    }

}
