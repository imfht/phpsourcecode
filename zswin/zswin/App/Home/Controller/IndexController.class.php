<?php
namespace Home\Controller;
use Org\Util\Tree;

class IndexController extends HomeController{
	
	public function _initialize(){
		
		
		parent::_initialize();
	}
	
	
	//系统首页
    public function index(){
    	
       //一级分类大于2时首页才显示
       if(M('cate')->where(array('type'=>1,'pid'=>0,'status'=>1))->count()>1){
       	$idarr=M('cate')->where(array('type'=>1,'pid'=>0,'status'=>1))->order('id asc')->limit(2)->getField('id',true);
       	$this->assign('idarr',$idarr);
       }else{
        	$this->assign('catenull',true);
        }
        
    
    
        $this->display();
    }
    
    public function zanart(){
    	$this->display();
    }
    public function hotart(){
    	$this->display();
    }
   public function gzart(){
    	$this->display();
    }
    public function artc(){

    	
    	
    	$id=I('id');
    	
    	$info=D('Article')->get_info($id);
    	if(!$info){
    		$this->error('非法ID！',U('Index/index'),false,true);
    		
    	}
    	
    	
    	$info['description']=stripcslashes($info['description']);
    	
    	
    	
    	preg_match_all("/(?<=\[attach\])([\d]*)(?=\[\/attach\])/", $info['description'],$arr);
    	preg_match_all("/(?<=\[qnattach\])([\d]*)(?=\[\/qnattach\])/", $info['description'],$qnarr);
    	foreach ($arr[0] as $key =>$vo){
    		
    		
    		$replace='<a href="'.U('File/download',array('id'=>think_encrypt($vo))).'" target="_blank" >'.getattachname($vo).'</a><span class="attachspan">(下载次数:'.getattachdnum($vo).'次;大小:'.format_bytes(getattachsize($vo)).')</span>';
    		$info['description']=str_replace('[attach]'.$vo.'[/attach]', $replace, $info['description']);
    		
    		
    	}
    foreach ($qnarr[0] as $key1 =>$vo1){
    		
    		
    		$replace1='<a href="'.U('File/download',array('qn'=>1,'id'=>think_encrypt($vo1))).'" target="_blank" >'.getqnattachname($vo1).'</a><span class="attachspan">(下载次数:'.getqnattachdnum($vo1).'次;大小:'.format_bytes(getqnattachsize($vo1)).')</span>';
    		$info['description']=str_replace('[qnattach]'.$vo1.'[/qnattach]', $replace1, $info['description']);
    		
    		
    	}
    	
    	if($info['status']!=1&&$info['uid']!=$_SESSION['zs_home']['user_auth']['uid']){
    		
    		$this->error('你无权查看该内容！','',false,true);
    		
    	}
    	
    	$focus['rowid']=$info['id'];
    	$focus['type']=1;
    	if($sccount=M('focus')->where($focus)->count()!=$info['sccount']){
    		
    		D('Article')->where(array('id'=>$id))->setField('sccount',$sccount);
    	}
    	
    	D('Article')->where(array('id'=>$id))->setInc('view',1);	
			
			if(!empty($info['tag'])){
			$tags=explode(',', $info['tag']);
			$info['linktag']='';
			
			foreach($tags as $key1 =>$vo1){
				$maptag['title']=$vo1;
				$maptag['type']=1;
				$tagid=M('tags')->where($maptag)->getField('id');
				
				$url=ZSU('/tagart/'.$tagid,'Index/tagart',array('id'=>$tagid));
				$info['linktag'].='<a style="margin-left:5px;" href="'.$url.'">['.$vo1.']</a>';	
				
				$info['tagarr'][$key1]='<a class="tag" href="'.$url.'">'.$vo1.'</a>';
			}
			}
			
       
		$shareurl='http://'.$_SERVER['HTTP_HOST'].ZSU('/artc/'.$info['id'],'Index/artc',array('id'=>$info['id']));	
		$sharedes='原文链接：'.$shareurl;	
    	$sharetitle=$info['title'].'-'.C('WEB_SITE_TITLE');
    	
    	$authorinfo=query_user(array('space_url','signature','avatar64','nickname'),$info['uid']);
    
    	
    	
    	
    	$map['id']=is_login();
    	$map['rowid']=$info['id'];
    	$map['type']=1;
    	
    	if(M('Focus')->where($map)->count()>0){
    		$hassc=true;
    	}
    	$this->assign('shareurl',$shareurl);
    	$this->assign('hassc',$hassc);
    	$this->assign('sharedes',$sharedes);
    	$this->assign('sharetitle',$sharetitle);
    	
    	$this->assign('authorinfo',$authorinfo);
    	$this->assign('info',$info);
    	$this->assign('webdescription',$info['title']);
    	$this->assign('webkeyword',$info['title']);
    	$this->assign('webtitle',$info['title']);
        
        $this->display();
    }
    public function search(){
        $keyword=I('keyword','','strip_tags');
       
        $this->assign('keyword',$keyword);  
      
           $this->assign('webdescription',$keyword);
    	$this->assign('webkeyword',$keyword);
    	$this->assign('webtitle',$keyword);   
        $this->display();
    }
    public function alltag(){
        
    
    	$this->assign('webdescription',$keyword);
    	$this->assign('webkeyword',$keyword);
    	$this->assign('webtitle',$keyword);
    	$this->display();
    }
public function tagart(){
        $id=I('id',0);
        
        if($id==0){
        	$this->error('请选择一个标签查询！',U('Index/index'),false,true,'Public:404');
        	
        }
        
        
        $hasgz=hasguanzhu($id, is_login(), 2);
         $this->assign('hasgz',$hasgz);
	    $map['id']=$id;
	    $tag=M('tags')->where($map)->getField('title');
	    $taginfo=M('tags')->where($map)->find();
	    $taginfo['path']=getThumbImageById($taginfo['img']);
	    $focus['rowid']=$id;
	    $focus['type']=2;
	    $focusnum=M('focus')->where($focus)->count();
	    $this->assign('focusnum',$focusnum);
	    if($taginfo['des']==0){
	    	$taginfo['des']='暂无介绍';
	    }
       $this->assign('webdescription',$tag);
    	$this->assign('webkeyword',$tag);
    	$this->assign('webtitle',$tag);
        $this->assign('tag',$tag);
        $this->assign('taginfo',$taginfo);
        $this->display();
    }
    public function artlist(){
    	
    	 $cid=I('cid',0,'int');
    	 if($cid==0){
    	 	$this->error('请选择一个分类！',U('Index/index'),false,true,'Public:404');
    	 	
    	 }
    	if($cid=='all'){
    		//$cid=0;
    	}
        $m = D('cate');
       
        $catelist = $m->field('*,CONCAT(spid,id) as path2')->where(array('type'=>1,'status'=>1))->order('path2')->select();
      
        $t = new tree();
        $catelistarr = $t->unlimitCategoryFormat($catelist);
        $catehtml=$t->treeFormat($catelistarr);
      
      
        $this->assign('cid',$cid);  
        $this->assign('cparent',getcidparent($cid));  
        
        $this->assign('webdescription',get_cate_nameByid($cid));
    	$this->assign('webkeyword',get_cate_nameByid($cid));
    	$this->assign('webtitle',get_cate_nameByid($cid));
    	
    	
        $this->assign('catehtml',$catehtml);  
          
        $cateinfo=M('cate')->where(array('id'=>$cid))->find();
        
        $cateinfo['path']=getThumbImageById($cateinfo['img']);
       
        if($cateinfo['des']==''){
        	//dump($cateinfo['des']==0);
        	//$cateinfo['des']='暂无介绍';
        }
        $this->assign('cateinfo',$cateinfo);
    	$this->display();
    }
    public function ding(){
    	
    	$uid=is_login();
    	$id=I('get.id');
    	if(cookie($uid.'ding'.$id)!=''||cookie($uid.'cai'.$id)!=''){
    		$this->error('你已经对该内容进行过操作！');
    	}
    	$res=M('Article')->where(array('id'=>$id))->setInc('ding',1);
    	if($res===false){
    		$this->error('操作失败');
    	}else{
    		cookie($uid.'ding'.$id,1);
    		$this->success('操作成功','',array('id'=>'ding'));
    	}
    	
    }
 public function focusnum(){
 	
    	$uid=is_login();
    	if($uid==0){
    		 
    		$this->error('请登陆后操作！');
    	}
    	$id=I('get.id');
    	$type=I('get.type');
    	$mark=I('get.mark');
    	$map['id']=$uid;
    	$map['rowid']=$id;
    	$map['type']=$type;
    	
    	if(M('Focus')->where($map)->count()>0){
    		$res=D('focus')->where($map)->delete();
    		$isdel=1;
    	}else{
    		$map['create_time']=time();
    		$res=M('focus')->add($map);
    		
    	}
    	
    	
    	
    	
    	
    	if($res===false){
    		$this->error('操作失败');
    	}else{
    		
    		if($isdel==1){
    			if($type==1){
    			M('Article')->where(array('id'=>$id))->setInc('sccount',1);
    			}
    			$this->success('操作成功','',array('id'=>$id,'del'=>1,'mark'=>$mark));
    		}else{
    			if($type==1){
    			M('Article')->where(array('id'=>$id))->setDec('sccount',1);
    			}
    			$this->success('操作成功','',array('id'=>$id,'mark'=>$mark));
    		}
    		
    		
    	}
    	
    }

