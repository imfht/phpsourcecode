<?php
namespace app\admin\controller;




class Slideimg extends AdminBase
{
    

	
    
   
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
       
        
    }
    public function getWhere($data = [])
    {
    
    	$where = [];
    
    	$where['status']=1;
    	
    	
    	if (!is_administrator()) {
    
    		 
    	}
    
    	return $where;
    }

    public function slideimgList()
    {
    
    	
    	
    	$where = $this->getWhere($this->param);
    	
        
        
    	$this->assign('list', parent::$commonLogic->getDataList('slideimg',$where));

    	return $this->fetch('slideimg_list');
    }

    public function slideimgAdd()
    {
    
    	
    	

    	if(IS_POST){
    		
    	$data=$this->param;
    	
    	$this->jump(parent::$commonLogic->dataInsert('slideimg',$data,false,'添加成功'));
    	
    	}
    	
    	
    	return $this->fetch('slideimg_add');
    }

    public function slideimgEdit()
    {
    	$info = parent::$commonLogic->getDataInfo('slideimg',['id' => $this->param['id']]);
    	
    	if(IS_POST){
    		
    		$data=$this->param;
    		
    	    $this->jump(parent::$commonLogic->dataEdit('slideimg',$data,false));
    	}

    	$this->assign('info', $info);
    	
    	return $this->fetch('slideimg_edit');
    }
    /**
     * 批量删除
     */
    public function slideimgAlldel($ids = 0)
    {
    
    	$this->jump(parent::$commonLogic->dataDel('slideimg',['id'=>array('in',$ids)]));
    }
    /**
     * 删除
     */
    public function slideimgDel($id = 0)
    {
    
    	$this->jump(parent::$commonLogic->dataDel('slideimg',['id' => $id]));
    }
    /**
     * 状态更新
     */
    public function slideimgCstatus($id = 0,$status)
    {
    
    	$this->jump(parent::$commonLogic->setDataValue('slideimg',['id' => $id],'status',$status));
    }

}
