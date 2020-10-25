<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

//项目日志操作
class LogPublic extends Action {
	//插入操作日誌
	/*
	return 		Void
	$data		必須值，傳入數組，格式： array('pro_id'=>項目ID，'task_id'=>子項目ID，worklog_id=>工作日誌ID，files_id=>文檔ID，'usage=>'耗時，，'notes=>'描述，status=>最新狀態，'descrtption'=>工作描述);
	$type		必須值，傳入對應模塊 project=>1，task=>2，worklog=>3，files=>4
	$mode		默認為創建，傳入字符串，add：創建=>1，deit：編輯=>2，del：刪除=>3，check：审核=>4
	*/
	public function actLog($data,$type,$mode=1){
		$log = D('Log_table');
		$datas = array(
			'user_id'=>$_SESSION['login']['se_id'],
			'type'=>$type,
			'mode'=>$mode,	
			'pro_id'=>$data['pro_id'],
			'notes'=>$data['notes'],
			'addtime'=>date("Y-m-d H:i:s",time()),
			'logmain'=>array(
				'pro_id'=>$data['pro_id'],
			),
		);
		switch($type){
			case 1:
				$datas['usage'] = $data['usage'];
				$datas['status'] = $data['status'];
			break;
			
			case 2:
				$datas['usage'] = $data['usage'];
				$datas['status'] = $data['status'];
				$datas['logmain']['task_id'] = $data['task_id'];
			break;
			
			case 3:
				$datas['usage'] = $data['usage'];
				$datas['status'] = $data['status'];
				$datas['workdate'] = $data['workdate'];
				if(isset($data['description'])){
					$datas['description'] = $data['description'];
				}
				$datas['logmain']['task_id'] = $data['task_id'];
				$datas['logmain']['worklog_id'] = $data['worklog_id'];
			break;
			
			case 4:
				$datas['logmain']['files_id'] = $data['files_id'];
			break;
		}
		
		$add = $log->relation(true)->add($datas);
	}
	
	//过期项目移到历史日志表
	/*
	$data		傳入數組，
	return 		Void
	*/
	
	public function moveLog($id=0,$type=0){
		$expire = C('EXPIRE_TIME');
		$log = M('Log_table');
		$logd = M('Log_destroy_table');
		$log_main = M('Log_main_table');
		$log_dmain = M('Log_dmain_table');
		$result = M();
		$log_table = C('DB_PREFIX').'log_table';
		$log_main_table = C('DB_PREFIX').'log_main_table';
		$destroy_table = C('DB_PREFIX').'log_destroy_table';
		$destroy_main_table = C('DB_PREFIX').'log_dmain_table';
		$Project = M('Project_table');
		
		if($id>0 || $type>0){
			$arr_type = array(
				1=>'pro_id',2=>'task_id',3=>'worklog_id',4=>'files_id',
			);
			$where[$arr_type[$type]] = array('eq',$id);
			$sql = $log_main->field('log_id as id')->where($where)->select(false);
			$map['id'] = array('exp','IN('.$sql.')');
			$log->where($map)->delete();
			$log_main->where($where)->delete();
			
			$sql = $log_dmain->field('log_id as id')->where($where)->select(false);
			$map['id'] = array('exp','IN('.$sql.')');
			$logd->where($map)->delete();
			$log_dmain->where($where)->delete();
		}else{
			$info = $Project->field('GROUP_CONCAT(id) as id')->where('`ststus`=65 and TO_DAYS(NOW()) - TO_DAYS(`uptime`)>'.$expire)->find();
			$r1 = $result->execute('insert into '.$destroy_table.' select * from '.$log_table.' where pro_id in('.$info['id'].')');
			$r2 = $result->execute('insert into '.$destroy_main_table.' select * from '.$log_main_table.' where pro_id in('.$info['id'].')');
			if($r1){
				$del = $log->where('pro_id in('.$info['id'].')')->delete();
				$del2 = $log_main->where('pro_id in('.$info['id'].')')->delete();
			}
		}
	}	
	
