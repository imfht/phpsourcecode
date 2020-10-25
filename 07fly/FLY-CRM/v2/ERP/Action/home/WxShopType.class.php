<?php	 
/*
 * 店铺分类管理
 */	 
class WxShopType extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/home/WxAuth');
	}
	public function shop_type(){
		$sql = "select * from fly_shop_type  order by sort asc ";	
		$list= $this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	public function shop_type_show(){
		$assArr    			= $this->shop_type();
		$smarty   			= $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('home/shop_type_show.html');	
	}
	public function shop_type_get_one($id){
		$sql = "select * from fly_shop_type where id='$id'";	
		$pak = $this->C($this->cacheDir)->findOne($sql);	
		return $pak;
	}
	
	//分类下拉选择
	public function shop_type_get_opt($inputname,$value=null){
		$sql = "select * from fly_shop_type;";
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
	public function shop_type_lookup(){
		$sql = "select id,typename from fly_shop_type;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}
	//传入ID返回名字
	public function shop_type_get_name($id){
		if(empty($id)) $id=0;
		$sql ="select * from fly_shop_type where id in ($id)";
		$list=$this->C($this->cacheDir)->findAll($sql);
		$str ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["typename"]."&nbsp;";
			}
		}
		return $str;
	}
}//
?>