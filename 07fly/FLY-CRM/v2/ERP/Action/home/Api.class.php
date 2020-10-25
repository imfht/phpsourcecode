<?php	 
class Api extends Action{	
	private $cacheDir='c_entry';//缓存目录
	public function __construct() {
	}	
	
	//分类下拉选择
	public function project_type_get_opt($inputname,$value=null){
		$sql = "select * from fly_project_type;";
		$list= $this->C($this->cacheDir)->findAll($sql);
		$string		="<select name='$inputname' id='$inputname'  class='combox'>";
		foreach($list as $key=>$row){
			$string.="<option value='$row[id]'";
			if($row["id"]==$value) $string.=" selected";
			$string.=">".$row["typename"]."</option>";
		}
		$string.="</select>";
		return $string;
	}
	
	//传入ID返回名字
	public function project_type_get_name($id){
		if(empty($id)) $id=0;
		$sql ="select typename from fly_project_type where id in ($id)";	
		$list=$this->C($this->cacheDir)->findAll($sql);
		$str ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["typename"]."&nbsp;";
			}
		}
		return $str;
	}
	
	//分类下拉选择
	public function school_get_opt($inputname,$value=null){
		$sql = "select * from fly_school;";
		$list= $this->C($this->cacheDir)->findAll($sql);
		$string		="<select name='$inputname' id='$inputname'  class='combox'>";
		foreach($list as $key=>$row){
			$string.="<option value='$row[id]'";
			if($row["id"]==$value) $string.=" selected";
			$string.=">".$row["name"]."</option>";
		}
		$string.="</select>";
		return $string;
	}
	//传入ID返回名字
	public function school_get_name($id){
		if(empty($id)) $id=0;
		$sql ="select name from fly_school where id in ($id)";	
		$list=$this->C($this->cacheDir)->findAll($sql);
		$str ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
}//
?>