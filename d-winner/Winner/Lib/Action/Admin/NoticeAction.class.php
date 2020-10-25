<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

class NoticeAction extends Action {
	/**
		* 主方法
		*@param $json    为NULL输出模板。为1时输出列表数据到前端，格式为Json
		*@param $method  为1时，单独输出记录数
		*@examlpe 
	*/
    public function index($json=NULL,$method=NULL){
		$Public = A('Index','Public');
		$Public->check('Notice',array('r'));
		
		//main
		if(!is_int((int)$json)){
			$json = NULL;
		}
		$view = C('DATAGRID_VIEW');
		$page_row = C('PAGE_ROW');
		if($json==1){
			$userid = $_SESSION['login']['se_id'];
			$userid = intval($userid);
			if(!$userid){
				echo '';exit;
			}
			$notice = D('Notice_table');
			
			/*
			$data = array(
				'user_id'=>1,
				'title'=>'测试数据',
				'content'=>'测试内容',
				'status'=>2,
				'addtime'=>'2014-12-09'
			);
			for($i=0; $i<2000000; $i++){
				$notice->add($data);
			}
			exit;
			*/
			
			$get_sort = $this->_get('sort');
			$get_order = $this->_get('order');
			$sort = isset($get_sort) ? strval($get_sort) : 't1_old_addtime';   
			$sort = str_replace('_new_','_old_',$sort); 
			$order = isset($get_order) ? strval($get_order) : 'desc';
			$result = M();
			$Notice_table = C('DB_PREFIX').'notice_table';
			$user_table = C('DB_PREFIX').'user_table';
			
			$map = array();
			if(cookie('Notice') || cookie('aNotice')){
				if(cookie('Notice')){
					$str_map = slashes(cookie('Notice'));
					$map = unserialize($str_map);
				}else{
					$str_map = slashes(cookie('aNotice'));
					$map = unserialize($str_map);
				}
				unset($str_map);
			}else{
				$map['id'] = 'id>0';
				cookie('All',1);
				cookie('Notice',serialize($map));
			}
			$map = implode(' ',$map);
			//dump(unserialize(slashes(cookie('aNotice'))));
			$all = cookie('All');
			
			$get_page = $this->_get('page');
			$get_rows = $this->_get('rows');
			$page = isset($get_page) ? intval($get_page) : 1;    
			$rows = isset($get_rows) ? intval($get_rows) : $page_row; 
			$now_page = $page-1;
			$offset = $now_page*$rows;
			
			$arr_flelds = array(
				'id' => 't1.id as id',
				'title' => 't1.title as t1_old_title',
				'user_id' => 't1.user_id as t1_old_user_id',
				'username' => 't2.username as t2_old_username',
				'title' => 't1.title as t1_old_title',
				'content' => 'if(t1.content<>\'\',t1.content,\'无\') as t1_old_content',
				'status' => 't1.status as t1_old_status',
				'status2' => 'case t1.status when \'1\' then \'顶置\' when \'2\' then \'上线\' else \'下线\' end as t1_new_status',
				'addtime' => 't1.addtime as t1_old_addtime',
			);
			$fields = implode(',',$arr_flelds);
			unset($arr_flelds);
			
			if(!$view){
				if($all){
					$info = $result->table($Notice_table.' as t1')->field($fields)->join(' '.$user_table.' as t2 on t2.id = t1.user_id')->having($map)->order($sort.' '.$order)->limit($offset,$rows)->select();
					$count = $result->query('select count(*) as total from '.$Notice_table);
				}else{
					$info = $result->table($Notice_table.' as t1')->field('SQL_CALC_FOUND_ROWS '.$fields)->join(' '.$user_table.' as t2 on t2.id = t1.user_id')->having($map)->order($sort.' '.$order)->limit($offset,$rows)->select();
					$count = $result->query('SELECT FOUND_ROWS() as total');
				}
				$count = $count[0]['total'];
			}else{
				$info = $result->table($Notice_table.' as t1')->field($fields)->join(' '.$user_table.' as t2 on t2.id = t1.user_id')->having($map)->order($sort.' '.$order)->select();
				$count = count($info);
			}
			//dump($info);exit;
			$new_info = array();
			$items = array();
			$new_info['total'] = $count;
			if($method=='total'){
				echo  json_encode($new_info); exit;
			}
			
			$new_info['rows'] = $info?$info:array();
			//dump($new_info);
			echo json_encode($new_info);
			unset($new_info,$info,$order,$sort,$count,$items);
		}else{
			$this->assign('page_row',$page_row);
			$this->display();
			unset($Public);
		}
    }
	
