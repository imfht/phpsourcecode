<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Doczj as LogicDoczj;


/**
 * 文档专辑控制器
 */
class Doczj extends AdminBase
{
    
    /**
     * 文档专辑逻辑
     */
	
    private static $doczjLogic = null;
   
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$doczjLogic = get_sington_object('doczjLogic', LogicDoczj::class);
        
    }

    
    /**
     * 文档专辑列表
     */
    public function doczjList()
    {
        
        $where = self::$doczjLogic->getWhere($this->param);
       
        
        
        
        $this->assign('list', self::$doczjLogic->getDoczjList($where, true, 'id desc'));
      
        return $this->fetch('doczj_list');
    }
    
    /**
     * 文档专辑添加
     */
    public function doczjAdd()
    {
        
        IS_POST && $this->jump(self::$doczjLogic->doczjAdd($this->param));
       
        return $this->fetch('doczj_add');
    }
    /**
     * 文档专辑编辑
     */
    public function doczjEdit()
    {
    	$info = self::$doczjLogic->getDoczjInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$doczjLogic->doczjEdit($this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('doczj_edit');
    }
    /**
     * 文档专辑批量删除
     */
    public function doczjAlldel($ids = 0)
    {
    
    	$this->jump(self::$doczjLogic->doczjAlldel($ids));
    }
    /**
     * 文档专辑删除
     */
    public function doczjDel($id = 0)
    {
        
        $this->jump(self::$doczjLogic->doczjDel(['id' => $id]));
    }

}
