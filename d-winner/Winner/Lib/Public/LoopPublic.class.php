<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

//转换树形列表所需数据格式
class LoopPublic extends Action {
	public $table = '';						//模型名称 注意大小写
	public $field = 'id,_parentId,text';	//查询字段 id,text必须存在，当$mode等于son时_parentId必须存在，遵循mysql规则
	public $mode = 'son';					//是否循环下级数据
	public $isparnet = true;				//是否有_parentId,当为false时，只在$mode为'noson'且参数$id为NULL时有效
	public $hasson = true;					//是否显示无下级数据，false为不显示
	public $where = '`status`=1';			//查询条件，遵循mysql规则
	public $order = 'id';					//排序，遵循mysql规则
	public $text = array();					//自定义text的附加值
	
	//Json数据循环
	private $arr_pa;
	private $new_info;
	public function rowLevel($id=NULL){
		$obj = M($this->table);
		
		if($id==NULL){
			if($this->mode=='noson'){
				if($this->isparnet){
					$sele = $obj->field($this->field)->where('_parentId=0 and '.$this->where)->order($this->order)->select();
				}else{
					$sele = $obj->field($this->field)->where($this->where)->order($this->order)->select();
				}
			}else{
				$sele = $obj->field($this->field)->where($this->where)->order($this->order)->select();
			}
		}else{
			if($this->mode=='noson'){
				$ids = $id;
				$sele = $obj->field($this->field)->where('_parentId='.$ids.' and '.$this->where)->order($this->order)->select();
			}else{
				$ids = $this->rowId($id);
				$sele = $obj->field($this->field)->where('id in ('.$ids.') and '.$this->where)->order($this->order)->select();
			}
		}	
		$this->arr_pa = array();
		$this->new_info = array();
		//dump($sele);
		foreach($sele as $t){
			//创建子数据对应的父ID
			if(isset($t['deep']) && isset($t['layer'])){
				if($t['deep']<$t['layer']){
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
		if($this->mode=='son'){
			while(true){
				if(count($this->arr_pa)>0){
					$this->getLoop();
				}else{
					break;
				}
			}
		}else{
			$this->getLoop();
		}
		
		$info = array_reverse($this->new_info);
		if(strstr($this->field,'sort')){
			$info = array_sort($info,'sort');
		}elseif(!$this->order){
			sort($info);
		}
		
		return $info;
	}
	
	
	//子数据压入父数据中
	private function getLoop(){
		foreach($this->new_info as $key=>$val){
			if($val['_parentId']!=0){
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
	public function rowId($id,$mode='noself',$mode='str'){
		$obj = M($this->table);
		$sele = $obj->field('id')->where('_parentId='.$id)->select();
		$this->arr_id = array();
		$this->far_id = array();
		if($mode!='noself'){
			$this->arr_id[] = $id;
		}
		foreach($sele as $t){
			$this->arr_id[] = $t['id'];
			$count = $obj->field('id')->where('_parentId='.$t['id'])->find();
			if($count){
				$this->loopId($t['id']);
			}
		}
		if($mode=='str'){
			return implode(',',$this->arr_id);
		}else{
			return $this->arr_id;
		}
		
	}
	
	private function loopId($id){
		$obj = M($this->table);
		$seles = $obj->field('id')->where('_parentId='.$id)->select();
		foreach($seles as $tt){
			$this->arr_id[] = $tt['id'];
			$count = $obj->field('id')->where('_parentId='.$tt['id'])->find();
			if($count){
				$this->loopId($tt['id']);
			}
		}
	}
	
	private function getLoopId($id){
		return $this->loopId($id);
	}
	
	//获取对应顶级层设置layer
	public function getLayer($parentId,$layer=NULL){
		$obj = M($this->table);
		if($parentId==0){
			if($layer){
				return $layer;
			}else{
				return $obj->where('id='.$parentId)->getField('layer');
			}
		}else{
			return $this->loopLayer($parentId);
		}
	}
	
	private function loopLayer($id){
		$obj = M($this->table);
		$info = $obj->field('_parentId,layer')->where('id='.$id)->find();
		return $this->getLayer($info['_parentId'],$info['layer']);
	}
}