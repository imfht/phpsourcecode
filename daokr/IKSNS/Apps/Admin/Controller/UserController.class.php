<?php
/*
* @copyright (c) 2012-3000 IKPHP All Rights Reserved
* @author 小麦 改版时间 2014年3月15日 22:47
* @Email:810578553@qq.com
* @IKPHP注册用户列表
*/
namespace Admin\Controller;
use Common\Controller\BackendController;

class UserController extends BackendController {

	public function _initialize() {
		parent::_initialize ();
		$this->mod = D ( 'Common/User' );
		$this->role_mod = M ( 'user_role' );
		$this->score_mod = M ( 'user_score' );
	}
	//会员管理
	public function manage(){
		$ik = $this->_get ( 'ik', 'trim','users');

		$this->assign('ik', $ik);
		$this->title ( '会员管理' );
		switch ($ik) {
			case "users" :
				$this->users();
				break;
		}
	}
	//会员列表
	public function users(){
		//是否启用 0 启用 1 禁用
		$isenable = $this->_get('isenable','intval','0');
		//查询开放
		//$map = array('status'=>$isenable);
		$map = '';
		//显示列表
		$pagesize = 20;
		$count = $this->mod->where($map)->order('reg_time DESC')->count();
		$pager = $this->_pager($count, $pagesize);
		$query = $this->mod->field('userid')->where($map)->order('reg_time DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
		
		foreach($query as $key=>$item){
			$list[] = $this->mod->getOneUser($item['userid']);
		}
		// 已经禁用的用户数目
		$count_isenable = $this->mod->where(array('status'=>'1'))->count();
		 
		$this->assign ( 'isenable', $isenable );
		$this->assign ( 'count_isenable', $count_isenable );
		$this->assign('pageUrl', $pager->show());
		$this->assign('list', $list);
		$this->display('users');
	}
	//审核
	public function isenable(){
		$ik = $this->_get ( 'ik', 'trim');
		$id = $this->_get ( 'id', 'intval');
		$isenable = $this->_get('isenable','intval','0');  
		switch ($ik) {
			case "user" :
				$this->mod->where(array('userid'=>$id))->setField(array('status'=>$isenable));
				$isenable = $isenable == 0? 1 : 0;
				$this->redirect ( 'user/manage',array('ik'=>'users','isenable'=>$isenable));
				break;
		}
		 
	}
	//ajax批量审核
	public function ajax_isenable(){
		$itemid = $this->_post('itemid');
		$ik = $this->_get ( 'ik', 'trim');
		$isenable = $this->_get('isenable','intval','0');
		if(!empty($itemid)){
			 
			switch ($ik) {
				case "users" :
					//审核
					$where['userid'] = array('exp',' IN ('.$itemid.') ');
					$this->mod->where($where)->setField(array('status'=>$isenable));
					$arrJson = array('r'=>0, 'html'=> '操作成功');
					echo json_encode($arrJson);
					break;
			}
	
		}
	}
	//会员积分管理
	public function score(){
		$ik = $this->_get ( 'ik', 'trim','role'); //默认角色管理
		$menu = array(
				'role' => array('text'=>'角色管理', 'url'=>U('user/score',array('ik'=>'role'))),
				'setscore' => array('text'=>'积分设置', 'url'=>U('user/score',array('ik'=>'setscore'))),
		);
		$this->assign('ik', $ik);
		$this->assign('menu', $menu);
		$this->title ( '角色管理' );
		switch ($ik) {
			case "role" :
				$this->role();
				break;
			case "setscore" :
				$this->setscore();
				break;				
		}
	}
	//角色管理
	public function role(){
		if(IS_POST){
			$arrRoleName 	= $this->_post('rolename');
			$arrScoreStart  = $this->_post('score_start');
			$arrScoreEnd 	= $this->_post('score_end');
			
			$this->role_mod->query('TRUNCATE TABLE '.C('DB_PREFIX').'user_role'); //先清空表
			//后添加
			foreach($arrRoleName as $key=>$item){
				$rolename = trim($item);
				$score_start = trim($arrScoreStart[$key]);
				$score_end = trim($arrScoreEnd[$key]);
					
				if($rolename){
					$data['rolename'] = $rolename;
					$data['score_start'] = $score_start;
					$data['score_end'] = $score_end;
					$this->role_mod->add($data);
				}
			}
			//快速生成静态缓存
			$arrRole = $this->role_mod->field('rolename,score_start,score_end')->select();
			foreach ($arrRole as $key=>$val) {
				$setting['ik_'.$key] = $val;
			}
			F('user_role',$setting);
			$this->success('更新成功！');
			
		}else{
			$list = $this->role_mod->select();
			$this->assign('list',$list);
			$this->title ( '角色管理' );
			$this->display('role');
		}
	}	
	//积分设置
	public function setscore(){
		if(IS_POST){
			$arrName 	= $this->_post('actionname');
			$arrscore 	= $this->_post('score');
			$arraction 	= $this->_post('action');
			$arrmaxnum  = $this->_post('maxnum');
			$this->score_mod->query('TRUNCATE TABLE '.C('DB_PREFIX').'user_score'); //先清空表
			
			//后添加
			foreach($arrName as $key=>$item){
				$actionname = trim($item);
				$score = trim($arrscore[$key]);
				$action = trim($arraction[$key]);
				$maxnum = trim($arrmaxnum[$key]);
					
				if($actionname){
					$data['actionname'] = $actionname;
					$data['score'] = $score;
					$data['action'] = $action;
					$data['maxnum'] = $maxnum;
					$this->score_mod->add($data);
				}
			}
						
			//快速生成静态缓存
			$arrScores = $this->score_mod->field('actionname,action,score,maxnum')->select();
			foreach ($arrScores as $key=>$val) {
				$setting['ik_'.$val['action']] = $val;
			}
			F('score_rule',$setting);
			
			$this->success('配置成功！');
		}else{
			$arrScore = $this->score_mod->order('scoreid asc')->select(); //读取配置
			$this->assign('arrScore',$arrScore);
			$this->title ( '积分设置' );
			$this->display('setscore');
		}
	}
			
}