<?php
/*
 * 店铺管理类
 */	
class WxShop extends Action{	
	private $cacheDir='';//缓存目录
	private $type='';//缓存目录
	private $member='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
		$this->type=_instance('Action/admin/WxShopType');
		$this->member=_instance('Action/home/WxMember');
		
	}	

	public function shop_add(){
		if(empty($_POST)){
			$type_opt=$this->type->shop_type_get_opt('type_id');
			$smarty = $this->setSmarty();
			$smarty->assign(array('type_opt'=>$type_opt));
			$smarty->display('admin/shop_add.html');	
		}else{
			$rtn=$this->shop_add_save();
			
			if($rtn>0){
				 $rtn_msg=array('code'=>'sucess','message'=>'申请成功');
			}else{
				 $rtn_msg=array('code'=>'fail','message'=>'申请失败');
			}	
			echo json_encode($rtn_msg);
		}
	}	
	public function shop_add_save(){
		$member	 	=$this->L('home/WxMember')->member_get_info();
		$member_id	=$member['id'];
		$name	 	= $this->_REQUEST("name");
		$type_id	= $this->_REQUEST("type_id");
		$intro	 	= $this->_REQUEST("intro");
		$tel	 = $this->_REQUEST("tel");
		$mobile	 = $this->_REQUEST("mobile");
		$address = $this->_REQUEST("address");
		$zipcode = $this->_REQUEST("zipcode");
		$email  = $this->_REQUEST("email");
		$status	 =0;
		$dt	 = date("Y-m-d H:i:s",time());
		$sql = "insert into fly_shop(name,type_id,member_id,tel,mobile,zipcode,email,address,intro,status,adt) 
							values('$name','$type_id','$member_id','$tel','$mobile','$zipcode','$email','$address','$intro','$status','$dt');";
		$rtn=$this->C($this->cacheDir)->update($sql);
		if($rtn>0){
			return $rtn;
		}else{
			return 0;
		}
	}
	
	public function shop_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 				="select * from fly_shop where id='$id'";
			$one 				=$this->C($this->cacheDir)->findOne($sql);	
			$one['type_name']	=$this->type->shop_type_get_name($one['type_id']);
			$one['member_name'] =$this->member->member_get_name($one['member_id']);
			$shop_type	 =$this->L('home/WxShopType')->shop_type();
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,'shop_type'=>$shop_type));
			$smarty->display('home/shop_modify.html');	
		}else{//更新保存数据
			$id	 		= $this->_REQUEST("id");
			$name	 	= $this->_REQUEST("name");
			$type_id	= $this->_REQUEST("type_id");
			$intro	 	= $this->_REQUEST("intro");
			$tel	 = $this->_REQUEST("tel");
			$mobile	 = $this->_REQUEST("mobile");
			$address = $this->_REQUEST("address");
			$zipcode = $this->_REQUEST("zipcode");
			$email = $this->_REQUEST("email");
			$sql= "update fly_shop set 
							name='$name',
							status='0',
							type_id='$type_id',
							intro='$intro',
							tel='$tel',
							mobile='$mobile',
							address='$address',
							zipcode='$zipcode',
							email='$email'
			 		where id='$id'";
			$rtn=$this->C($this->cacheDir)->update($sql);	
			if($rtn>=0){
				 $rtn_msg=array('code'=>'sucess','message'=>'修改成功');
			}else{
				 $rtn_msg=array('code'=>'fail','message'=>'修改失败');
			}	
			echo json_encode($rtn_msg);	
		}
	}
	
	public function shop_status(){
		$rtn=array(
			"0"=>"待审核",
			"1"=>"已审核",
			"2"=>"未通过"
		);
		return $rtn;
	}
	
		
	public function shop_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from fly_shop where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/admin/WxShop/shop_show/");	
	}

	//传入ID返回名字
	public function shop_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,name from fly_shop where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
	
}//
?>