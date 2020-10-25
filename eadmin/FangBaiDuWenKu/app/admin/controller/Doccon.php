<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Doccon as LogicDoccon;
use app\common\logic\Doczj as LogicDoczj;
use app\common\logic\Doccate as LogicDoccate;
/**
 * 文档控制器
 */
class Doccon extends AdminBase
{
    
    /**
     * 文档逻辑
     */
	private static $doccateLogic = null;
    private static $docconLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        self::$doccateLogic = get_sington_object('doccateLogic', LogicDoccate::class);
        self::$docconLogic = get_sington_object('docconLogic', LogicDoccon::class);
    }

    
    /**
     * 文档列表
     */
    public function docconList()
    {
       
        $where = self::$docconLogic->getWhere($this->param);
        
        $this->assign('list', self::$docconLogic->getDocconList($where, true, 'id desc'));
       
        $doccate_list = self::$doccateLogic->getDoccateList(['status'=>1], true, 'id desc',false);
        $this->assign('doccate_list', $doccate_list);
      
        return $this->fetch('doccon_list');
    }
    
    /**
     * 文档添加
     */
    public function docconAdd()
    {
        
        IS_POST && $this->jump(self::$docconLogic->docconAdd($this->param));
        
        return $this->fetch('doccon_add');
    }
    /**
     * 文档批量添加
     */
    public function docconPiliang()
    {
    
    	$doccate_list = self::$doccateLogic->getDoccateList(['status'=>1], true, 'id desc',false);
    	$this->assign('doccate_list', $doccate_list);
    	
    	
    	IS_POST && $this->jump(self::$docconLogic->docconPiliang($this->param));
    
    	return $this->fetch('doccon_piliang');
    }
    /**
     * 文档编辑
     */
    public function docconEdit()
    {
    	$info = self::$docconLogic->getDocconInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$docconLogic->docconEdit($this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('doccon_edit');
    }
    /**
     * 文档批量删除
     */
    public function docconAlldel($ids = 0)
    {
    
    	$this->jump(self::$docconLogic->docconAlldel($ids));
    }
    /**
     * 文档删除
     */
    public function docconDel($id = 0)
    {
        
        $this->jump(self::$docconLogic->docconDel(['id' => $id]));
    }
    /**
     * 文档状态更新
     */
    public function docconCstatus($id = 0,$status,$field)
    {
        
        $this->jump(self::$docconLogic->setDocconValue(['id' => $id],$field,$status));
    }
    /**
     * 文档审核
     */
    public function docconSh($id = 0,$status,$field)
    {
    
    	$this->jump(self::$docconLogic->setDocconSh(['id' => $id],$field,$status));
    }
    
    /**
     * 文档批量审核
     */
    public function docconAllSh($ids = 0)
    {
    
    	$this->jump(self::$docconLogic->setDocconAllSh($ids));
    }
    /**
     * 文档批量审核
     */
    public function docconAll()
    {
    
    	$this->jump(self::$docconLogic->setDocconAll());
    }
    /**
     * 文档推送
     */
    public function docconTuisong($ids = 0)
    {
    	IS_POST && $this->jump(self::$docconLogic->docconTuisong($this->param));
    	$doczjLogic = get_sington_object('doczjLogic', LogicDoczj::class);
    	 $this->assign('list', $doczjLogic->getDoczjList(['status'=>1], true, 'id desc'));
    	$this->assign('ids', $ids);
    	return $this->fetch('doccon_ts');
    	
    }
    /**
     * 文档更改
     */
    public function docconChange($ids = 0)
    {
    	IS_POST && $this->jump(self::$docconLogic->docconChange($this->param));
    	
    	$this->assign('list', self::$doccateLogic->getDoccateList(['status'=>1], true, 'id desc',false));
    	$this->assign('ids', $ids);
    	return $this->fetch('doccon_change');
    	 
    }
}
