<?php
/**

 *
 * 文档自定义列表标签
 */
class docTag {
    

    public function lists($options) {
        $options['field'] = isset($options['field']) ? trim($options['field']) : '*';
        $options['where'] = isset($options['where']) ? trim($options['where']) : '';
        $options['cateid'] = isset($options['cateid']) ? trim($options['cateid']) : 0;
        $options['order'] = isset($options['order']) ? trim($options['order']) : 'id DESC';
        $options['day'] = isset($options['day']) ? intval($options['day']) : 0;
        $options['cache'] = isset($options['cache']) ? intval($options['cache']) : 60;
        $options['pagenum'] = isset($options['pagenum']) ? intval($options['pagenum']) : 0;
        $options['minscore'] = isset($options['minscore']) ? trim($options['minscore']) : 'min';
        $options['maxscore'] = isset($options['maxscore']) ? trim($options['maxscore']) : 'max';
        $options['ext'] = isset($options['ext']) ? trim($options['ext']) : 0;
        $options['usertype'] = isset($options['usertype']) ? trim($options['usertype']) : 0;
        $options['uid'] = isset($options['uid']) ? intval($options['uid']) : 0;
        $options['own'] = isset($options['own']) ? intval($options['own']) : 0;//为1时表示调用该uid的文档
        $options['how'] = isset($options['how']) ? trim($options['how']) : '';
        $options['search'] = isset($options['search']) ? trim($options['search']) : '';
        $options['similar'] = isset($options['similar']) ? trim($options['similar']) : 0;
        $options['status'] = isset($options['status']) ? trim($options['status']) : 0;
        $options['exceptid'] = isset($options['exceptid']) ? trim($options['exceptid']) : 0;//除了哪个文档不显示
        
        
        /*
         * how表示选择什么类型的文档，如down表示下载数最多
         * rate评分排行,wytj表示网友推荐的,wysc网友收藏
         * 
         * search如果有值，表示按照关键字去查询
         * similar如果有值，说明查找该id的相关文档
         * 
         */
        
        
        
        $doc_mod = M('doc_con');
        $select = $doc_mod->field($options['field']); //字段
        //条件
        if($options['exceptid']){
        $map['id']=array('neq',$options['exceptid']);
        }
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
        if($options['minscore']!='min'){
        $map['score']=array('egt',$options['minscore'])	;
        
        if($options['maxscore']!='max'){
        if($options['maxscore']>$options['minscore']){
          
        	$map['score']=array('between',array($options['minscore'],$options['maxscore']))	;
       
        }	
        }	
        
        }else{
        if($options['maxscore']!='max'){
           $map['score']=array('elt',$options['maxscore'])	;
       
        }
        
        }
        
        
        
       if($options['search']){
       	  
       	
       	
       	
       	
       	
       	
       	$searcharr=explode(' ', $options['search']);
       	delete_empty($searcharr);
       	foreach($searcharr as $keysear =>$valuesear){
       		
       		$searcharr[$keysear]='%'.$valuesear.'%';
       		
       	}
       	
       	$where['title'] =array('like',$searcharr,'OR');
       	$where['tags'] =array('like',$searcharr,'OR');
       	
       	//$where['title']=array('like','%'.$options['search'].'%');
    	//$where['tags']=array('like','%'.$options['search'].'%');
    	$where['_logic']='or';
    	$map['_complex'] = $where;
       }
       
     if($options['similar']){
       	$mapsimilar['id']=$options['similar'];
     	$tags=D('doc_con')->where($mapsimilar)->getField('tags');
     	$tagarr=explode(',', $tags);
     	
     	foreach ($tagarr as $key=>$value)
     	{
     		
     		$tagarr[$key]='%'.$value.'%';
     	}
     	
     	$where['title']=array('like',$tagarr,'OR');
     	
    	$where['tags']=array('like',$tagarr,'OR');
    	$where['_logic']='or';
    	$map['_complex'] = $where;
       }
       
        $options['where'] && $map['_string'] = $options['where'];
        $select->where($map); 
        //$options['pagenum'] && $select->limit($options['pagenum']); //每页的文档数量
        
        $select->order($options['order']); //排序
        //dump($select);
        //$options['pagenum'] = 1;
        if($options['pagenum']){
        	
       
        $count      = $select->count();// 查询满足要求的总记录数
        $Page       = new Page($count,$options['pagenum']);// 实例化分页类 传入总记录数和每页显示的记录数
        $data['page']       = $Page->show();// 分页显示输出
        $data['total'] = $Page->rollPage;
       
        $select->where($map)->order($options['order'])->limit($Page->firstRow.','.$Page->listRows);
       
        }
        
      $data['list']=$select->select();
        
     //  dump($map);
       // dump($options);
        
        
        
    foreach ($data['list'] as $key =>$value){
    	
    	
    	
        $data['list'][$key]['downnum']=downcount($value['id'],1);	
        $data['list'][$key]['raty']=getratyint(getitemraty($value['id'],1));
        $data['list'][$key]['ratynum']=getratynum($value['id'],1);
        
        $data['list'][$key]['commentnum']=comcount($value['id'],1);	
        $data['list'][$key]['wyscnum']=wysccount($value['id'],1);	
        $data['list'][$key]['wytjnum']=wytjcount($value['id'],1);					
        				
    }
        
        
        
        
        
        
        
        
        
        
        
        
    if($options['how']!=''){
    	
    	
    	
    	
    	
        	switch ($options['how']){
        		case 'down':
        			
        			//D('itemlog')->where($mapdown)->count('itemid');
        			foreach ($data['list'] as $key =>$value){
        				
        				$data['list'][$key]['comcount']=downcount($value['id'],1);	
        			}
        			usort($data['list'], 'cmpcomcount');
        			
        			
        			
        			
        			break;
        		
        		case 'rate':
        			
        			foreach ($data['list'] as $key =>$value){
        				
        				$data['list'][$key]['comcount']=getraty($value['id'],1);			
        			}
        			usort($data['list'], 'cmpcomcount');
        			
        			
        			
        			break;
        		
        		case 'wytj':
        			
        			foreach ($data['list'] as $key =>$value){
        			
        				$data['list'][$key]['comcount']=wytjcount($value['id'],1);
        			}
        			usort($data['list'], 'cmpcomcount');
        			break;
        		case 'wysc':
        			
        			foreach ($data['list'] as $key =>$value){
        				
        				$data['list'][$key]['comcount']=wysccount($value['id'],1);
        			}
        			usort($data['list'], 'cmpcomcount');
        			break;
        		case 'comment':
        			
        			foreach ($data['list'] as $key =>$value){
        				
        				$data['list'][$key]['comcount']=comcount($value['id'],1);	
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