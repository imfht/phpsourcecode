<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
 
namespace osc\mobile\controller;
use osc\common\controller\AdminBase;
use think\Db;
use osc\mobile\validate\TextReply;
use osc\mobile\validate\NewsReply;
class ReplyBackend extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','自动回复');	
	}
	
	function text(){
		$this->assign('list',Db::name('wechat_text_reply')->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		return $this->fetch();
	}
	
	function text_add(){
		
		if(request()->isPost()){			
			
			$data=input('post.');
			
			$validate=new TextReply();
				
			if(!$validate->check($data)){				
			    return ['error'=>$validate->getError()];				
			}
			
			$rule['keyword']=$data['keyword'];
			$rule['module']='text';
			$rule['status']=$data['status'];
			$rule['create_time']=date('Y-m-d H:i:s',time());
			
			$rid=Db::name('wechat_rule')->insert($rule,false,true);
			
			if($rid){
				
				$text=$data;
				$text['rid']=$rid;				
				$text['create_time']=date('Y-m-d H:i:s',time());				
				
				Db::name('wechat_text_reply')->insert($text,false,true);
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增文字回复');	
				
				return ['success'=>'新增成功','action'=>'add'];
			}else{
				return ['error'=>'新增失败'];
			}
		}
		
		$this->assign('action',url('ReplyBackend/text_add'));
		return $this->fetch('text_edit');
	}
	
	function text_edit(){
		
		if(request()->isPost()){			
			
			$data=input('post.');
			
			$validate=new TextReply();
				
			if(!$validate->scene('edit')->check($data)){				
			    return ['error'=>$validate->getError()];				
			}
			
			$text=Db::name('wechat_text_reply')->find($data['tr_id']);	
			
			$rule['rid']=$text['rid'];
			$rule['keyword']=$data['keyword'];
			$rule['create_time']=date('Y-m-d H:i:s',time());
			$rule['status']=$data['status'];
		
			
			$r=Db::name('wechat_rule')->update($rule,false,true);
			
			if($r){
				
				$text['keyword']=$data['keyword'];
				$text['content']=$data['content'];			
				$text['status']=$data['status'];
				$text['create_time']=date('Y-m-d H:i:s',time());
				
				Db::name('wechat_text_reply')->where(array('tr_id'=>$data['tr_id']))->update($text);
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'编辑文字回复');
				
				return ['success'=>'编辑成功','action'=>'edit'];
			}else{
				return ['error'=>'编辑失败'];
			}
		}

		$this->assign('reply',Db::name('wechat_text_reply')->find((int)input('param.id')));		
		$this->assign('action',url('ReplyBackend/text_edit'));
		return $this->fetch('text_edit');
	}
	
	function text_del(){
		
		$id=(int)input('param.id');
		Db::name('wechat_rule')->delete($id);
		Db::name('wechat_text_reply')->where(array('rid'=>$id))->delete();
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除文字回复');
		
		$this->redirect('ReplyBackend/text');
	}
	
	function news(){
		
		$this->assign('list',Db::name('wechat_news_reply')->paginate(config('page_num')));
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		return $this->fetch();
	}
	function news_add(){
		
		if(request()->isPost()){			
			
			$data=input('post.');
			
			$validate=new NewsReply();
				
			if(!$validate->check($data)){				
			    return ['error'=>$validate->getError()];				
			}
			
			$rule['keyword']=$data['keyword'];
			$rule['module']='news';
			$rule['status']=$data['status'];
			$rule['create_time']=date('Y-m-d H:i:s',time());
			
			$rid=Db::name('wechat_rule')->insert($rule,false,true);
			
			if($rid){
				
				$news['rid']=$rid;
				$news['keyword']=$data['keyword'];
				$news['title']=$data['title'];
				
				$news['description']=$data['description'];
				$news['content']=$data['content'];
				$news['thumb']=$data['thumb'];
				
				$news['create_time']=date('Y-m-d H:i:s',time());
				$news['status']=$data['status'];			
				
				Db::name('wechat_news_reply')->insert($news,false,true);
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增图文回复');
				
				return ['success'=>'新增成功','action'=>'add'];
			}else{
				return ['error'=>'新增失败'];
			}
		}
		
		$this->assign('action',url('ReplyBackend/news_add'));
		return $this->fetch('news_edit');
	}
	function news_edit(){
		
		if(request()->isPost()){			
			
			$data=input('post.');
			
			$validate=new NewsReply();
				
			if(!$validate->scene('edit')->check($data)){				
			    return ['error'=>$validate->getError()];				
			}
			
			$news=Db::name('wechat_news_reply')->find($data['nr_id']);			
			
			$rule['rid']=$news['rid'];
			$rule['keyword']=$data['keyword'];		
			$rule['status']=$data['status'];		
			$rule['create_time']=date('Y-m-d H:i:s',time());			
			
			$r=Db::name('wechat_rule')->update($rule,false,true);
			
			if($r){
				
				$news['keyword']=$data['keyword'];
				$news['title']=$data['title'];
				
				$news['description']=$data['description'];
				$news['content']=$data['content'];
				$news['thumb']=$data['thumb'];
				//$news['url']=$data['url'];						
				$news['create_time']=date('Y-m-d H:i:s',time());
				$news['status']=$data['status'];
				
				Db::name('wechat_news_reply')->where(array('nr_id'=>$data['nr_id']))->update($news);
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'编辑图文回复');	
								
				return ['success'=>'编辑成功','action'=>'edit'];
			}else{
				return ['error'=>'编辑失败'];
			}
		}
		
		$this->assign('reply',Db::name('wechat_news_reply')->find((int)input('param.id')));
		$this->assign('action',url('ReplyBackend/news_edit'));
		return $this->fetch('news_edit');
	}
	function news_del(){
		
		$id=(int)input('param.id');
		Db::name('wechat_rule')->delete($id);
		Db::name('wechat_news_reply')->where(array('rid'=>$id))->delete();
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除图文回复');
		
		$this->redirect('ReplyBackend/news');
	}
}
?>