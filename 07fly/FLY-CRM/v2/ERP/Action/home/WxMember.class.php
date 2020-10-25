<?php
class WxMember extends Action{	
	private $cacheDir='';//缓存目录
	private $pcMember='';//PC后台类
	public function __construct() {
		$this->auth	 =_instance('Action/home/WxAuth');
	}	
	//返回用户的基本信息
	public function member_get_info(){
		if(empty($id)) $acct=$_COOKIE['member_acct'];
		$sql  ="select * from fly_member where account='$acct' order by member_id desc";
		$one  =$this->C($this->cacheDir)->findOne($sql);
		$one  =$this->member_get_one($one['member_id']);
		return $one;
	}
	public function member_isexit(){
		$account	 = $this->_REQUEST("account");
		$sql="select * from fly_member where account='$account'";
		$one= $this->C($this->cacheDir)->findOne($sql);
		$rtn_msg=array();
		if(empty($one)){
			$rtn_msg=array('code'=>'fail','message'=>'用户不存在');
		}else{
			$rtn_msg=array('code'=>'sucess','message'=>'用户存在');
		}
		echo json_encode($rtn_msg);	
	}
	public function member_mobile_isexit(){
		$mobile	 = $this->_REQUEST("mobile");
		$sql="select * from fly_member where mobile='$mobile'";
		$one= $this->C($this->cacheDir)->findOne($sql);
		print_r($one);
		$rtn_msg=array();
		if(empty($one)){
			$rtn_msg=array('code'=>'fail','message'=>'手机号不存在');
		}else{
			$rtn_msg=array('code'=>'sucess','message'=>'手机号存在');
		}
		echo json_encode($rtn_msg);	
	}
	
	//检查余额是否充足
	public function member_balance_isexit(){
		$balance	 = $this->_REQUEST("balance");
		$member		=$this->member_get_info();
		$rtn_msg=array();
		if($balance>$member['balance']){
			$rtn_msg=array('code'=>'fail','message'=>'余额不足');
		}else{
			$rtn_msg=array('code'=>'sucess','message'=>'余额充足');
		}
		echo json_encode($rtn_msg);	
	}
	
	//会员中心
	public function member_center(){
		$member =$this->member_get_info();
		$smarty =$this->setSmarty();
		$smarty->assign(array('one'=>$member));
		$smarty->display('home/member_center.html');
	}
	
	//名称修改
	public function member_name_edit(){
		if(empty($_POST)){
			$one   =$this->member_get_info();
			$smarty =$this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('home/member_name_edit.html');
		}else{
			$member_name=$this->_REQUEST('member_name');
			$member_acct=$_COOKIE['member_acct'];
			$sql="update fly_member set name='$member_name' where account='$member_acct'";
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>=0){
				$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'操作失败');
			}
			echo json_encode($rtn_msg);
		}
	}
	
	//修改登录密码
	public function member_pwd_login_edit(){
		if(empty($_POST)){
			$one   =$this->member_get_info();
			$smarty =$this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('home/member_pwd_login_edit.html');
		}else{
			$one   	=$this->member_get_info();
			$account =$one['account'];
			$pwd_old =$this->_REQUEST('pwd_old');
			$pwd_new =$this->_REQUEST('pwd_new');
			if($one['password']!=$pwd_old){
				$rtn_msg=array('code'=>'fail','message'=>'输入旧密码错误！');
			}else{
				$sql="update fly_member set password='$pwd_new' where account='$account'";
				$rtn=$this->C($this->cacheDir)->update($sql);
				if($rtn>=0){
					$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
				}else{
					$rtn_msg=array('code'=>'fail','message'=>'操作失败');
				}				
			}

			echo json_encode($rtn_msg);
		}
	}
	//修改支付密码
	public function member_pwd_pay_edit(){
		if(empty($_POST)){
			$one   =$this->member_get_info();
			$smarty =$this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('home/member_pwd_pay_edit.html');
		}else{
			$one   	=$this->member_get_info();
			$account =$one['account'];
			$pwd_old =$this->_REQUEST('pwd_old');
			$pwd_new =$this->_REQUEST('pwd_new');
			if($one['password_pay']!=$pwd_old){
				$rtn_msg=array('code'=>'fail','message'=>'输入旧支付密码错误！');
			}else{
				$sql="update fly_member set password_pay='$pwd_new' where account='$account'";
				$rtn=$this->C($this->cacheDir)->update($sql);
				if($rtn>=0){
					$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
				}else{
					$rtn_msg=array('code'=>'fail','message'=>'操作失败');
				}				
			}

			echo json_encode($rtn_msg);
		}
	}
	//头像修改
	public function member_photo_edit(){
		if(empty($_POST)){
			$one   =$this->member_get_info();
			$smarty =$this->setSmarty();
			$smarty->assign(array('one'=>$one));
			$smarty->display('home/member_photo_edit.html');
		}else{
			$one   	=$this->member_get_info();
			$account =$one['account'];
			
			$imgs	   = $this->_REQUEST("imgs");
			if(!empty($imgs)){
				@$imgs_arr=explode(',',$imgs);
				@$img_1="/Apply/View/templates/home/webuploader/upload/".$imgs_arr[0];
			}else{
				$img_1='';
			}
			
			$sql="update fly_member set photo='$img_1' where account='$account'";
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>=0){
				$rtn_msg=array('code'=>'sucess','message'=>'操作成功');
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'操作失败');
			}
			echo json_encode($rtn_msg);
		}
	}	
	//传入ID返回名字
	public function member_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select member_id,account as name from fly_member where member_id in ($id) order by member_id desc";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $key=>$row){
				$str .= " ".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
	//传入ID返回名字
	public function member_get_one($id){
		if(empty($id)) $id=0;
		$sql  ="select * from fly_member where member_id in ($id) order by member_id desc";	
		$one  =$this->C($this->cacheDir)->findOne($sql);
		$photo ="";
		if(empty($one['photo'])){
			$photo='<img src="'.APP.'/View/template/home/img/list/home.png" style="width:40px;height: 40px; border-radius:50%;"  />';
		}else{
			$photo='<img src="'.$one['photo'].'" style="width:40px;height: 40px; border-radius:50%;" /> ';
		}
		$vip_txt="";
		if($one['integral']>=1000000){
			$vip_txt="vip";
		}
		$star_t="";
		for($x=0;$x<=$one['star'];$x++){
			$star_t .='<span class="hui-icons hui-icons-like hui-font-red hui-tl hui-font-s12" style="padding-right:2px;"></span>';
		}
		$one['vip_txt']=$vip_txt;
		$one['star_txt']=$star_t;
		$one['photo_img']=$photo;
		$one['parent_name']=$this->member_get_name($one['parent_id']);
		$one['type_name']=$this->L('home/WxMemberType')->member_type_get_name($one['member_type_id']);
		return $one;
	}
	//返回用户的下组用户
	public function member_get_son_list(){
		$member=$this->member_get_info();
		$p_id  =$member['member_id'];
		$sql  ="select * from fly_member where parent_id='$p_id' order by member_id desc";
		$list  =$this->C($this->cacheDir)->findAll($sql);
		foreach($list as  $key=>$row){
			$list[$key]=$this->member_get_one($row['member_id']);
		}
		return $list;
	}
	
}//
?>