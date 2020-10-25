<?php
/**

 *
 * 用户自定义列表标签
 */
class userTag {
    

    public function lists($options) {
        $options['field'] = isset($options['field']) ? trim($options['field']) : '*';
        $options['where'] = isset($options['where']) ? trim($options['where']) : '';
        $options['order'] = isset($options['order']) ? trim($options['order']) : 'id DESC';
        $options['roleid'] = isset($options['roleid']) ? intval($options['roleid']) : 0;
        $options['how'] = isset($options['how']) ? trim($options['how']) : '';
        $options['pagenum'] = isset($options['pagenum']) ? intval($options['pagenum']) : 0;
        
        /*
         * how表示选择什么类型的文档，如down表示下载数最多
         * score积分排行,doc上传排行         * 
        
         */
        
        
        
        $user_mod = M('user');
        $select = $user_mod->field($options['field']); //字段
        //条件
       
        
      
          $map['status'] = 1;
       
     if($options['roleid']){//如果填写了，表示查找该分类范围内的文档，可以为1,2,3,4用半角逗号隔开

       $rolearr= explode(',', $options['roleid']);
         
       $map['roleid']=array('in',$rolearr);
        }
      
     
       
        $options['where'] && $map['_string'] = $options['where'];
        $select->where($map);
    if($options['pagenum']){
        	
        
        $count      = $select->count();// 查询满足要求的总记录数
        $Page       = new Page($count,$options['pagenum']);// 实例化分页类 传入总记录数和每页显示的记录数
        $data['page']       = $Page->show();// 分页显示输出
           
        $select->limit($Page->firstRow.','.$Page->listRows);
         
        }
        
        $select->order($options['order']); //排序
        
        $data['list']=$select->select();
       //dump($map);
        if($options['how']!=''){
    	
    	
    	
    	
    	
        	switch ($options['how']){
        		case 'down':
        			
        			
        			foreach ($data['list'] as $key =>$value){
        				
        				$data['list'][$key]['comcount']=userdowncount($value['uid'],1);	
        			}
        			usort($data['list'], 'cmpcomcount');
        			
        			
        			
        			break;
        		
        		case 'score':
        			
        			foreach ($data['list'] as $key =>$value){
        				
        				$data['list'][$key]['comcount']=getuserscore($value['uid']);			
        			}
        			usort($data['list'], 'cmpcomcount');
        			
        			
        			
        			break;
        		
        		case 'doc':
        			
        			foreach ($data['list'] as $key =>$value){
        			
        				$mapdoc['uid']=$value['uid'];
        				$mapdoc['status']=array('gt',1);
        				$data['list'][$key]['comcount']=D('doc_con')->where($mapdoc)->count('id');
        			}
        			usort($data['list'], 'cmpcomcount');
        			break;
        		
        		default:
        			break;
        		
        		
        		
        	}
        	
        	
        	
        	
        }
        
        
        
        
        
        //dump($data);
        return $data;
    }
}