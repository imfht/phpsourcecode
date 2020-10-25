<?php
class WxMemberCard extends Action{
	private $cacheDir='';//缓存目录
	private $pcBalanceIntegral ='';
	public function __construct() {
		$this->auth=_instance('Action/home/WxAuth');
	}	
	
	public function member_card(){
		$member	  =$this->L('home/WxMember')->member_get_info();
		$where_str =" member_id='".$member['id']."' ";
		$sql = "select * from fly_member_card where $where_str order by id desc ";	
		$list= $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,'member_name'=>$member['account']);	
		return $assignArray;
	}
	
	public function member_card_show(){
			$assArr  = $this->member_card();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('home/member_card_show.html');	
	}
	//增加地址
	public function member_card_add(){
		$member	 =$this->L('home/WxMember')->member_get_info();
		$rtn_path	= $this->_REQUEST("rtn_path");
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$member,'rtn_path'=>$rtn_path));
			$smarty->display('home/member_card_add.html');	
		}else{
			$member_id= $member['id'];
			$name		= $this->_REQUEST("name");
			$cardname 	= $this->_REQUEST("cardname");
			$cardnumber = $this->_REQUEST("cardnumber");
			$cardaddress= $this->_REQUEST("cardaddress");

			
			$sql="insert into fly_member_card(member_id,name,cardname,cardnumber,cardaddress) 
										values('$member_id','$name','$cardname','$cardnumber','$cardaddress');";
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				$rtn_msg=array('code'=>'sucess','message'=>'操作成功'); 
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'操作失败'); 
			}
			
			echo json_encode($rtn_msg);
		}
	}
	
	public function member_card_edit(){
		$id	= $this->_REQUEST("id");
		if(empty($_POST)){
			$sql  = "select * from fly_member_card where id='$id'";
			$one  = $this->C($this->cacheDir)->findOne($sql);
			$smarty = $this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('home/member_card_edit.html');				
		}else{
			$name		= $this->_REQUEST("name");
			$cardname 	= $this->_REQUEST("cardname");
			$cardnumber = $this->_REQUEST("cardnumber");
			$cardaddress= $this->_REQUEST("cardaddress");
			
			$sql="update fly_member_card set name='$name', cardname='$cardname', cardnumber='$cardnumber', cardaddress='$cardaddress' where id='$id'";
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
	public function member_card_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_member_card where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
		echo json_encode($rtn_msg);
	}	
	public function member_card_default(){
		$id	= $this->_REQUEST("id");
		$member	 =$this->L('home/WxMember')->member_get_info();
		$member_id		=$member['id'];
		
		$sql="update fly_member_card set ifdefault='0' where member_id='$member_id'";
		$this->C($this->cacheDir)->update($sql);
		
		$sql= "update fly_member_card set ifdefault='1' where id='$id'";
		$this->C($this->cacheDir)->update($sql);
		
		$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
		echo json_encode($rtn_msg);
	}
	
	//返回默认地址
	public function member_card_get_default(){
		$member	 =$this->L('home/WxMember')->member_get_info();
		$member_id		=$member['id'];
		$sql="select * from fly_member_card  where member_id='$member_id' order by ifdefault desc;";
		$one= $this->C($this->cacheDir)->findOne($sql);
		return $one;
	}
	
	
}//
?>