	/**
		* 公告详情
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function detail($id=NULL){	
		$Public = A('Index','Public');
		$Public->check('Index',array('r'));
		
		//main
		$notice = D('Notice_table');
		$userid = $_SESSION['login']['se_id'];
		$userid = intval($userid);
		$arr_st = array('下线','顶置','上线');
		$map['id'] = array('eq',$id);
		$info = $notice->relation(true)->where($map)->find();
		$info['status'] = $arr_st[$info['status']];
		$this->assign('role',$role);
		$this->assign('id',$id);
		$this->assign('info',$info);
		$this->display();
		unset($info,$map);
	}
	
	/**
		* 删除数据
		*@examlpe 
	*/
	public function del(){
		$Public = A('Index','Public');
		$role = $Public->check('Notice',array('d'));
		if($role<0){
			echo $role; exit;
		}
		
		//main
		$str_id = I('id');
		$str_id = strval($str_id);
		$str_id = substr($str_id,0,-1);
		$arr_id = explode(',',$str_id);
		$notice = M('Notice_table');
		$pass = 0;$fail = 0;
		//dump($arr_id);
		foreach($arr_id as $id){
			$map['id'] = array('eq',$id);
			$del = $notice->where($map)->delete();
			if($del){
				$pass++;
			}else{
				$fail++;
			}
		}
		unset($map,$str_id,$arr_id);
		if($pass==0){
			echo 0;
		}else{
			echo 1;
		}
		$pass = 0; $fail = 0;
		unset($notice,$Public);
	}
	
	/**
		* 高级搜索
		*@param $act   为1时，获取post
		*@examlpe 
	*/
	public function advsearch($act=NULL){
		$App = A('App','Public');
			
		//main
		$field = strval($field);
		if($act==1){
			$field = I('field');
			$mod = I('mod');
			$keyword = I('keys');	
			$type = I('type');
			array_pop($field); array_pop($mod); array_pop($keyword); array_pop($type);
			
			$del = array_pop($type);
			
			$arr = array();
			$num = 0;
			$map['id'] ='id>0';
			foreach($field as $key=>$val){
				if($mod[$key]=='like' || $mod[$key]=='notlike'){
					$keyword[$key] = '%'.$keyword[$key].'%';
				}
				$tt = trim($type[$key]);
				$n = $key+1;
				$l = $key-1;
				$nt = trim($type[$n]);
				$lt = trim($type[$l]);
				$lf = $field[$l];
				$step = 1;
				
				if($val==$lf){
					$str = $val.$step;
					$step++;
				}else{
					$str = $val;
				}
				
				if($tt=='OR'){
					if($keyword[$key]){
						$mod[$key] = htmlspecialchars_decode($mod[$key]);
						$arr[$num]['k'][] = $val;
						$arr[$num]['v'][] = $val." ".$mod[$key]." '".$keyword[$key]."'";
					}
					if($nt=='AND'){
						$mod[$n] = htmlspecialchars_decode($mod[$n]);
						if($mod[$n]=='like' || $mod[$n]=='notlike'){
							$keyword[$n] = '%'.$keyword[$n].'%';
						}
						if($keyword[$n]){
							$arr[$num]['k'][] = $val;
							$arr[$num]['v'][] = $val." ".$mod[$n]." '".$keyword[$n]."'";
						}
						$num++;
					}
				}else{
					if($lt!='OR' && $tt=='AND'){
						$mod[$key] = htmlspecialchars_decode($mod[$key]);
						if($keyword[$key]){
							$map[$str] = ' and '.$val." ".$mod[$key]." '".$keyword[$key]."'";
						}
					}
				}
				
				if(!isset($type[$key]) && $lt=='OR'){
					$mod[$key] = htmlspecialchars_decode($mod[$key]);
					if($keyword[$key]){
						$arr[$num]['k'][] = $val;
						$arr[$num]['v'][] = $val." ".$mod[$key]." '".$keyword[$key]."'";
					}
				}else{
					if(!isset($type[$key]) && $lt!='OR'){
						$mod[$key] = htmlspecialchars_decode($mod[$key]);
						if($keyword[$key]){
							$map[$str] = ' and '.$val." ".$mod[$key]." '".$keyword[$key]."'";
						}
					}
				}
			}
			$num = 0;
			unset($key,$val,$ntable,$table,$field,$mod,$type,$keyword);
			
			foreach($arr as $key=>$val){
				$str = $val['k'][0];
				for($i=0;$i<count($val['v']);$i++){
					if($i==0){
						$map[$str] .= ' and ('.$val['v'][$i];
					}elseif($i==count($val['v'])-1){
						$map[$str] .= ' or '.$val['v'][$i].')';
					}else{
						$map[$str] .= ' or '.$val['v'][$i];
					}
				}	
			}
			unset($arr);
			
			cookie('All',0);
			cookie('Notice',NULL);
			cookie('aNotice',serialize($map));
			echo 1;
			unset($map);
		}else{
			$this->assign('uniqid',uniqid());
			$this->assign('field',$field);
			$this->display();
		}	
	}
	
