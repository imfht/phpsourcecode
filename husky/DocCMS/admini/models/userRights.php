<?php
class userRights extends user{
	private $level_arr;
	function __construct(){
		$this->level_arr =array(
			7=>array(
					'7'=>'栏目管理员'
					),
			8=>array(
					'7'=>'栏目管理员',
					'8'=>'频道管理员'
					),
			9=>array(
					'7'=>'栏目管理员',
					'8'=>'频道管理员',
					'9'=>'超级管理员'
					),
			10=>array(
					'7'=>'栏目管理员',
					'8'=>'频道管理员',
					'9'=>'超级管理员',
					'10'=>'创始人'
					)
		); 
	}
	static  function return_level_nickname($role){
		$tmp_arr=array(
					'7'=>'栏目管理员',
					'8'=>'频道管理员',
					'9'=>'超级管理员',
					'10'=>'创始人'
					);
		return $tmp_arr[$role];
	} 
	public function user_power_list_select_create($name,$select=null,$oper){
		$sel=intval($oper)-1;
		$temp_arr=$this->level_arr[$sel]?$this->level_arr[$sel]:$this->level_arr[7];	
		$this->select($temp_arr,$name,intval($select));
	}
	public function user_power_list_select_edit($name,$select=null,$selectId,$oper,$operId){//edit user
		if($selectId==$operId){//修改自身
			$sel=intval($oper);
		}else{
			if($oper>$select){
				$sel=intval($oper)-1;
			}else{
				$sel=intval($select);
			}
		}
		$temp_arr=$this->level_arr[$sel]?$this->level_arr[$sel]:$this->level_arr[7];
		$this->select($temp_arr,$name,intval($select));
	}
	public function return_level_name($role){
		return $this->level_arr[9][$role];
	}
	
	static function select($str_arr,$name,$select=null,$ev=null)
	{
		if($ev)
		$str='<select id="'.$name.'" name="'.$name.'" '.$ev.'>';
		else
		$str='<select name="'.$name.'">';
		foreach ($str_arr as $k=>$v){
			$selected=($select==$k)?'selected ':' ';
	    	$str.='<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
		}
		$str.= '</select>';
		echo $str;
	}
}
?>