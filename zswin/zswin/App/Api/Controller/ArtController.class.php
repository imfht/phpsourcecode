<?php
namespace Api\Controller;

class ArtController extends ApiController
{

	protected $model;
	public function _initialize() {
		parent::_initialize();
		$this->model=D('Home/Article');
		
	}
	public function getArtInfo($id){
		
		$map['id']=$id;
		$info=$this->model->where($map)->find();
		$this->apiSuccess("获取文章内容成功", null, array('data'=>$info));	
		
	}
	
	public function getArt($cate,$child=true,$uid,$status,$order,$field=true,$row,$onetag,$title,$position,$limit,$focus){
		
			
		if($onetag!=''){
			$map['tag']=array('like','%'.$onetag.'%');
		}
	   if($position!=''){
			$map['tj']=array('in',$position);	
		}
	   if($title!=''){
			$map['title']=array('like','%'.$title.'%');
		}
		
		if($child){
			$cateids=D('Home/Cate')->getChildrenId($cate);
		}else{
			$cateids=$cate;
		}
		if($uid!=0){
			
		  $map['uid']=array('in',$uid);	
		}
	  
		
		
		if($cateids!=null){
			$map['cid']=array('in',$cateids);
		}
		
		$map['status']=array('in',$status);
		if($focus){
			
			$loginuid=is_login();
			$focusuid=M('focus')->where(array('id'=>$loginuid,'type'=>0))->getField('rowid',true);
			$focustag=M('focus')->where(array('id'=>$loginuid,'type'=>2))->getField('rowid',true);
			if($focustag!=null){
			foreach ($focustag as $key =>$vo){
				
				$focustagarr[$key]='%'.M('tags')->where(array('id'=>$vo))->getField('title').'%';
				
				
			}
			$where['tag']=array('like',$focustagarr,'OR');
			if($focusuid!=null){
			$where['uid']=array('in',$focusuid);
			$where['_logic'] = 'or';
			}
			$map['_complex'] = $where;
		   }else{
		   	if($focusuid!=null){
			$where['uid']=array('in',$focusuid);
			$map['_complex'] = $where;
			
			}else{
				
				
				$map['status']=-1;
			}
			
		   	
		   	
		   	
		   	
		   	
		   }
			
			
		}
		
		$count=$this->model->where($map)->count();
	
		if($count>0){
		
		
		$p=I(C('VAR_PAGE'));
		if($limit){
		$data=$this->model->where($map)->order($order)->limit($row)->field($field)->select();	
		}else{
		$data=$this->model->where($map)->order($order)->page(!empty($p)?$p:1,$row)->field($field)->select();	
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
					
					
				$data[$key]['img']=getImgs($vo['description'],0);
					
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
	public function getArtCount($cate,$child=true,$uid,$status,$onetag,$title,$position,$focus){
	if($onetag!=''){
			$map['tag']=array('like','%'.$onetag.'%');
		}
	   if($title!=''){
			$map['title']=array('like','%'.$title.'%');
		}
	 if($position!=''){
			$map['tj']=array('in',$position);	
		}
		if($child){
			$cateids=D('Home/Cate')->getChildrenId($cate);
		}else{
			$cateids=$cate;
		}
		if($uid!=0){
		  $map['uid']=array('in',$uid);	
		}
	if($cateids!=null){
			$map['cid']=array('in',$cateids);
		}
		$map['status']=array('in',$status);
		
		
		if($focus){
			
			$loginuid=is_login();
			$focusuid=M('focus')->where(array('id'=>$loginuid,'type'=>0))->getField('rowid',true);
			$focustag=M('focus')->where(array('id'=>$loginuid,'type'=>2))->getField('rowid',true);
			if($focustag!=null){
			foreach ($focustag as $key =>$vo){
				
				$focustagarr[$key]='%'.M('tags')->where(array('id'=>$vo))->getField('title').'%';
				
				
			}
			$where['tag']=array('like',$focustagarr,'OR');
			if($focusuid!=null){
			$where['uid']=array('in',$focusuid);
			$where['_logic'] = 'or';
			}
			$map['_complex'] = $where;
		   }else{
		   	if($focusuid!=null){
			$where['uid']=array('in',$focusuid);
			$map['_complex'] = $where;
			
			}else{
				
				
				$map['status']=-1;
			}
			
		   	
		   	
		   	
		   	
		   	
		   }
		}
		
		
		$data=$this->model->where($map)->count();
		
		if($data==null){
			$this->apiError("获取文章数失败", null);
		}else{
		    $this->apiSuccess("获取文章数成功", null, array('data'=>$data));	
		}
		
		
		
		
		
	}
	public function getNextArt($info,$sign){
		
		$map['id'] = array('gt', $info['id']);
		$map['status']=1;
		$map['create_time']=array('elt',time());
		switch ($sign){
			case 'user':
				
				$map['uid']=$info['uid'];
				
				
				break;
			case 'cate':
				$map['cid']=$info['cid'];
				break;
			case 'tag':
				
				$tagarr=explode(',', $info['tag']);
				
				foreach ($tagarr as $key => $vo){
					
					
					$tagarr[$key]='%'.$vo.'%';
					
				}
				$map['tag']=array('like',$tagarr,'OR');
				break;
			
		}
		$data=$this->model->field(true)->where($map)->find();
		
		if($data==null){
			$this->apiError("获取下一篇文章失败", null);
		}else{
		    $this->apiSuccess("获取下一篇文章成功", null, array('data'=>$data));	
		}
		
		
		
		
	}
public function getPreArt($info,$sign){
		
		$map['id'] = array('lt', $info['id']);
		$map['status']=1;
		$map['create_time']=array('elt',time());
		switch ($sign){
			case 'user':
				
				$map['uid']=$info['uid'];
				
				
				break;
			case 'cate':
				$map['cid']=$info['cid'];
				break;
			case 'tag':
				
				$tagarr=explode(',', $info['tag']);
				
				foreach ($tagarr as $key => $vo){
					
					
					$tagarr[$key]='%'.$vo.'%';
					
				}
				$map['tag']=array('like',$tagarr,'OR');
				break;
			
		}
		
		$data=$this->model->where($map)->field(true)->find();
		
		if($data==null){
			$this->apiError("获取上一篇文章失败", null);
		}else{
		    $this->apiSuccess("获取上一篇文章成功", null, array('data'=>$data));	
		}
		
		
		
		
	}

}