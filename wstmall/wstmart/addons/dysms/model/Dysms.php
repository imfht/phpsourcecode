<?php
namespace addons\dysms\model;
use think\addons\BaseModel as Base;
use think\Db;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
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
 * 阿里云-云通信接口
 */
class Dysms extends Base{
	public function getConfigs(){
		$data = cache('dysms_sms');
		if(!$data){
			$rs = Db::name('addons')->where('name','Dysms')->field('config')->find();
		    $data =  json_decode($rs['config'],true);
		    cache('dysms_sms',$data,31622400);
		}
		return $data;
	}

	public function install(){
		Db::startTrans();
		try{
			$hooks = ['sendSMS'];
			$this->bindHoods("Dysms", $hooks);
			Db::commit();
			return true;
		}catch (\Exception $e) {
	 		Db::rollback();
	  		return false;
	   	}
	}
	public function uninstall(){
		Db::startTrans();
		try{
			$hooks = ['sendSMS'];
			$this->unbindHoods("Dysms", $hooks);
			Db::commit();
			return true;
		}catch (\Exception $e) {
	 		Db::rollback();
	  		return false;
	   	}
	}
	/**
	 * 发送短信接口
	 */
	public function http($params){
		$resp = '{"Message":"系统发送短信失败"}';
		try{
			require_once  WST_ADDON_PATH.'dysms/sdk/vendor/autoload.php';
			Config::load();
			$smsConf = $this->getConfigs();
			//此处需要替换成自己的AK信息
		    $accessKeyId = $smsConf['smsKey'];;
		    $accessKeySecret = $smsConf['smsPass'];
		    //短信API产品名（短信产品名固定，无需修改）
		    $product = "Dysmsapi";
		    //短信API产品域名（接口地址固定，无需修改）
		    $domain = "dysmsapi.aliyuncs.com";
		    //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
		    $region = "cn-hangzhou";
		    //初始化访问的acsCleint
		    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
		    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
		    $acsClient= new DefaultAcsClient($profile);
		    $request = new SendSmsRequest();
		    //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
		    $request->setPhoneNumbers($params['phoneNumber']);
		    //必填-短信签名
		    $request->setSignName($smsConf["signature"]);
		    //必填-短信模板Code
		    $request->setTemplateCode($smsConf[$params['params']['tpl']['tplCode']]);
		    //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
		    $request->setTemplateParam($params['content']);
		    //选填-发送短信流水号
		    $request->setOutId($params['timeId']);
		    //发起访问请求
		    $resp = $acsClient->getAcsResponse($request);
		    return $resp;
		}catch (\Exception $e) {
	    	$resp ='{"Message":"'.$e->getMessage().'"}';
	    }
        return json_decode($resp);
	}

	public function sendSMS($params){
		$smsConf = $this->getConfigs();
		$code = [];
		$isVerfy = false;
		foreach($params['params']['params'] as $key =>$v){
			$key = str_replace('_','',$key);
			if($key=='VERFIYCODE')$isVerfy = true;
		}
		foreach($params['params']['params'] as $key =>$v){
			$key = str_replace('_','',$key);
			if($isVerfy){
                if($key=='VERFIYCODE')$code[] = '"'.$key.'":"'.$v.'"';
			}else{
				$code[] = '"'.$key.'":"'.$v.'"';
			}
		}
        $codes = "{".implode(',',$code)."}";
        $params['content'] = $codes;
        $timeId = time().rand(100,999);
        $params['timeId'] = $timeId;
		$code = $this->http($params);
		$log = model('common/logSms')->get($params['smsId']);
		$log->smsReturnCode = json_encode($code);
		$log->smsContent = $codes."||".$params['params']['tpl']['tplCode']."||".$smsConf[$params['params']['tpl']['tplCode']]."||".$timeId;
		$log->save();
		if(strtolower($code->Message)=='ok'){
	        $params['status']['msg'] = '短信发送成功!';
	        $params['status']['status'] = 1;
		}else{
            $params['status']['msg'] = $code->Message;
	        $params['status']['status'] = -1;
		}
	}
}
