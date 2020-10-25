<?php
class Register extends Action {
	private $cacheDir='';
	//会员前台注册
	public function main(){
		if(empty($_POST)){
			$p_account	=$this->_REQUEST("recommend_acct");//是否有推荐帐号
			$smarty =$this->setSmarty();
			$smarty->assign(array('p_account'=>$p_account));
			$smarty->display('home/register.html');	
		}else{
			$p_account	=$this->_REQUEST("p_account");
			$mobile		=$this->_REQUEST("phone");
			$name		=$this->_REQUEST("name");
			$pwd		=$this->_REQUEST("pwd");
			$pwd_pay	=$this->_REQUEST("pwd_pay");
			$dt=date("Y-m-d H:i:s",time());
			
			$sql="select * from fly_member where account='$p_account'";
			$one= $this->C($this->cacheDir)->findOne($sql);
			if(!empty($one)){
				$parent_id=$one['member_id'];	
			}else{
				$parent_id='0';
			}
			
			$sql="select * from fly_member where account='$mobile'";
			$one= $this->C($this->cacheDir)->findOne($sql);
			if(empty($one)){
				$sql="insert into fly_member(account,password,password_pay,name,mobile,parent_id,adt) 
									values('$mobile','$pwd','$pwd_pay','$name','$mobile','$parent_id','$dt');";
				$rtn=$this->C($this->cacheDir)->update($sql);		
				if($rtn>0 ){
					$this->L('admin/MemberTree')->member_tree_add($parent_id,$rtn);
					$rtn_msg=array('code'=>'sucess','message'=>'注册成功');
				}else{
					$rtn_msg=array('code'=>'fail','message'=>'注册失败');
				}
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'手机已经注册了');
			}			
			echo urldecode(json_encode($rtn_msg));	
		}
	}
	
	//会员忘记登录密码
	public function forget_password(){
		if(empty($_POST)){
			$smarty =$this->setSmarty();
			$smarty->display('home/forget_password.html');	
		}else{
			$mobile		=$this->_REQUEST("phone");
			$name		=$this->_REQUEST("name");
			$pwd		=$this->_REQUEST("pwd");
			$pwd_pay	=$this->_REQUEST("pwd_pay");
			$dt=date("Y-m-d H:i:s",time());
			$sql="select id from fly_member where mobile='$mobile'";
			$one= $this->C($this->cacheDir)->findOne($sql);
			if(!empty($one)){
				$sql="update fly_member set password='$pwd' where mobile='$mobile'";
				$rtn=$this->C($this->cacheDir)->update($sql);		
				if($rtn>0 ){
					$rtn_msg=array('code'=>'sucess','message'=>'修改成功');
				}else{
					$rtn_msg=array('code'=>'fail','message'=>'修改失败');
				}
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'手机号不存在');
			}
			echo urldecode(json_encode($rtn_msg));	
		}
	}
	//会员忘记支付密码
	public function forget_password_pay(){
		if(empty($_POST)){
			$smarty =$this->setSmarty();
			$smarty->display('home/forget_password_pay.html');	
		}else{
			$mobile		=$this->_REQUEST("phone");
			$name		=$this->_REQUEST("name");
			$pwd		=$this->_REQUEST("pwd");
			$pwd_pay	=$this->_REQUEST("pwd_pay");
			$dt=date("Y-m-d H:i:s",time());
			$sql="select id from fly_member where mobile='$mobile'";
			$one= $this->C($this->cacheDir)->findOne($sql);
			if(!empty($one)){
				$sql="update fly_member set password_pay='$pwd' where mobile='$mobile'";
				$rtn=$this->C($this->cacheDir)->update($sql);		
				if($rtn>0 ){
					$rtn_msg=array('code'=>'sucess','message'=>'修改成功');
				}else{
					$rtn_msg=array('code'=>'fail','message'=>'修改失败');
				}
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'手机号不存在');
			}
			echo urldecode(json_encode($rtn_msg));	
		}
	}
	//短信发送接口
	public function api_sms_send(){
		$mobile		=$this->_REQUEST("phone");
		$ipaddr		=$this->_REQUEST("ipaddr");
		$rname		=$this->_REQUEST("rname");//==username
		$sms_conf	=$this->L('admin/Sms')->get_sms_info();
		$acc		=$sms_conf["sms_account"];
		$pwd		=$sms_conf["sms_password"];	
		$html		=$sms_conf["sms_content"];
		$sms_time   =$sms_conf["sms_time_interval"];
		$random		=empty($random)?rand(1000,9999):$random;
		$tagsArr	=array('smsCode'=>$random,'username'=>$rname);
		$sms_content=$this->L("Common")->replace_tags($tagsArr,$html);
		if($this->L('admin/Sms')->sms_send($mobile,$sms_content,$ipaddr)){
			return $random;
			//$rtn_msg=array('code'=>'sucess','message'=>"$random");
		}else{
			return 0;
			//$rtn_msg=array('code'=>'fail','message'=>'发送失败');
		}
		//echo urldecode(json_encode($rtn_msg));		
	}
	public function member_mobile_isexit($mobile){
		$sql="select * from fly_member where mobile='$mobile'";
		$one= $this->C($this->cacheDir)->findOne($sql);
		if(!empty($one)){
			return true;
		}else{
			return false;
		}
	}
	
	public function api_sms_send_login(){
		$mobile		=$this->_REQUEST("phone");
		$rtn=$this->member_mobile_isexit($mobile);
		if($rtn){
			$rtn_code=$this->api_sms_send();
			if($rtn_code){
				$rtn_msg=array('code'=>'sucess','message'=>"$rtn_code");
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'发送失败');
			}			
		}else{
			$rtn_msg=array('code'=>'fail','message'=>'手机号未注册');
		}
		echo urldecode(json_encode($rtn_msg));		
	}

	public function api_sms_send_reg(){
		$mobile		=$this->_REQUEST("phone");
		$rtn=$this->member_mobile_isexit($mobile);
		if(!$rtn){
			$rtn_code=$this->api_sms_send();
			if($rtn_code){
				$rtn_msg=array('code'=>'sucess','message'=>"$rtn_code");
			}else{
				$rtn_msg=array('code'=>'fail','message'=>'发送失败');
			}			
		}else{
			$rtn_msg=array('code'=>'fail','message'=>'手机号已注册');
		}
		echo urldecode(json_encode($rtn_msg));		
	}
	
} //

?>