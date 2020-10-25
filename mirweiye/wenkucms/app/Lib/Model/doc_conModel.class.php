<?php
class doc_conModel extends RelationModel
{

protected $_link = array(
        //关联角色
        'user' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'uid',
            'parent_key' => 'uid',
            'as_fields'=>'username',
            'auto_prefix' => true
        )
    );
    protected $_auto =array(
    
    array('add_time','time',1,'function'), // 对add_time字段在新增的时候写入当前时间戳
   
    
    
    
    );
    
    public function singletags($tags){
    	
    	
    	$tagarr=explode(',', $tags);
    	
    	
    	foreach ($tagarr as $key =>$value){
    	
    		if(D('tag')->name_exists($value)){
    			
    			$map['name']=$value;
    			D('tag')->where($map)->setInc('count',1);
    		}else{
    			$data['name']=$value;
    			
    			D('tag')->add($data);
    			
    		}
    		
    	}
    	
    	
    	
    	
    }
    
public function title_exists($title, $id = 0) {
        $where = "title='" . $title . "' AND id<>'" . $id . "'";
        $result = $this->where($where)->count('id');
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
public function hash($hash) {
        $map['hash']=$hash;
        $result = $this->where($map)->count('id');
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
}