	//获取操作日誌
	/*
	return 		Void
	$pid		传入项目ID	
	$id 		传入的ID
	$type		必須值，傳入對應模塊 project=>1，task=>2，worklog=>3，files=>4
	*/
	public function getLog($pid,$id,$type){
		$logPg = A('Page','Public');
		
		//main
		$project = M('Project_table');
		$pinfo = $project->field('status,uptime')->where('pro_id='.$pid)->find();
		$endtime = strtotime($pinfo['uptime']);
		$expire = C('EXPIRE_TIME');
		$realdate = date("Y-m-d", strtotime("+$expire day",$endtime));
		$nowdate = date("Y-m-d");
		
		if($nowdate>$realdate && $pinfo['ststus']==65){
			$log = D('Log_destroy_table');
			$log_main = M('Log_dmain_table');
		}else{
			$log = D('Log_table');
			$log_main = M('Log_main_table');
		}
		
		
		if($type==1){
			$map['pro_id'] = array('eq',$id);
		}elseif($type==2){
			$map['task_id'] = array('eq',$id);
		}elseif($type==3){
			$map['worklog_id'] = array('eq',$id);
		}elseif($type==4){
			$map['files_id'] = array('eq',$id);
		}
		
		$sql = $log_main->field('log_id as id')->where($map)->select(false);
		$count = $log->where('`id` in ('.$sql.')')->count();
		$logpage = $logPg->show($count,5);
		$info = $log->relation('user')->where('id in('.$sql.')')->order('addtime desc')->limit($logPg->offset,$logPg->rows)->select();
		$this->assign('logcount',$count);
		
		$str = '<table class="infobox table-border" width="100%" border="0" cellspacing="0" cellpadding="0">';
		$arr_mode = array(
			1=>'创建',2=>'编辑',3=>'刪除',4=>'审核',
		);
		$arr_type = array(
			1=>'项目',2=>'任务',3=>'工作日志',4=>'文档',
		);
		foreach($info as $k=>$t){
			if($k%2==0){
				$cls = 'class="rebg5"';
			 }else{
				$cls = '';
			 }
			switch($type){
				case 1:
					if($t['type']==3){
						$str .= '<tr><td height="36" '.$cls.'><div class="tpm">'.$t['username'].' 于 '.$t['addtime'].' '.$arr_mode[$t['mode']].'了 '.$t['notes'].'</div></td></tr>';
					}else{
						$str .= '<tr><td height="36" '.$cls.'><div class="tpm">'.$t['username'].' 于 '.$t['addtime'].' '.$arr_mode[$t['mode']].'了该'.$arr_type[$t['type']].'，'.$t['notes'].'</div></td></tr>';
					}
					break;
				case 2:
					if($t['type']==2){
						$str .= '<tr><td height="36" '.$cls.'><div class="tpm">'.$t['username'].' 于 '.$t['addtime'].' '.$arr_mode[$t['mode']].'了该'.$arr_type[$t['type']].'，'.$t['notes'].'</div></td></tr>';
					}elseif($t['type']==3){
						if($t['mode']==1 || $t['mode']==2){
							$str .= '<tr><td height="36" '.$cls.'><div class="tpt">'.$t['username'].' 于 '.$t['addtime'].' '.$arr_mode[$t['mode']].'了 '.$t['notes'].'</div><div class="tpc">'.$t['description'].'</div></td></tr>';
						}else{
							$str .= '<tr><td height="36" '.$cls.'><div class="tpm">'.$t['username'].' 于 '.$t['addtime'].' '.$arr_mode[$t['mode']].'了 '.$t['notes'].' 的日志</div></td></tr>';
						}
					}
					break;
				case 3:
					$str .= '<tr><td height="36" '.$cls.'><div class="tpm">'.$t['username'].' 于 '.$t['addtime'].' '.$arr_mode[$t['mode']].'了该'.$arr_type[$t['type']].'，'.$t['notes'].'</div></td></tr>';
					break;
			}	
		}
		
		return $str.'</table><div class="pages">'.$logpage.'</div>';
	}
}