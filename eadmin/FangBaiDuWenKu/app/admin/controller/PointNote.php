<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;



/**
 * 文档控制器
 */
class PointNote extends AdminBase
{
    
    /**
     * 文档逻辑
     */
	
       
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
     
         
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
    
    	$where = [];
    
    	
    
    	if (!is_administrator()) {
    
    		 
    	}
    
    	return $where;
    }
    /**
     * 文档列表
     */
    public function point_noteList()
    {
        
        $where = $this->getWhere($this->param);
        
        $this->assign('list', parent::$commonLogic->getDataList('point_note',$where, true, 'id desc'));
       
       
        return $this->fetch('point_note_index');
    }
    
    /**
     * 文档添加
     */
    public function point_noteAdd()
    {
        
        IS_POST && $this->jump(parent::$commonLogic->dataAdd('point_note',$this->param));
        
        return $this->fetch('topic_add');
    }
    /**
     * 文档编辑
     */
    public function point_noteEdit()
    {
    	$info = parent::$commonLogic->getDataInfo('point_note',['id' => $this->param['id']]);
    	IS_POST && $this->jump(parent::$commonLogic->dataEdit('point_note',$this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('topic_edit');
    }
    /**
     * 文档批量删除
     */
    public function point_noteAlldel($ids = 0)
    {
    
    	$this->jump(parent::$commonLogic->dataDel('point_note',['id'=>array('in',$ids)]));
    }
    /**
     * 文档删除
     */
    public function point_noteDel($id = 0)
    {
        
        $this->jump(parent::$commonLogic->dataDel('point_note',['id' => $id]));
    }
    /**
     * 文档状态更新
     */
    public function point_noteCstatus($id = 0,$status,$field)
    {
    	
        $this->jump(parent::$commonLogic->setDataValue('point_note',['id' => $id],$field,$status));
    }
    /**
     * 文档审核
     */
    public function point_noteSh($id = 0,$status,$field)
    {
    
    	$this->jump(parent::$commonLogic->setDataValue('point_note',['id' => $id],$field,$status));
    }
    
    /**
     * 文档批量审核
     */
    public function point_noteAllSh($ids = 0)
    {
    
    	$this->jump(parent::$commonLogic->setDataValue('point_note',['id'=>array('in',$ids)],$field,$status));
    }
    

}