    public function guanzhu(){
    	 
    	$uid=is_login();
    	$id=I('get.id');//要关注的人
    	$type=I('get.type');
    	
    	if($uid==0){
    	
    		$this->error('请登陆后操作！');
    	}
    	
    	
    	if($type==0&&$uid==$id){
    		
    		$this->error('操作失败！');
    	}
    	
    	 
    	$map['id']=$uid;
    	$map['rowid']=$id;
    	$map['type']=$type;//表示是关注用户
    	if(M('Focus')->where($map)->count()>0){
    		$res=D('focus')->where($map)->delete();
    		$isdel=1;
    	}else{
    		$map['create_time']=time();
    		$res=D('focus')->add($map);
    		
    		
    	}
    	 
    	 
    	
    	 
    	 
    	 
    	if($res===false){
    		$this->error('操作失败');
    	}else{
    		if($isdel==1){
    			$this->success('操作成功','',array('id'=>$id,'del'=>1));
    		}else{
    			$this->success('操作成功','',array('id'=>$id));
    		}
    		
    	}
    	 
    }
    public function delart(){
    	 
    	$uid=is_login();
    	if($uid==0){
    		 
    		$this->error('请登陆后操作！');
    	}
    	$id=I('get.id');
    	$info=M('Article')->where(array('id'=>$id))->find();
    	 
       if( is_admin($uid)||$info['uid']==$uid){
       	$res=M('Article')->where(array('id'=>$id))->delete();
       	if($res===false){
       		$this->error('操作失败');
       	}else{
       		setuserscore($info['uid'], C('ARTSCORE'),false);
       		$this->success('操作成功','Ucenter/userart');
       	}
       }else{
       	$this->error('无权操作！');
       }
    	 
    	
    	 
    	 
    	 
    	
    	 
    }
    public function cai(){
    $uid=is_login();
    	$id=I('get.id');
        if(cookie($uid.'ding'.$id)!=''||cookie($uid.'cai'.$id)!=''){
    		$this->error('你已经对该内容进行过操作！');
    	}
    	$res=M('Article')->where(array('id'=>$id))->setInc('cai',1);
    	if($res===false){
    		$this->error('操作失败');
    	}else{
    		cookie($uid.'cai'.$id,1);
    		$this->success('操作成功','',array('id'=>'cai'));
    	}
    	
    
    }
   
}