<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

//转换左菜单栏树形列表所需数据格式
class LeftPublic extends Action {
	public $table = '';				//模型名称 注意大小写
	public $access = '';			//用户组别权限值
	
	private $arr_pa;
	private $new_info;
	public function rowMenu($id=NULL,$uid){
		$Public = A('Index','Public');
		$obj = M($this->table);
		$result = M();
		$linkage = M('Linkage');
		$linkage_table = C('DB_PREFIX').'linkage';
		$Show = $obj->where("code like 'Show%'")->find();
		$url = '/index.php?s=/'.str_replace('Show','',$Show['code']).'/index';
		if($id==NULL){
			$sele = $obj->union('select CONCAT(\'100\',id) as id,IF(deep=1,'.$Show['id'].',CONCAT(\'100\',_parentId)) as _parentId,deep+1 as deep,\'Chart\' as code,text,CONCAT(\''.$url.'\',IF(deep=1,CONCAT(\'/topid/\',id),CONCAT(\'/topid/\',_parentId)),IF(deep=1,\'\',CONCAT(\'/sonid/\',id))) as url,\'\' as iconCls,1 as status,\'open\' as state,1 as mode,\''.$Show['type'].'\' as type,\''.$Show['level'].'\' as level,\''.$Show['view'].'\' as view,100 as sort from '.$linkage_table.' where code like \'%_menu%\' and _parentId<>0 order by deep,sort')->where('status=1')->select();
		}else{
			$ids = $this->rowId($id);
			$hashow = $obj->where("code like 'Show%' and id in(".$ids.")")->getField('id');
			if($hashow){
				$sele = $obj->union('select CONCAT(\'100\',id) as id,IF(deep=1,'.$hashow.',CONCAT(\'100\',_parentId)) as _parentId,deep+1 as deep,\'Chart\' as code,text,CONCAT(\''.$url.'\',IF(deep=1,CONCAT(\'/topid/\',id),CONCAT(\'/topid/\',_parentId)),IF(deep=1,\'\',CONCAT(\'/sonid/\',id))) as url,\'\' as iconCls,1 as status,\'open\' as state,1 as mode,\''.$Show['type'].'\' as type,\''.$Show['level'].'\' as level,\''.$Show['view'].'\' as view,100 as sort from '.$linkage_table.' where code like \'%_menu%\' and _parentId<>0 order by deep,sort')->where('status=1 and id in('.$ids.')')->select();
			}else{
				$sele = $obj->where('status=1 and id in('.$ids.')')->order('deep,sort')->select();
			}
			//dump($sele);
		}
		
		
		
		$this->arr_pa = array();
		$this->new_info = array();
		
		$gid = $_SESSION['login']['se_groupID'];	//当前登录用户组别ID
		$cid = $_SESSION['login']['se_comyID'];		//当前登录用户公司ID
		$pid = $_SESSION['login']['se_partID'];		//当前登录用户部门ID
		foreach($sele as $t){
			if($t['mode']==1){
				$access = $Public->GS('User_group_table',$gid);//获取组别权限组
			}elseif($t['mode']==2){
				$access = $Public->GS('User_company_table',$cid);//获取公司权限值
			}elseif($t['mode']==3){
				$access = $Public->GS('User_part_table',$pid);//获取部门权限值
			}
			$view = unserialize($t['view']);
			if($t['type']=='='){
				if(($this->access>=999 && $t['level']<9999) || strstr($t['level'],$access) || in_array($uid,$view)){
					if($id==NULL){
						if($t['_parentId']!=0){
							$this->arr_pa[$t['id']] = $t['_parentId'];
						}
					}else{
						if($t['_parentId']!=$id){
							$this->arr_pa[$t['id']] = $t['_parentId'];
						}
					}
					$this->new_info[$t['id']] = $t;
				}
			}else{
				if($access>=$t['level'] || in_array($uid,$view)){
					if($id==NULL){
						if($t['_parentId']!=0){
							$this->arr_pa[$t['id']] = $t['_parentId'];
						}
					}else{
						if($t['_parentId']!=$id){
							$this->arr_pa[$t['id']] = $t['_parentId'];
						}
					}
					$this->new_info[$t['id']] = $t;
				}
			}
		}
		while(true){
			if(count($this->arr_pa)>0){
				$this->getLoop($id);
			}else{
				break;
			}
		}
		
		$info = array_reverse($this->new_info);
		//dump($info);exit;
		$info = array_sort($info,'sort');
		return $info;
	}
	
	//子数据压入父数据中
	private function getLoop($id=NULL){
		$id==NULL?0:$id;
		foreach($this->new_info as $key=>$val){
			if($val['_parentId']!=$id){
				$idd = array_search($key,$this->arr_pa);
				if(!$idd){
					$this->new_info[$val['_parentId']]['children'][] = $val;
					unset($this->new_info[$key],$this->arr_pa[$key]);
				}
			}
		}
	}
	
	
	//循环获取下级ID
	private $arr_id;
	public function rowId($id,$mode='noself'){
		$obj = M($this->table);
		$sele = $obj->cache(true)->field('id')->where('_parentId='.$id)->select();
		$this->arr_id = array();
		$this->far_id = array();
		if($mode=='self'){
			$this->arr_id[] = $id;
		}
		foreach($sele as $t){
			$this->arr_id[] = $t['id'];
			$count = $obj->cache(true)->field('id')->where('_parentId='.$t['id'])->find();
			if($count){
				$this->loopId($t['id']);
			}
		}
		
		return implode(',',$this->arr_id);
	}
	
	private function loopId($id){
		$obj = M($this->table);
		$seles = $obj->field('id')->where('_parentId='.$id)->select();
		foreach($seles as $tt){
			$this->arr_id[] = $tt['id'];
			$count = $obj->field('id')->where('_parentId='.$tt['id'])->find();
			if($count){
				$this->getLoopId($tt['id']);
			}
		}
	}
	
	private function getLoopId($id){
		$this->loopId($id);
	}
}