<?php
namespace Home\Model;
use Think\Model;


class ArticleModel extends Model {

    /**
     * 查找后置操作
     */
    protected function _after_find(&$result,$options) {

    }

    protected function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }
    protected $_validate = array(
        array('title','require','标题必须！'), //默认情况下用正则进行验证
        array('title','','标题已存在',0,'unique',3),
        array('description','require','内容必须！'), //默认情况下用正则进行验证
         array('cid','require','分类必须！'), //默认情况下用正则进行验证
        
    );

    /**
     * 文件模型自动完成
     * @var array
     */
       protected $_auto = array(
        array('create_time', NOW_TIME, 1),
        array('update_time', NOW_TIME, 3),
        );
   
    public function get_info($id){
    	
    	$map['id']=$id;
    	$info=$this->where($map)->find();
        if($info===false||empty($info)){
    		return false;
    		
    	}
    	
    	
		$info['copyright']=str_replace('{link}',ZSU('/artc/'.$info['id'],'Index/artc',array('id'=>$info['id'])), $info['copyright']);
	
    	
    	return $info;
    	
    }


   
    
   
}
