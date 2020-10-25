<?php
namespace Api\Controller;

class FocusController extends ApiController
{

	protected $model;
	public function _initialize() {
		parent::_initialize();
		$this->model=D('Home/Focus');
		
	}
	public function getTag($uid,$row,$limit){
	
		$mapfocus['id']=$uid;
		$mapfocus['type']=2;
		
	
	
		$count=$this->model->where($mapfocus)->count();
	
		if($count>0){
	
	
			$p=I(C('VAR_PAGE'));
			if($limit){
				$data=$this->model->where($mapfocus)->order('rowid desc')->limit($row)->select();
			}else{
				$data=$this->model->where($mapfocus)->order('rowid desc')->page(!empty($p)?$p:1,$row)->select();
			}
	
		}
	
	
		if($data==null){
			$this->apiError("获取标签关注列表失败", null);
		}else{
	
	
			foreach ($data as $key =>$vo){
					
	             $data[$key]['tag']=gettaginfo($vo['rowid']);
	             
	             $data[$key]['tag']['path']=getThumbImageById($data[$key]['tag']['img']);
	             
	             
	             $data[$key]['hasfocustag']=hasguanzhu($vo['rowid'],$uid,2);
					
			}
			
			$this->apiSuccess("获取标签关注列表成功", null, array('data'=>$data));
		}
	
	
	}
	public function getTagCount($uid){
	
		$mapfocus['id']=$uid;
		$mapfocus['type']=2;
		$data==$this->model->where($mapfocus)->count();
		
		if($data==null){
			$this->apiError("获取标签关注数失败", null);
		}else{
			$this->apiSuccess("获取标签关注数成功", null, array('data'=>$data));
		}
	
	
	
	
	
	}
	public function getUser($uid,$row,$limit){
	
		$mapfocus['id']=$uid;
		$mapfocus['type']=0;
		
	
	
		$count=$this->model->where($mapfocus)->count();
	
		if($count>0){
	
	
			$p=I(C('VAR_PAGE'));
			if($limit){
				$data=$this->model->where($mapfocus)->order('rowid desc')->limit($row)->select();
			}else{
				$data=$this->model->where($mapfocus)->order('rowid desc')->page(!empty($p)?$p:1,$row)->select();
			}
	
		}
	
	
		if($data==null){
			$this->apiError("获取用户关注列表失败", null);
		}else{
	
	
			foreach ($data as $key =>$vo){
					
	            $data[$key]['user']=query_user(array('zan','uid','fensi','score','artnum','nickname','username','space_url','avatar32','avatar64'),$vo['rowid']);
	             $data[$key]['hasfocususer']=hasguanzhu($vo['rowid'],$uid,0);
	             
	            
				$data[$key]['hufen']=hasguanzhu($uid,$vo['rowid'],0);	
			}
			
			$this->apiSuccess("获取用户关注列表成功", null, array('data'=>$data));
		}
	
	
	}
	public function getUserCount($uid){
	
		$mapfocus['id']=$uid;
		$mapfocus['type']=0;
		$data==$this->model->where($mapfocus)->count();
		
		if($data==null){
			$this->apiError("获取用户关注数失败", null);
		}else{
			$this->apiSuccess("获取用户关注数成功", null, array('data'=>$data));
		}
	
	
	
	
	
	}
	
	public function getArt($uid,$status,$order,$field=true,$row,$position,$limit){
		
		$mapfocus['id']=$uid;
		$mapfocus['type']=1;
		$rowarr=$this->model->where($mapfocus)->getField('rowid',true);	
	
		$map['id']=array('in',$rowarr);
		
	   if($position!=''){
			$map['tj']=array('in',$position);	
		}
	    
		
	
		
	
		$map['status']=array('in',$status);
		
		$count=D('Home/Article')->where($map)->count();
	
		if($count>0){
		
		
		$p=I(C('VAR_PAGE'));
		if($limit){
		$data=D('Home/Article')->where($map)->order($order)->limit($row)->field($field)->select();	
		}else{
		$data=D('Home/Article')->where($map)->order($order)->page(!empty($p)?$p:1,$row)->field($field)->select();	
		}
		
		}
		
		
		if($data==null){
			$this->apiError("获取文章列表失败", null);
		}else{


			foreach ($data as $key =>$vo){
					

				
				
				
				if($vo['tj']==1){
					$data[$key]['titleicon']='[<i class="icon-thumbs-up"></i> 推荐]';
			
				}
				if($vo['tj']==2){
					$data[$key]['titleicon']='[<i class="icon-arrow-up"></i> 置顶]';
			
				}
					
				$data[$key]['yesedit']=getarteditauth($vo['id'],$uid)||is_admin($uid);
					
					
				$data[$key]['img']=getImgs($vo['description'],1);
					
				$data[$key]['user']=query_user(array('nickname','username','space_url','avatar32','avatar64'),$vo['uid']);
					
				if($title!=''){
					$data[$key]['title']=str_replace($title, '<b style="color:red">'.$title.'</b>', $vo['title']);
				}
				if(!empty($vo['tag'])){
					$tags=explode(',', $vo['tag']);
					$data[$key]['linktag']='';
						
					foreach($tags as $key1 =>$vo1){
						$maptag['title']=$vo1;
						$maptag['type']=1;
						$tagid=M('tags')->where($maptag)->getField('id');
						$url=ZSU('/tagart/'.$tagid,'Index/tagart',array('id'=>$tagid));
			
			
			
						if($onetag!=''&&$onetag==$vo1){
							$data[$key]['linktag'].='<a style="margin-left:5px;color:red;" href="'.$url.'">['.$vo1.']</a>';
						}else{
							$data[$key]['linktag'].='<a style="margin-left:5px;" href="'.$url.'">['.$vo1.']</a>';
						}
			
					}
				}
					
			
					
					
			}
		    $this->apiSuccess("获取文章列表成功", null, array('data'=>$data));	
		}
		
		
	}
	public function getArtCount($uid,$status,$position){
	
		$mapfocus['id']=$uid;
		$mapfocus['type']=1;
		$rowarr=$this->model->where($mapfocus)->getField('rowid',true);	
	if($position!=''){
			$map['tj']=array('in',$position);	
		}
		$map['id']=array('in',$rowarr);
	
		$map['status']=array('in',$status);
		
		$data=D('Home/Article')->where($map)->count();
		
		if($data==null){
			$this->apiError("获取文章数失败", null);
		}else{
		    $this->apiSuccess("获取文章数成功", null, array('data'=>$data));	
		}
		
		
		
		
		
	}
	

}