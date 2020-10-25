<?php
namespace wstmart\common\model;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 短信日志类
 */
class LogSms extends Base{
	protected $pk = 'smsId';

	/**
	 * 写入并发送短讯记录
	 */
	public function sendSMS($smsSrc,$phoneNumber,$params,$smsFunc,$verfyCode,$userId=0,$isLimit=1){
		//判断有没有开启短信功能
		if((int)WSTConf('CONF.smsOpen')==0)return WSTReturn('未开启短信接口');
		$userId = $userId>0?(int)session('WST_USER.userId'):$userId;
		$ip = request()->ip();
		if($isLimit==1){
			//检测短信验证码验证是否正确
			if(WSTConf("CONF.smsVerfy")==1){
				//判断是否开启了外部的验证码
				if((int)WSTConf('CONF.isAddonCaptcha')!=1){
					$smsverfy = input("post.smsVerfy");
					$rs = WSTVerifyCheck($smsverfy);
					if(!$rs)return WSTReturn("验证码不正确!",-2);
				}else{
					$rs =  WSTReturn("短信发送校验失败!",-2);
					hook('checkSendSmsCaptcha',['rdata'=>&$rs,'params'=>input()]);
					if($rs['status']!=1)return $rs;
				}
			}
			//检测是否超过每日短信发送数
			$date = date('Y-m-d');
			$smsRs = $this->field("count(smsId) counts,max(createTime) createTime")
				 		  ->where(["smsPhoneNumber"=>$phoneNumber])
			 	          ->whereTime('createTime', 'between', [$date.' 00:00:00', $date.' 23:59:59'])->find();
			if($smsRs['counts']>(int)WSTConf("CONF.smsLimit")){
				return WSTReturn("请勿频繁发送短信验证!");
			}
			if($smsRs['createTime'] !='' && ((time()-strtotime($smsRs['createTime']))<15)){
				return WSTReturn("请勿频繁发送短信验证!");
			}
			//检测IP是否超过发短信次数
			$ipRs = $this->field("count(smsId) counts,max(createTime) createTime")
						 ->where(["smsIP"=>$ip])
						 ->whereTime('createTime', 'between', [$date.' 00:00:00', $date.' 23:59:59'])->find();
			if($ipRs['counts']>(int)WSTConf("CONF.smsLimit")){
				return WSTReturn("请勿频繁发送短信验证!");
			}
			if($ipRs['createTime']!='' && ((time()-strtotime($ipRs['createTime']))<15)){
				return WSTReturn("请勿频繁发送短信验证!");
			}
		}
		$data = array();
		$data['smsSrc'] = $smsSrc;
		$data['smsUserId'] = $userId;
		$data['smsPhoneNumber'] = $phoneNumber;
		$data['smsContent'] = 'N/A';
		$data['smsReturnCode'] = '';
		$data['smsCode'] = $verfyCode;
		$data['smsIP'] = $ip;
		$data['smsFunc'] = $smsFunc;
		$data['createTime'] = date('Y-m-d H:i:s');
		$this->data($data)->isUpdate(false)->save();
		//$rdata = ['msg'=>'短信ok!','status'=>1];
		$rdata = ['msg'=>'短信发送失败!','status'=>-1];
		try{
		hook('sendSMS',['phoneNumber'=>$phoneNumber,"params"=>$params,'smsId'=>$this->smsId,'status'=>&$rdata]);
		}catch(Exception $e){}
		return $rdata;
	}

	/**
	 * 写入并发送管理员短讯记录
	 */
	public function sendAdminSMS($smsSrc,$phoneNumber,$params,$smsFunc,$verfyCode,$userId=0){
		//判断有没有开启短信功能
		if((int)WSTConf('CONF.smsOpen')==0)return WSTReturn('未开启短信接口');
		$userId = $userId>0?(int)session('WST_USER.userId'):$userId;
		$ip = request()->ip();

		$data = array();
		$data['smsSrc'] = $smsSrc;
		$data['smsUserId'] = $userId;
		$data['smsPhoneNumber'] = $phoneNumber;
		$data['smsContent'] = 'N/A';
		$data['smsReturnCode'] = '';
		$data['smsCode'] = $verfyCode;
		$data['smsIP'] = $ip;
		$data['smsFunc'] = $smsFunc;
		$data['createTime'] = date('Y-m-d H:i:s');
		$this->save($data);
		$rdata = ['msg'=>'短信发送失败!','status'=>-1];
		hook('sendSMS',['phoneNumber'=>$phoneNumber,"params"=>$params,'smsId'=>$this->smsId,'status'=>&$rdata]);
		return $rdata;
	}
}
