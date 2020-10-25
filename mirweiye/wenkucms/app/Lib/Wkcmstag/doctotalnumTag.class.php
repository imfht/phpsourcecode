<?php
/**

 *
 * 文档自定义列表标签
 */
class doctotalnumTag {
    

    public function lists($options) {
        $options['field'] = isset($options['field']) ? trim($options['field']) : '*';
        $options['where'] = isset($options['where']) ? trim($options['where']) : '';
        $options['cateid'] = isset($options['cateid']) ? trim($options['cateid']) : 0;
        $options['order'] = isset($options['order']) ? trim($options['order']) : 'id DESC';
        $options['day'] = isset($options['day']) ? intval($options['day']) : 0;
          $options['cache'] = isset($options['cache']) ? intval($options['cache']) : 60;
        $options['ext'] = isset($options['ext']) ? trim($options['ext']) : 0;
        $options['usertype'] = isset($options['usertype']) ? trim($options['usertype']) : 0;
         $options['uid'] = isset($options['uid']) ? intval($options['uid']) : 0;
        $options['status'] = isset($options['status']) ? trim($options['status']) : 0;
        $options['own'] = isset($options['own']) ? intval($options['own']) : 0;
        
       
        
        
        
        $doc_mod = M('doc_con');
        $select = $doc_mod->field($options['field']); //字段
        //条件
        
        if($options['ext']){
        $map['ext']=array('in',$options['ext']);
        }
     if($options['uid']){
        	
        	
        	if($options['own']){	
        $map['uid']=array('eq',$options['uid']);
        	}elseif($options['usertype']){
        switch ($options['usertype'])	{
        	
        	case 'down':
        		$idarr=userdownitemid($options['uid'],1);
        		break;
        	case 'comment':
        		$idarr=usercomitemid($options['uid'],1);
        		break;
        	case 'wysc':
        		$idarr=userwyscitemid($options['uid'],1);
        		break;
        	case 'wytj':
        		$idarr=userwytjitemid($options['uid'],1);
        		break;
        	default:
        		break;
        	
        	
        	
        	
        }
        
        	
        $map['id']	=array('in',$idarr);
        
        }
        }
        $now=time();
        
        if($options['day']){//如果填写了天数，表示查找该范围内的文档
        $map['add_time']=array('egt',$now-$options['day']*24*3600);
        }else{
        $map['add_time']=array('elt',$now-$options['cache']);	
        	
        }
        if($options['cateid']){//如果填写了，表示查找该分类范围内的文档，可以为1,2,3,4用半角逗号隔开

       
         
            $map['cateid']=array('in',$options['cateid']);
        }
        
        
       if($options['status']){
       	$map['status'] = array('eq',$options['status']);
       }else{
          $map['status'] = array('gt',1);
       }

        
        


       
        $options['where'] && $map['_string'] = $options['where'];
        $select->where($map); 

        $select->order($options['order']); //排序

        
     $data=$select->select();

        
    foreach ($data as $key =>$value){
    	
    	
    	
        $data[$key]['downnum']=downcount($value['id'],1);

        
        $data[$key]['raty']=getitemraty($value['id'],1);	
        $data[$key]['ratynum']=getratynum($value['id'],1);
        
        $data[$key]['commentnum']=comcount($value['id'],1);	
        $data[$key]['wyscnum']=wysccount($value['id'],1);	
        $data[$key]['wytjnum']=wytjcount($value['id'],1);					
        				
    }
      $total['down']=0; 
      $total['hits']=0;
      $total['score']=0;
      $total['raty']=0; 
      $total['doc']=0; 
    foreach($data as $key =>$value){
    	
    	
    	$total['down']=$total['down']+$value['downnum'];
    	$total['hits']=$total['hits']+$value['hits'];
    	$total['score']=$total['score']+$value['score'];
    	$total['raty']=$total['raty']+$value['raty'];
    	$total['doc']=$total['doc']+1;
    	
    }    
        
        
        
        
        
        
       // dump($total);
   
        
        
        
        return $total;
    }
}