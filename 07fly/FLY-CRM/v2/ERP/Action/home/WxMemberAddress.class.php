<?php
class WxMemberAddress extends Action{
	private $cacheDir='';//缓存目录
	private $pcBalanceIntegral ='';
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
	}	
	
	public function member_address(){
		$member	  =$this->L('home/WxMember')->member_get_info();
		$where_str =" member_id='".$member['member_id']."' ";
		$sql = "select * from fly_member_address where $where_str order by id desc ";	
		$list= $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,'member_name'=>$member['account']);	
		return $assignArray;
	}
	
	public function member_address_show(){
			$assArr  = $this->member_address();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('home/member_address_show.html');	
	}
	//增加地址
	public function member_address_add(){
		$member	 	=$this->L('home/WxMember')->member_get_info();
		$retrun_shop=$this->_REQUEST("retrun_shop");
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$member,'retrun_shop'=>$retrun_shop));
			$smarty->display('home/member_address_add.html');	
		}else{
			$member_id= $member['member_id'];
			$name	= $this->_REQUEST("name");
			$mobile	= $this->_REQUEST("mobile");
			$address= $this->_REQUEST("address");
			
			$sql="insert into fly_member_address(member_id,name,mobile,address) 
										values('$member_id','$name','$mobile','$address');";
			
			$rtn=$this->C($this->cacheDir)->update($sql);
			
			if($rtn>0){
				$rtn_msg=array('code'=>'sucess','message'=>'操作成功'); 
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'操作失败'); 
			}
			
			echo json_encode($rtn_msg);
		}
	}
	
	public function member_address_edit(){
		$id	= $this->_REQUEST("id");
		if(empty($_POST)){
			$sql  = "select * from fly_member_address where id='$id'";
			$one  = $this->C($this->cacheDir)->findOne($sql);
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('home/member_address_edit.html');				
		}else{
			$member	 =$this->L('home/WxMember')->member_get_info();
			$name	= $this->_REQUEST("name");
			$mobile	= $this->_REQUEST("mobile");
			$address= $this->_REQUEST("address");
			$sql="update fly_member_address set name='$name', mobile='$mobile', address='$address' where id='$id'";
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>=0){
				$rtn_msg=array('code'=>'sucess','message'=>'操作成功');	
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'操作有误');	
			}

			echo json_encode($rtn_msg);
		}	
	}
	//删除
	public function member_address_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_member_address where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
		echo json_encode($rtn_msg);
	}	
	
	public function member_address_default(){
		$id			= $this->_REQUEST("id");
		$member		=$this->L('home/WxMember')->member_get_info();
		$member_id	=$member['memeber_id'];
		
		$sql="update fly_member_address set ifdefault='0' where member_id='$member_id'";
		$this->C($this->cacheDir)->update($sql);
		
		$sql= "update fly_member_address set ifdefault='1' where id='$id'";
		$this->C($this->cacheDir)->update($sql);
		
		$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
		echo json_encode($rtn_msg);
	}
	
	//返回默认地址
	public function member_address_get_default(){
		$member	 =$this->L('home/WxMember')->member_get_info();
		$member_id		=$member['member_id'];
		$sql="select * from fly_member_address  where member_id='$member_id' order by ifdefault desc;";
		$one= $this->C($this->cacheDir)->findOne($sql);
		return $one;
	}
	
	
}//
?>