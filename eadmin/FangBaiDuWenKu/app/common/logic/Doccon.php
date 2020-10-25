<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;


/**
 * 文档逻辑
 */
class Doccon extends LogicBase
{
    
    // 文档模型
    public static $docconModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$docconModel = model($this->name);
    }
    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
    
    	$where = [];
            if(!empty($data['status'])&&$data['status']!=0){
            	
            	if($data['status']==2){
            		$where['m.status'] = 0;
            	}else{
            		$where['m.status'] = $data['status'];
            	}
            	
        	
        }else{
        	$where['m.status']=array('in','0,1');
        }
        
        if(!empty($data['tid'])&&$data['tid']>0){
        	 
       
        		$where['m.tid'] = $data['tid'];
        	
        	 
        	 
        }else{
        	
        }
        
    	!empty($data['search_data']) && $where['m.title'] = ['like', '%'.$data['search_data'].'%'];
    
    	if (!is_administrator()) {
    
    		 
    	}
    
    	return $where;
    }
    /**
     * 获取文档列表
     */
    public function getDocconList($where = [], $field = true, $order = '', $paginate = 0)
    {
    	
    	
    	return self::$docconModel->getList($where, 'm.*,user.username,doccate.name as tidname,doczj.name as zjname,groupcate.name as gidname,file.savepath,file.savename,file.ext', $order,$paginate,[['user','m.uid=user.id'],['doccate','m.tid=doccate.id','LEFT'],['groupcate','m.gid=groupcate.id','LEFT'],['file','m.fileid=file.id','LEFT'],['doczj','m.zjid=doczj.id','LEFT']]);
       // return self::$docconModel->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 获取文档信息
     */
    public function getDocconInfo($where = [], $field = true)
    {
        
        return self::$docconModel->getInfo($where, $field);
    }
    
    
    /**
     * 文档添加
     */
    public function docconAdd($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('configList');
        
        if(self::$docconModel->setInfo($data)){
        	
        	session('last_uploadid',null);
        	
        	return [RESULT_SUCCESS, '文档添加成功', $url];
        	
        }else{
        	
        	return [RESULT_ERROR, self::$docconModel->getError()];
        	
        }
        
        
    }
    
    /**
     * 文档编辑
     */
    public function docconEdit($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('edit')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('configList');
        
        return self::$docconModel->setInfo($data) ? [RESULT_SUCCESS, '文档编辑成功', $url] : [RESULT_ERROR, self::$docconModel->getError()];
    }
    /**
     * 设置文档信息
     */
    public function setDocconValue($where = [], $field = '', $value = '')
    {
    	 
    	return self::$docconModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    }
    /**
     * 更新文档信息
     */
    public function setDocconInfo($data)
    {
    
    	return self::$docconModel->setInfo($data) ? [RESULT_SUCCESS, '文档更新成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    }
    
    /**
     * 文档审核
     */
    public function setDocconSh($where = [], $field = '', $value = '')
    {
    
    	
    	
    	if(self::$docconModel->setFieldValue($where, $field, $value)){
    		
    		$info = $this->getDocconInfo($where);
    		
    		if($info['pageid']==0&&$field=='status'&&$value==1){
    			
    			point_controll($info['uid'],'docupload',$info['id']);

    		
    			
    			
    			
    			$httpstr = http_curl(url('admin/Ybcommand/copyfile'),array('fileid'=>$info['fileid'],'id'=>$info['id']), 'POST');
    			
    			$content='您的文档<a href="'.routerurl('doc/doccon',array('id'=>$info['id'])).'">'.$info['title'].'</a>已经审核通过';
    				
    			sendsysmess($content,0,$info['uid'],1);
    			
    			
    			if($info['xsid']>0){
    					
    				$xsinfo=model('docxs')->where(['id'=>$info['xsid']])->find();
    					
    				$content='您的悬赏<a href="'.routerurl('doc/docxscon',array('id'=>$info['xsid'])).'">'.$xsinfo['title'].'</a>有了新文档';
    			
    				sendsysmess($content,0,$xsinfo['uid'],1);
    			}
    			
    		}else{
    			if($field=='status'&&$value==1){
    				
    				$content='您的文档<a href="'.routerurl('doc/doccon',array('id'=>$info['id'])).'">'.$info['title'].'</a>已经审核通过';
    				 
    				sendsysmess($content,0,$info['uid'],1);
    				
    				
    				if($info['xsid']>0){
    					
    					$xsinfo=model('docxs')->where(['id'=>$info['xsid']])->find();
    					
    					$content='您的悬赏<a href="'.routerurl('doc/docxscon',array('id'=>$info['xsid'])).'">'.$xsinfo['title'].'</a>有了新文档';
    						
    					sendsysmess($content,0,$xsinfo['uid'],1);
    				}
    				
    			}
    			
    			
    			
    			if($field=='status'&&$value==0){
    				
    				$content='您的文档<a href="'.routerurl('doc/doccon',array('id'=>$info['id'])).'">'.$info['title'].'</a>已经被列为待审核';
    				 
    				sendsysmess($content,0,$info['uid'],1);
    				
    				roll_point_controll($info['uid'],'docupload',$info['id']);//这个是回滚积分操作，如果审核不通过了，就退回积分
    				
    			}
    			
    			
    		}
    		
    		
    		return [RESULT_SUCCESS, '状态更新成功'];
    		
    	}else{
    		
    		return [RESULT_ERROR, self::$docconModel->getError()];
    		
    	}
    	
    	
    	
    }
    
    /**
     * 文档批量审核
     */
    public function setDocconAll()
    {
    	
    	
    	$httpstr = http_curl(url('admin/Ybcommand/docconall'),array('arr'=>1), 'POST');
    
    	return [RESULT_SUCCESS, '后台正在审核中，耐心等候'];
    }
    
    
    /**
     * 文档批量审核
     */
    public function setDocconAllSh($ids)
    {

    	
    	
    	if(self::$docconModel->setFieldValue(['id'=>array('in',$ids)],'status',1)){
    		
    		foreach ($ids as $k =>$v){
    			
    			$info = $this->getDocconInfo(['id'=>$v]);
    			
    			$content='您的文档<a href="'.routerurl('doc/doccon',array('id'=>$info['id'])).'">'.$info['title'].'</a>已经审核通过';
    				
    			sendsysmess($content,0,$info['uid'],1);
    			
    			
    			if($info['xsid']>0){
    					
    				$xsinfo=model('docxs')->where(['id'=>$info['xsid']])->find();
    					
    				$content='您的悬赏<a href="'.routerurl('doc/docxscon',array('id'=>$info['xsid'])).'">'.$xsinfo['title'].'</a>有了新文档';
    			
    				sendsysmess($content,0,$xsinfo['uid'],1);
    			}
    			
    			
    			if($info['pageid']==0){
    				 
    				point_controll($info['uid'],'docupload',$info['id']);
    				 
    				$httpstr = http_curl(url('admin/Ybcommand/copyfile'),array('fileid'=>$info['fileid'],'id'=>$info['id']), 'POST');
    				
    				
    				 
    			}   			
    			
    			
    			
    		}
    		
    		
    		
    		return [RESULT_SUCCESS, '文档批量审核成功'];
    		
    	}else{
    		
    		return [RESULT_ERROR, self::$docconModel->getError()];
    		
    	}
    
    	
    }
    
    /**
     * 文档删除
     */
    public function docconDel($where = [])
    {
        
        return self::$docconModel->deleteInfo($where) ? [RESULT_SUCCESS, '文档删除成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    }
    
    /**
     * 批量删除
     */
    public function docconAlldel($ids)
    {
    	

    return self::$docconModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '文档删除成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    }  

    /**
     * 推送
     */
    public function docconTuisong($data)
    {
    	 $ids=explode('-', $data['ids']);
    	return self::$docconModel->setFieldValue(['id'=>array('in',$ids)], 'zjid', $data['zjid']) ? [RESULT_SUCCESS, '推送成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    	//return self::$docconModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '文档删除成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    }
    /**
     * 推送
     */
    public function docconChange($data)
    {
    	$ids=explode('-', $data['ids']);
    	
    	$data['tid']=$data['tid'];
    	$pid=model('doccate')->where(['id'=>$data['tid']])->value('pid');
    	$data['gid']=$pid;
    	return self::$docconModel->setInfo($data,['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '更改成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    	//return self::$docconModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '文档删除成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    }
   
    public function docconPiliang($data)
    {
    	$ids=explode(',', $data['cover_id']);
    	 
    	$n['tid']=$data['tid'];
    	$pid=model('doccate')->where(['id'=>$data['tid']])->value('pid');
    	$n['gid']=$pid;
    	
    	foreach ($ids as $k => $v){
    		$n['fileid']=$v;
    		$n['uid']=1;
    		$fileinfo=model('file')->where(['id'=>$v])->find();
    		$n['filename']=$fileinfo['name'];
    		$n['title']=$fileinfo['name'];
    		$n['description']=$fileinfo['name'];
    		$n['status']=0;
    		
    		
    		
    		
    		if($v>0){
    			if(model('doccon')->where(array('fileid'=>$v))->count()>0){
    				 
    			}else{
    				$result=self::$docconModel->addInfo($n);
    			}
    			
    		}else{
    			
    		}
    		
    		
    	}
    	
    	
    	return  [RESULT_SUCCESS, '更改成功'] ;
    	//return self::$docconModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '文档删除成功'] : [RESULT_ERROR, self::$docconModel->getError()];
    }
}
