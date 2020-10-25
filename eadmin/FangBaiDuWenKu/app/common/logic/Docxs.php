<?php

namespace app\common\logic;

/**
 * 悬赏逻辑
 */
class Docxs extends LogicBase
{
    
    // 会员模型
    public static $docxsModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$docxsModel = model($this->name);
    }
    
    /**
     * 获取悬赏信息
     */
    public function getDocxsInfo($where = [], $field = true)
    {
        
        return self::$docxsModel->getInfo($where, $field);
    }
    
    /**
     * 获取悬赏列表
     */
    public function getDocxsList($where = [], $field = true, $order = '')
    {
    	return self::$docxsModel->getList($where, 'm.*,user.username,doccate.name as tidname,groupcate.name as gidname', $order,0,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id'],['groupcate','m.gid=groupcate.id']]);
    }
    
    /**
     * 获取悬赏列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
                if(!empty($data['status'])&&$data['status']!=0){
            	
            	if($data['status']==3){
            		$where['m.status'] = 0;
            	}else{
            		$where['m.status'] = $data['status'];
            	}
            	
        }else{
        	$where['m.status']=array('in','0,1,2');
        }
        !empty($data['search_data']) && $where['name'] = ['like', '%'.$data['search_data'].'%'];
        
        if (!is_administrator()) {
            
         
        }
        
        return $where;
    }
    
  
    /**
     * 悬赏添加
     */
    public function docxsAdd($data = [])
    {
       
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('add')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
       
        return self::$docxsModel->setInfo($data) ? [RESULT_SUCCESS, '悬赏添加成功'] : [RESULT_ERROR, self::$docxsModel->getError()];
    }
    /**
     * 悬赏编辑
     */
    public function docxsEdit($data = [],$info)
    {
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('edit')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	 
       return self::$docxsModel->setInfo($data) ? [RESULT_SUCCESS, '悬赏编辑成功'] : [RESULT_ERROR, '1'];
    }
    /**
     * 设置悬赏信息
     */
    public function setDocxsValue($where = [], $field = '', $value = '')
    {
       
    	$list=self::$docxsModel->getInfo($where);
    	if($value==1&&$field=='status'){
    		
    		$content='您的悬赏<a href="'.routerurl('doc/docxscon',array('id'=>$list['id'])).'">'.$list['title'].'</a>已经审核通过';
    		
    		sendsysmess($content,0,$list['uid'],1);
    		
    		
    		point_change($list['uid'],'point',$list['score'],2,'docxsfb',$list['id'],0);
    		
    		point_controll($list['uid'],'docxsfb',$list['id']);
    	}
    	if($value==0&&$field=='status'){
    		point_change($list['uid'],'point',$list['score'],1,'docxsfb',$list['id'],0);
    		
    		$content='您的悬赏<a href="'.routerurl('doc/docxscon',array('id'=>$list['id'])).'">'.$list['title'].'</a>已经列为待审核';
    		
    		sendsysmess($content,0,$list['uid'],1);
    	}
    	
        return self::$docxsModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$docxsModel->getError()];
    }
    /**
     * 更新悬赏信息
     */
    public function setDocxsInfo($data)
    {
    
    
    	
    	return self::$docxsModel->setInfo($data) ? [RESULT_SUCCESS, '悬赏更新成功'] : [RESULT_ERROR, self::$docxsModel->getError()];
    }
    
    /**
     * 悬赏批量审核
     */
    public function setDocxsAllSh($ids)
    {
    	 $list=self::$docxsModel->getList(['id'=>array('in',$ids),'status'=>0], true,'',false);
    	 
    	 if($list){
    	 	
    	 	foreach ($list as $k =>$v){
    	 		
    	 		point_change($v['uid'],'point',$v['score'],2,'docxsfb',$v['id'],0);
    	 		
    	 		point_controll($v['uid'],'docxsfb',$v['id']);
    	 		
    	 		$content='您的悬赏<a href="'.routerurl('doc/docxscon',array('id'=>$v['id'])).'">'.$v['title'].'</a>已经审核通过';
    	 		
    	 		sendsysmess($content,0,$v['uid'],1);
    	 		
    	 	}
    	 	
    	 }
    	 
    
    	return self::$docxsModel->setFieldValue(['id'=>array('in',$ids)],'status',1) ? [RESULT_SUCCESS, '悬赏批量审核成功'] : [RESULT_ERROR, self::$docxsModel->getError()];
    }
    /**
     * 悬赏批量删除
     */
    public function docxsAlldel($ids)
    {
    	

    return self::$docxsModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '悬赏删除成功'] : [RESULT_ERROR, self::$docxsModel->getError()];
    }  
    /**
     * 悬赏删除
     */
    public function docxsDel($where = [])
    {
        
      
        
        return self::$docxsModel->deleteInfo($where) ? [RESULT_SUCCESS, '悬赏删除成功'] : [RESULT_ERROR, self::$docxsModel->getError()];
    }
}