	/**
		* 清空所以搜索产生的cookies
		*@examlpe 
	*/
	public function clear(){
		cookie('All',NULL);
    	cookie('Notice',NULL);
		cookie('aNotice',NULL);
	}
	
	/**
		* 工具栏搜索控制
		*@param $act  传入的字段名
		*@param $mode  为like时，模糊搜索
		*@examlpe 
	*/
	public function change($act,$mode=NULL){
		if(cookie('Notice')){
			$str_map = slashes(cookie('Notice'));
			$map = unserialize($str_map);
		}
		unset($str_map);
		$id = strval(I('val'));
		switch($act){
			case 'user_id':
				$map['user_id'] = " and t1_old_user_id='".$id."'";
				if(!$id){
					unset($map['user_id']);
				}
			break;
		}
		cookie('All',0);
		cookie('Notice',serialize($map));
	}
	
	/**
		* 新增与更新数据
		*@param $act add为新增、edit为编辑
		*@param $go  为1时，获取post
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function add($act=NULL,$go=false,$id=NULL){	
		$Public = A('Index','Public');
		
		//main
		$notice = D('Notice_table');
		$userid = $_SESSION['login']['se_id'];
		$userid = intval($userid);
		
		if($go==false){
			$this->assign('uniqid',uniqid());
			if($act=='add'){
				$role = $Public->check('Notice',array('c'));
				$this->assign('role',$role);
				$this->assign('act','add');
				$this->display();
			}else{
				$role = $Public->check('Notice',array('u'));
				if(!is_int((int)$id)){
					$id = NULL;
					$this->show('无法获取ID');
				}else{
					$map['id'] = array('eq',$id);
					$info = $notice->relation(true)->where($map)->find();
					//dump($info);
					$this->assign('role',$role);
					$this->assign('id',$id);
					$this->assign('act','edit');
					$this->assign('info',$info);
					$this->display();
					unset($info,$map);
				}
			}	
		}else{
			$data = $notice->create();
			$data['addtime'] = date("Y-m-d H:i:s");
			//dump($data);exit;
			if($act=='add'){
				$role = $Public->check('Notice',array('c'));
				if($role<0){
					echo $role; exit;
				}
				
				$data['user_id'] = $userid;
				//dump($data);exit;
				$add = $notice->add($data);
				if($add>0){
					echo 1;
				}else{
					echo 0;
				}
				unset($data);
			}elseif($act=='edit'){
				$role = $Public->check('Notice',array('u'));
				if($role<0){
					echo $role; exit;
				}
				
				if(!is_int((int)$id)){
					echo 0;
				}else{
					unset($data['id'],$data['addtime']);
					$map['id'] = array('eq',$id);
					$edit = $notice->where($map)->save($data);
					unset($map);
					if($edit !== false){
						echo 1;
					}else{
						echo 0;
					}
					unset($data);
				}
			}
		}
		unset($notice,$Public);
	}
}