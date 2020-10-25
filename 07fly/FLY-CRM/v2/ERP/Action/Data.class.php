<?php
class Data extends Action {
	
	//订单状态
	public function data_arr($type)
	{
		switch($type){
			case "overs":
				$rtn=array("0"=>"连载","1"=>"完结");
				break;
		}
		return $rtn;
	}
	public function data_arr_opt($type,$inputname,$value=null){
		$package	=$this->data_arr($type);
		$string		="<select name='$inputname' id='$inputname'  class='combox'>";
		foreach($package as $key=>$v){
			$string.="<option value='$key'";
			if($key==$value) $string.=" selected";
			$string.=">".$v."</option>";
		}
		$string.="</select>";
		return $string;
	}	

} //
?>