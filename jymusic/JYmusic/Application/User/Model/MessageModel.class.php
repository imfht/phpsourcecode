<?php
// +----------------------------------------------------------------------
// | Author: 战神~~巴蒂
// +----------------------------------------------------------------------

namespace User\Model;
use Think\Model;


class MessageModel extends Model {
	protected $_map = array(   
		'con' 	=>'content',
		'toid' 	=>'to_uid', 
		'reply' =>  'reply_id',  
	);
	
    protected $_validate = array(
    	array('to_uid', 'require', '接收用户不存在', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
    	array('reply_id', 'number', '参数错误', self::VALUE_VALIDATE ,'regex', self::MODEL_BOTH),
        array('content', 'require', '内容不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),     	
      	array('content', '5,500', '字段长度5-255字符', self::MUST_VALIDATE ,'length', self::MODEL_BOTH),
      	
    );

    protected $_auto = array(
        array('post_time', NOW_TIME, self::MODEL_INSERT),
        array('post_uid',UID,self::MODEL_BOTH),
        array('content','htmlentities',3,'function') , 
        array('content','htmlspecialchars',3,'function') ,
        array('content','remove_xss',3,'function'),
        array('post_uname','getName',3,'callback'),
    );
    
    protected  function getName () {
     	return get_nickName(UID);
    }
    
    public function checkpost() {
    	$post = cookie('post');
 		if (empty($post)){  	
	    	$map['post_uid'] = UID;
	    	$map['post_time'] = array('gt',strtotime("-1 hour"));
	    	//检测 一个小时发送条数
	    	$count = $this->where($map)->count();
	    	if ($count > 10 ){
	    		cookie('post',md5(UID.'postleter'),3600);
	    		return false;
	    	}else{
	    		$map['post_time'] = array('gt',strtotime("-1 day"));
	    		$count = $this->where($map)->count();
	    		if ($count > 100 ){
	    			cookie('post',md5(UID.'postleter'),3600);
	    			return false;
	    		}else{
	    			return true;
	    		}
	    	}
    	}else{
    		return false;
    	}
    }
            
}
