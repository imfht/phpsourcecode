<?php
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
 */

use think\Db;


const WST_ADDON_PATH = './addons/';
/**
* 用户名称匿名处理
* @param $str 	  需要处理的字符串
* @param $subject 用来替换的字符串，默认为：星号(*)
*/
function WSTAnonymous($str, $subject='*'){
    $len = mb_strlen($str,'utf-8');
    if($len>=6){
    	$cut_start = floor($len/3);
    	$cut_end = floor($len*2/3);
        $str1 = mb_substr($str,0,$cut_start,'utf-8');
        $str2 = mb_substr($str,$len-$cut_start,$cut_end,'utf-8');
    }
    else{
        $str1 = mb_substr($str,0,1,'utf-8');
        $str2 = mb_substr($str,$len-1,1,'utf-8');            
    }
    $length = $len - mb_strlen($str1) - mb_strlen($str2);
    return $str1 . str_repeat($subject, $length) . $str2;
}
/**
 * 生成验证码
 */
function WSTVerify(){
	$Verify = new \verify\Verify();
    $Verify->length   = 4;
    $Verify->entry();
}
/**
 * 核对验证码
 */
function WSTVerifyCheck($code){
	$code = str_replace(' ','',$code);
	$verify = new \verify\Verify();
	return $verify->check($code);
}
/**
 * 生成数据返回值
 */
function WSTReturn($msg,$status = -1,$data = []){
	$rs = ['status'=>$status,'msg'=>$msg];
	if(!empty($data))$rs['data'] = $data;
	return $rs;
}
/**
 * 生成数据返回值
 */
function jsonReturn($msg,$status = -1,$data = []){
	if(isset($data['status']))return json_encode($data);
	$rs = ['status'=>$status,'msg'=>$msg];
	if(!empty($data))$rs['data'] = $data;
	return json_encode($rs);
}

/**
 * 检测字符串不否包含
 * @param $srcword 被检测的字符串
 * @param $filterWords 禁用使用的字符串列表
 * @return boolean true-检测到,false-未检测到
 */
function WSTCheckFilterWords($srcword,$filterWords){
	$flag = true;
	if($filterWords!=""){
		$filterWords = str_replace("，",",",$filterWords);
		$words = explode(",",$filterWords);
		for($i=0;$i<count($words);$i++){
			if($words[$i]=='')continue;
			if(strpos($srcword,$words[$i]) !== false){
				$flag = false;
				break;
			}
		}
	}
	return $flag;
}

/**
 * 检测并替换字符串
 * @param $srcword 被检测的字符串
 * @param $filterWords 禁用使用的字符串列表
 * @return boolean true-检测到,false-未检测到
 */
function WSTReplaceFilterWords($srcword,$filterWords,$rword='✲✲✲'){
	$flag = true;
	if($filterWords!=""){
		$filterWords = str_replace("，",",",$filterWords);
		$words = explode(",",$filterWords);
		for($i=0;$i<count($words);$i++){
			if($words[$i]=='')continue;
			$srcword = str_replace($words[$i], $rword, $srcword);
		}
	}
	return $srcword;
}

/**
 * 获取指定的全局配置
 */
function WSTConf($key,$v = ''){
	if(is_null($v)){
		if(array_key_exists('WSTMARTCONF',$GLOBALS) && array_key_exists($key,$GLOBALS['WSTMARTCONF'])){
		    unset($GLOBALS['WSTMARTCONF'][$key]);
		}
	}else if($v === ''){
		if(array_key_exists('WSTMARTCONF',$GLOBALS)){
			$conf = $GLOBALS['WSTMARTCONF'];
			$ks = explode(".",$key);
			for($i=0,$k=count($ks);$i<$k;$i++){
				if(array_key_exists($ks[$i],$conf)){
					$conf = $conf[$ks[$i]];
				}else{
					return null;
				}
			}
			return $conf;
		}
	}else{
		return $GLOBALS['WSTMARTCONF'][$key] = $v;
	}
	return null;
}

//php获取中文字符拼音首字母
function WSTGetFirstCharter($str){
	if(empty($str)){
		return '';
	}
	$fchar=ord($str{0});
	if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
	$s1=iconv('UTF-8','gb2312',$str);
	$s2=iconv('gb2312','UTF-8',$s1);
	$s=$s2==$str?$s1:$str;
	if(empty($s{1})){
		return '';
	}
	$asc=ord($s{0})*256+ord($s{1})-65536;
	if($asc>=-20319 && $asc<=-20284) return 'A';
	if($asc>=-20283 && $asc<=-19776) return 'B';
	if($asc>=-19775 && $asc<=-19219) return 'C';
	if($asc>=-19218 && $asc<=-18711) return 'D';
	if($asc>=-18710 && $asc<=-18527) return 'E';
	if($asc>=-18526 && $asc<=-18240) return 'F';
	if($asc>=-18239 && $asc<=-17923) return 'G';
	if($asc>=-17922 && $asc<=-17418) return 'H';
	if($asc>=-17417 && $asc<=-16475) return 'J';
	if($asc>=-16474 && $asc<=-16213) return 'K';
	if($asc>=-16212 && $asc<=-15641) return 'L';
	if($asc>=-15640 && $asc<=-15166) return 'M';
	if($asc>=-15165 && $asc<=-14923) return 'N';
	if($asc>=-14922 && $asc<=-14915) return 'O';
	if($asc>=-14914 && $asc<=-14631) return 'P';
	if($asc>=-14630 && $asc<=-14150) return 'Q';
	if($asc>=-14149 && $asc<=-14091) return 'R';
	if($asc>=-14090 && $asc<=-13319) return 'S';
	if($asc>=-13318 && $asc<=-12839) return 'T';
	if($asc>=-12838 && $asc<=-12557) return 'W';
	if($asc>=-12556 && $asc<=-11848) return 'X';
	if($asc>=-11847 && $asc<=-11056) return 'Y';
	if($asc>=-11055 && $asc<=-10247) return 'Z';
	return null;
}

/**
 * 邮件发送函数
 * @param string to      要发送的邮箱地址
 * @param string subject 邮件标题
 * @param string content 邮件内容
 * @return array
 */
function WSTSendMail($to, $subject, $content) {
	$rs = [];
	if(WSTConf("CONF.mailOpen")==1){
		require Env::get('root_path') . 'extend/phpmailer/phpmailer.php';
		$mail = new \phpmailer\phpmailer();
	    // 装配邮件服务器
	    $mail->IsSMTP();
	    $mail->SMTPDebug = 0;
	    $mail->Timeout = 5;
	    $mail->Host = (WSTConf("CONF.mailOpenSSL")==1?'ssl://':'').WSTConf("CONF.mailSmtp");
	    $mail->SMTPAuth = WSTConf("CONF.mailAuth");
	    $mail->Username = WSTConf("CONF.mailUserName");
	    $mail->Password = WSTConf("CONF.mailPassword");
	    $mail->CharSet = 'utf-8';
	    $mail->Port = WSTConf("CONF.mailPort");
	    // 装配邮件头信息
	    $mail->From = WSTConf("CONF.mailAddress");
	    $mail->AddAddress($to);
	    $mail->FromName = WSTConf("CONF.mailSendTitle");
	    $mail->IsHTML(true);
	    // 装配邮件正文信息
	    $mail->Subject = $subject;
	    $mail->Body = $content;
	    // 发送邮件
	    $rs =array();
	    if (!$mail->Send()) {
	    	$rs['status'] = 0;
	    	$rs['msg'] = $mail->ErrorInfo;
	        return $rs;
	    } else {
	    	$rs['status'] = 1;
	        return $rs;
	    }
	}
	return ['status'=>0,'msg'=>'未开启邮件发送功能'];
}

/**
 * 获取系统配置数据
 */
function WSTConfig(){
	$rs = cache('WST_CONF');
	if(!$rs){
		$rv = Db::name('sys_configs')->field('fieldCode,fieldValue')->select();
		$rs = [];
		foreach ($rv as $v){
			$rs[$v['fieldCode']] = $v['fieldValue'];
		}
		//获取风格
        $styles = Db::name('styles')->where('isUse','=',1)->field('styleSys,stylePath,id')->select();
        if(!empty($styles)){
	        foreach ($styles as $key => $v) {
		        $rs['wst'.$v['styleSys'].'Style'] = $v['stylePath'];
		        $rs['wst'.$v['styleSys'].'StyleId'] = $v['id'];
	        }
        }
        //判断资源路径
        $rs['resourcePath'] = ($rs['ossService']!='')?WSTProtocol().$rs['ossBucket'].'.'.$rs['ossBucketDomain']:str_replace('/index.php','',request()->root());
        $rs['resourceDomain'] = ($rs['ossService']!='')?WSTProtocol().$rs['ossBucket'].'.'.$rs['ossBucketDomain']:str_replace('/index.php','',request()->root(true));
		//获取上传文件目录配置
		$data = Db::name('datas')->where('catId','=',3)->column('dataVal');
		foreach ($data as $key => $v){
			$data[$key] = str_replace('_','',$v);
		}
		$rs['wstUploads'] = $data;
		if($rs['mallLicense']=='')$rs['mallSlogan'] = $rs['mallSlogan']."  ".base64_decode('UG93ZXJlZCBCeSBXU1RNYXJ0');
		// 是否有app端
		$prefix = config('database.prefix');
		$rs['hasApp'] = !!Db::query("show tables like '{$prefix}app_session';");
		cache('WST_CONF',$rs,31536000);
	}
	return $rs;
} 

/**
 * 判断手机号格式是否正确
 */
function WSTIsPhone($phoneNo){
	$reg = "/^1[\d]{10}$$/";
	$rs = Validate::regex($phoneNo,$reg);
	return $rs;
}

/**
 * 检测登录账号是否可用
 * @param $key 要检测的内容
 */
function WSTCheckLoginKey($val,$userId = 0){
    if($val=='')return WSTReturn("登录账号不能为空");
    if(!WSTCheckFilterWords($val,WSTConf("CONF.registerLimitWords"))){
    	return WSTReturn("登录账号包含非法字符");
    }
    $dbo = Db::name('users')->where([["loginName|userEmail|userPhone",'=',$val],['dataFlag','=',1]]);
    if($userId>0){
    	$dbo->where("userId", "<>", $userId);
    }
    $rs = $dbo->count();
    if($rs==0){
    	return WSTReturn("该登录账号可用",1);
    }
    return WSTReturn("对不起，登录账号已存在");
}

/**
 * 生成随机数账号
 */
function WSTRandomLoginName($loginName){
	$chars = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
    //简单的派字母
    foreach ($chars as $key =>$c){
    	$crs = WSTCheckLoginKey($loginName."_".$c);
    	if($crs['status']==1)return $loginName."_".$c;
    }
    //随机派三位数值
    for($i=0;$i<1000;$i++){
    	$crs = $this->WSTCheckLoginKey($loginName."_".$i);
    	if($crs['status']==1)return $loginName."_".$i;
    }
    return '';
}

/**
 * 开启自动注册生成的随机账号
 */
function WSTAutoRegisterName(){
    $chars = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",'0','1','2','3','4','5','6','7','8','9');
    $loginName = '';
    for($i=0;$i<13;$i++){
        $rand = rand(0,35);
        $loginName .= $chars[$rand];
    }
    $loginName = 'wst_'.$loginName;
    $crs = WSTCheckLoginKey($loginName);
    if($crs['status']==1)return $loginName;
    return '';
}

/*
 * 微信端,小程序端自动注册 $loginSrc=5小程序端，$loginSrc=1微信端
 */
function WSTAutoRegister($loginSrc = 0){
    $data = [];
    $openId = '';
    $userName = '';
    $userPhoto = '';
    $wxUnionId = '';
    $user = [];
    $sendMsg = false;
    //检测账号是否存在
    if($loginSrc == 5){
        $openId = input('openId','');
        $user = Db::name('users')->where([["weOpenId",'=',$openId],['dataFlag','=',1]])->find();
    }else{
        $openId = session('WST_WX_OPENID')?session('WST_WX_OPENID'):'';
        if($openId!=''){
            $user = Db::name('users')->where([["wxOpenId",'=',$openId],['dataFlag','=',1]])->find();
        }
        $userinfo = session('WST_WX_USERINFO');
		if($userinfo){
            $nickname = json_encode($userinfo['nickname']);
            $nickname = preg_replace("/\\\u[ed][0-9a-f]{3}\\\u[ed][0-9a-f]{3}/","*",$nickname);//替换成*
            $userName = json_decode($nickname);
            
			$userPhoto = $userinfo['headimgurl'];
            $wxUnionId = isset($userinfo['unionid'])?$userinfo['unionid']:'';
		}
    }
    
    if(empty($user)) {
        $sendMsg = true;
        $loginName = WSTAutoRegisterName();
        $crs = WSTCheckLoginKey($loginName);
        if ($crs['status'] != 1) return $crs;
        $data['loginName'] = $loginName;
        $data['loginPwd'] = rand(100000, 999999);
        $data["loginSecret"] = rand(1000, 9999);
        $data['loginPwd'] = $loginSrc == 1 ? md5($data['loginPwd'] . $data['loginSecret']) : '';
        $data['userType'] = 0;
        $data['userName'] = ($userName!="")?$userName:$loginName;
        $data['userPhoto'] = ($userPhoto!="")?$userPhoto:"";
        $data['wxUnionId'] = ($wxUnionId!="")?$wxUnionId:"";
        $data['userQQ'] = "";
        $data['userScore'] = 0;
        $data['createTime'] = date('Y-m-d H:i:s');
        $data['dataFlag'] = 1;
        $data['userStatus'] = 1;
        if ($loginSrc == 5) {
            $data['weOpenId'] = $openId;
        } else {
            $data['wxOpenId'] = $openId;
        }
    }

    Db::startTrans();
    try {
        if(empty($user)) {
            $userId = Db::name('users')->insertGetId($data);
        }else {
            $userId = $user['userId'];
        }
        if (false !== $userId) {
            $data = array();
            $ip = request()->ip();
            $data['lastTime'] = date('Y-m-d H:i:s');
            $data['lastIP'] = $ip;
            Db::name('users')->where(["userId" => $userId])->update($data);
            //记录登录日志
            $data = array();
            $data["userId"] = $userId;
            $data["loginTime"] = date('Y-m-d H:i:s');
            $data["loginIp"] = $ip;
            $data['loginSrc'] = $loginSrc;
            Db::name('log_user_logins')->insert($data);
            $user = Db::name('users')->where(['userId' => $userId])->find();
            if ($user['userPhoto'] == '') $user['userPhoto'] = WSTConf('CONF.userLogo');
            if ($loginSrc == 1) {
                session('WST_USER', $user);
            }
            //注册成功后执行钩子
            hook('afterUserRegist', ['user' => $user]);
            //发送消息
            $tpl = WSTMsgTemplates('USER_REGISTER');
            if ($tpl['tplContent'] != '' && $tpl['status'] == '1' && $sendMsg) {
                $find = ['${LOGIN_NAME}', '${MALL_NAME}'];
                $replace = [$user['loginName'], WSTConf('CONF.mallName')];
                WSTSendMsg($userId, str_replace($find, $replace, $tpl['tplContent']), ['from' => 0, 'dataId' => 0]);
            }
            if ($loginSrc == 5) {
                //记录tokenId
                $data = array();
                $key = sprintf('%011d', $userId);
                $tokenId = WSTToGuidString($key . time());
                $data['userId'] = $userId;
                $data['tokenId'] = $tokenId;
                $data['startTime'] = date('Y-m-d H:i:s');
                $data['deviceId'] = input('deviceId');
                Db::name('weapp_session')->insert($data);
                if(!empty($user)) {
                    //删除上一条登录记录
                    Db::name('weapp_session')->where('tokenId!="' . $tokenId . '" and userId=' . $userId)->delete();
                }
            }
            Db::commit();
            if ($loginSrc == 1) {
                return true;
            } else {
                return $tokenId;
            }
        }
    } catch (\Exception $e) {
        if ($loginSrc == 1) {
            session('WST_USER', null);
        }
        Db::rollback();
    }
    return false;
}

function WSTToGuidString($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * 删除一维数组里的多个key
 */
function WSTUnset(&$data,$keys){
    if($keys!='' && is_array($data)){
        $key = explode(',',$keys);
        foreach ($key as $v)unset($data[$v]);
    }
}
/**
 * 只允许一维数组里的某些key通过
 */
function WSTAllow(&$data,$keys){
    if($keys!='' && is_array($data)){
        $key = explode(',',$keys);
        foreach ($data as $vkeys =>$v)if(!in_array($vkeys,$key))unset($data[$vkeys]);
    }
}

/**
 * 字符串替换
 * @param string $str     要替换的字符串
 * @param string $repStr  即将被替换的字符串
 * @param int $start      要替换的起始位置,从0开始
 * @param string $splilt  遇到这个指定的字符串就停止替换
 */
function WSTStrReplace($str,$repStr,$start,$splilt = ''){
	$newStr = substr(utf8_encode($str),0,$start);
	$breakNum = -1;
	for ($i=$start;$i<strlen($str);$i++){
		$char = substr($str,$i,1);
		if($char==$splilt){
			$breakNum = $i;
			break;
		}
		$newStr.=$repStr;
	}
	if($splilt!='' && $breakNum>-1){
		for ($i=$breakNum;$i<strlen($str);$i++){
			$char = substr($str,$i,1);
			$newStr.=$char;
		}
	}
	return $newStr;
}

/**
 * 获取指定商品分类的子分类列表/获取指定的商品分类，靠$isSelf=-1判断
 */
function WSTGoodsCats($parentId = 0,$isFloor = -1,$isSelf = 0){
	if($isSelf==1){
        return Db::name('goods_cats')->where(['dataFlag'=>1, 'isShow' => 1,'catId'=>$parentId])
                 ->field("catName,simpleName,catId,parentId")->order('catSort asc')->find();
	}else{
		$dbo = Db::name('goods_cats')->where(['dataFlag'=>1, 'isShow' => 1,'parentId'=>$parentId]);
	    if($isFloor!=-1)$dbo->where('isFloor',$isFloor);
	    return $dbo->field("catName,simpleName,catId")->order('catSort asc')->select();
	}
}


/**
 * 获取指定行业的子分类列表/获取指定的行业
 */
function WSTTrades($parentId = 0,$isSelf = 0){
	if($isSelf==1){
        return Db::name('trades')->where(['dataFlag'=>1, 'isShow' => 1,'tradeId'=>$parentId])
                 ->field("tradeName,simpleName,tradeId,parentId")->order('tradeSort asc')->find();
	}else{
		$dbo = Db::name('trades')->where(['dataFlag'=>1, 'isShow' => 1,'parentId'=>$parentId]);
	    return $dbo->field("tradeName,simpleName,tradeId,tradeFee")->order('tradeSort asc')->select();
	}
}

/**
 * 获取指定店铺经营的商城分类
 */
function WSTShopApplyGoodsCats($parentId = 0){
	$shopId = (int)session('WST_USER.shopId');
    $rs = Db::name('goods_cats')->alias('gc')
             ->join('__CAT_SHOPS__ csp','gc.catId=csp.catId')
             ->where(['dataFlag'=>1, 'isShow' => 1,'gc.parentId'=>$parentId,'csp.shopId'=>$shopId])
             ->field("catName,simpleName,gc.catId,parentId")->order('catSort asc')->select();
    return $rs;
}


// 检测图片是否需要旋转
function checkImageOrientation($image, $imageSrc){
	if(!is_file($imageSrc))return $image;
	try{
		$exif = exif_read_data($imageSrc);
		if(isset($exif['Orientation']) && !empty($exif['Orientation'])) {
			switch($exif['Orientation']) {
				// 插件中对传入的角度前面加上的负数(-)，故传入角度时需与文档中取反
				// 文档参考：https://www.php.net/manual/zh/function.exif-read-data.php 关键字：Orientation
				case 8:
					$image = $image->rotate(-90);
				break;
				case 3:
					$image = $image->rotate(-180);
				break;
				case 6:
					$image = $image->rotate(90);
				break;
			}
		}
		$image->save($imageSrc, $image->type(), 100);
		return $image;
	}catch(\Exception $e){
		return $image;
	}
}

/**
 * 上传图片
 * 需要生成缩略图： isThumb=1
 * 需要加水印：isWatermark=1
 * pc版缩略图： width height
 * 手机版原图：mWidth mHeight
 * 缩略图：mTWidth mTHeight
 * 判断图片来源：fromType 0：商家/用户   1：平台管理员
 */
function WSTUploadPic($fromType=0){
	$fileKey = key($_FILES);
	$dir = Input('param.dir');
	if($dir=='')return json_encode(['msg'=>'没有指定文件目录！','status'=>-1]);
	$dirs = WSTConf("CONF.wstUploads");
   	if(!in_array($dir, $dirs)){
   		return json_encode(['msg'=>'非法文件目录！','status'=>-1]);
   	}
   	// 上传文件
    $file = request()->file($fileKey);
    if($file===null){
    	return json_encode(['msg'=>'上传文件不存在或超过服务器限制','status'=>-1]);
    }
	$rule = [
	    'type'=>'image/png,image/gif,image/jpeg,image/x-ms-bmp',
	    'ext'=>'jpg,jpeg,gif,png,bmp',
	    'size'=>'20971520'
	];
    $info = $file->validate($rule)->rule('uniqid')->move(Env::get('root_path').'upload/'.$dir."/".date('Y-m'));
    if($info){
    	$filePath = $info->getPathname();
    	$filePath = str_replace(Env::get('root_path'),'',$filePath);
    	$filePath = str_replace('\\','/',$filePath);
    	$imgSrc = $info->getFilename();
    	$filePath = str_replace($imgSrc,'',$filePath);
    	//原图路径
    	$imageSrc = trim($filePath.$imgSrc,'/');
    	//图片记录
    	WSTRecordResources($imageSrc, (int)$fromType);
    	//打开原图
    	$image = \image\Image::open($imageSrc);
    	//缩略图路径 手机版原图路径 手机版缩略图路径
    	$thumbSrc = $mSrc = $mThumb = null;
    	//手机版原图宽高
    	$mWidth = min($image->width(),(int)input('mWidth',700));
		$mHeight = min($image->height(),(int)input('mHeight',700));
		//手机版缩略图宽高
		$mTWidth = min($image->width(),(int)input('mTWidth',250));
		$mTHeight = min($image->height(),(int)input('mTHeight',250));

    	/****************************** 生成缩略图 *********************************/
    	$isThumb = (int)input('isThumb');
    	if($isThumb==1){

			// 检测是否需要翻转图片
			$image = checkImageOrientation($image, $imageSrc);

    		//缩略图路径
    		$thumbSrc = str_replace('.', '_thumb.', $imageSrc);
    		$image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
    		//是否需要生成移动版的缩略图
    		$suffix = WSTConf("CONF.wstMobileImgSuffix");
    		if(!empty($suffix)){
    			$image = \image\Image::open($imageSrc);
    			$mSrc = str_replace('.',"$suffix.",$imageSrc);
    			$mThumb = str_replace('.', '_thumb.',$mSrc);
    			$image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
    			$image->thumb($mTWidth, $mTHeight, 2)->save($mThumb,$image->type(),90);
    		}


    	}
    	/***************************** 添加水印 ***********************************/
    	$isWatermark=(int)input('isWatermark');
    	if($isWatermark==1 && (int)WSTConf('CONF.watermarkPosition')!==0){
	    	//取出水印配置
	    	$wmWord = WSTConf('CONF.watermarkWord');//文字
	    	$wmFile = trim(WSTConf('CONF.watermarkFile'),'/');//水印文件
	    	//判断水印文件是否存在
	    	if(!file_exists(WSTRootPath()."/".$wmFile))$wmFile = '';
	    	$wmPosition = (int)WSTConf('CONF.watermarkPosition');//水印位置
	    	$wmSize = ((int)WSTConf('CONF.watermarkSize')!=0)?WSTConf('CONF.watermarkSize'):'20';//大小
	    	$wmColor = (WSTConf('CONF.watermarkColor')!='')?WSTConf('CONF.watermarkColor'):'#000000';//颜色必须是16进制的
	    	$wmOpacity = ((int)WSTConf('CONF.watermarkOpacity')!=0)?WSTConf('CONF.watermarkOpacity'):'100';//水印透明度
	    	//是否有自定义字体文件
	    	$customTtf = Env::get('root_path').WSTConf('CONF.watermarkTtf');
	    	$ttf = is_file($customTtf)?$customTtf:Env::get('extend_path').'verify/verify/ttfs/3.ttf';
	        $image = \image\Image::open($imageSrc);
	    	if(!empty($wmWord)){//当设置了文字水印 就一定会执行文字水印,不管是否设置了文件水印
	    		// 文字偏移量
	    		$offset = WSTConf('CONF.watermarkOffset');
	    		if($offset!=''){
	    			$offset = explode(',',str_replace('，', ',',$offset));
	    			$offset = array_slice($offset,0,2);
	    			$offset = array_map(function($val){return (int)$val;},$offset);
	    			if(count($offset)<2)array_push($offset, 0);
	    		}
	    		//执行文字水印
	    		$image->text($wmWord, $ttf, $wmSize, $wmColor, $wmPosition,$offset)->save($imageSrc);
	    		if($thumbSrc!==null){
	    			$image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
	    		}
	    		//如果有生成手机版原图
	    		if(!empty($mSrc)){
	    			$image = \image\Image::open($imageSrc);
	    			$image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
	    			$image->thumb($mTWidth, $mTHeight, 2)->save($mThumb,$image->type(),90);
	    		}
	    	}elseif(!empty($wmFile)){//设置了文件水印,并且没有设置文字水印
	    		//执行图片水印
	    		$image->water($wmFile, $wmPosition, $wmOpacity)->save($imageSrc);
	    		if($thumbSrc!==null){
	    			$image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
	    		}
	    		//如果有生成手机版原图
	    		if($mSrc!==null){
	    			$image = \image\Image::open($imageSrc);
	    			$image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
	    			$image->thumb($mTWidth, $mTHeight,2)->save($mThumb,$image->type(),90);
	    		}
	    	}
    	}
    	//判断是否有生成缩略图
    	$thumbSrc = ($thumbSrc==null)?$info->getFilename():str_replace('.','_thumb.', $info->getFilename());
		$filePath = ltrim($filePath,'/');
		// 用户头像上传宽高限制
		$isCut = (int)input('isCut');
		if($isCut){
			$imgSrc = $filePath.$info->getFilename();
			$image = \image\Image::open($imgSrc);
			$size = $image->size();//原图宽高
			$w = $size[0];
			$h = $size[1];
			$rate = $w/$h;
			if($w>$h && $w>500){
				$newH = 500/$rate;
				$image->thumb(500, $newH)->save($imgSrc,$image->type(),90);
			}elseif($h>$w && $h>500){
				$newW = 500*$rate;
				$image->thumb($newW, 500)->save($imgSrc,$image->type(),90);
			}
		}
		$info=null;
		$rdata = ['status'=>1,'savePath'=>$filePath,'name'=>$imgSrc,'thumb'=>$thumbSrc];
		hook('afterUploadPic',['data'=>&$rdata,'isLocation'=>(int)input('isLocation')]);
        return json_encode($rdata);
    }else{
        //上传失败获取错误信息
        return $file->getError();
    }    
}
/**
 * 上传文件
 */
function WSTUploadFile(){
	$fileKey = key($_FILES);
	$dir = Input('post.dir');
	if($dir=='')return json_encode(['msg'=>'没有指定文件目录！','status'=>-1]);
	$dirs = WSTConf("CONF.wstUploads");
   	if(!in_array($dir, $dirs)){
   		return json_encode(['msg'=>'非法文件目录！','status'=>-1]);
   	}
   	//上传文件
    $file = request()->file($fileKey);
    if($file===null){
    	return json_encode(['msg'=>'上传文件不存在或超过服务器限制','status'=>-1]);
    }
	$rule = [
	    'ext'=>'xls,xlsx,xlsm'
	];
    $info = $file->validate($rule)->rule('uniqid')->move(Env::get('root_path').'/upload/'.$dir."/".date('Y-m'));
	if($info){
	    //保存路径
		$filePath = $info->getPathname();
		$filePath = str_replace(Env::get('root_path'),'',$filePath);
		$filePath = str_replace('\\','/',$filePath);
		$name = $info->getFilename();
		$filePath = str_replace($name,'',$filePath);
		return json_encode(['status'=>1,'name'=>$info->getFilename(),'route'=>$filePath]);
	}else{
		//上传失败获取错误信息
		return $file->getError();
	}
}
/**
 * 上传文件
 */
function WSTUploadVideo(){
	$fileKey = key($_FILES);
	$dir = Input('post.dir');
	if($dir=='')return json_encode(['msg'=>'没有指定文件目录！','status'=>-1]);
	$dirs = WSTConf("CONF.wstUploads");
   	if(!in_array($dir, $dirs)){
   		return json_encode(['msg'=>'非法文件目录！','status'=>-1]);
   	}
   	//上传文件
    $file = request()->file($fileKey);
    if($file===null){
    	return json_encode(['msg'=>'上传文件不存在或超过服务器限制','status'=>-1]);
    }
    $rule = ['ext'=>'3gp,mp4,rmvb,mov,avi,m4v'];
    $info = $file->validate($rule)->rule('uniqid')->move(Env::get('root_path').'/upload/'.$dir."/".date('Y-m'));
	if($info){
		//保存路径
	    $filePath = $info->getPathname();
		$filePath = str_replace(Env::get('root_path').'/','',$filePath);
		$filePath = str_replace('\\','/',$filePath);
		$name = $info->getFilename();
		$filePath = str_replace($name,'',$filePath);
		$rdata = ['status'=>1,'name'=>$info->getFilename(),'savePath'=>$filePath];
		// 视频记录
		$videoSrc = trim($filePath.$name,'/');
		// 只有商家才能上传视频
    	WSTRecordResources($videoSrc, 0, 1);
		hook('afterUploadPic',['data'=>&$rdata,'isVideo'=>true]);
		return json_encode($rdata);
	}else{
		//上传失败获取错误信息
		return $file->getError();
	}
}
/**
 * 生成默认商品编号/货号
 */
function WSTGoodsNo($pref = ''){
	return $pref.(round(microtime(true),4)*10000).mt_rand(0,9);
}
/**
 * 获取订单统一流水号
 */
function WSTOrderQnique(){
	return (round(microtime(true),4)*10000).mt_rand(1000,9999);
}


/**
* 资源管理
* @param $resPath    资源路径
* @param $fromType   0：用户/商家 1：平台管理员
* @param $resType   0：图片 1：视频
*/
function WSTRecordResources($resPath, $fromType, $resType=0){
	$data = [];
	$data['resPath'] = $resPath;
	if(file_exists($resPath)){
		$data['resSize'] = filesize($resPath); //返回字节数 resSize/1024 k  	resSize/1024/1024 m
	}
	//获取表名
	$table = explode('/',$resPath);
	$data['fromTable'] = $table[1];
	$data['fromType'] = (int)$fromType; 
	//根据类型判断所有者
	$data['ownId'] = ((int)$fromType==0)?(int)session('WST_USER.userId'):(int)session('WST_STAFF.staffId');
	$data['isUse'] = 0; //默认不使用
	$data['createTime'] = date('Y-m-d H:i:s');
	$data['resType'] = $resType;

	//保存记录
	Db::name('resources')->insert($data);

}
/**
* 启用资源文件
* @param $fromType 0：  用户/商家 1：平台管理员
* @param $dataId        来源记录id
* @param $resPath       资源路径,要处理多个资源时请传入一位数组,或用","连接资源路径
* @param $fromTable     该记录来自哪张表
* @param $resFieldName  表中的资源字段名称
*/
function WSTUseResource($fromType, $dataId, $resPath, $fromTable='', $resFieldName=''){
	if(empty($resPath))return;

	$image['fromType'] = (int)$fromType;
	//根据类型判断所有者
	$image['ownId'] = ((int)$fromType==0)?(int)session('WST_USER.userId'):(int)session('WST_STAFF.staffId');
	$image['dataId'] = (int)$dataId;

	$image['isUse'] = 1;//标记为启用
	if($fromTable!=''){
		$tmp = ['',''];
		if(strpos($fromTable,'-')!==false){
			$tmp = explode('-',$fromTable);
			$fromTable = str_replace('-'.$tmp[1],'',$fromTable);
		}
		$image['fromTable'] = str_replace('_','',$fromTable.$tmp[1]);
	}

	$resPath = is_array($resPath)?$resPath:explode(',',$resPath);//转数组


	//用于与旧资源比较
	$newRes = $resPath;

	// 不为空说明执行修改
	if($resFieldName!=''){
		//要操作的表名  $fromTable;
		// 获取`$fromTable`表的主键
		$tableName = $fromTable;
		$pk = Db::name("$tableName")->getPk();
		// 取出旧图
		$oldResPath = model("$fromTable")->where("$pk",$dataId)->value("$resFieldName"); 
		// 转数组
		$oldResPath = explode(',', $oldResPath);

		// 1.要设置为启用的文件
		$newRes = array_diff($resPath, $oldResPath);
		// 2.要标记为删除的文件
		$oldResPath = array_diff($oldResPath, $resPath);
		//旧资源数组跟新资源数组相同则不需要继续执行
		if($newRes!=$oldResPath)WSTUnuseResource($oldResPath);
	}
	if(!empty($newRes)){
		Db::name('resources')->where([['resPath','in',$newRes]])->update($image);
	}
}

/**
* 编辑器图片记录
* @param $fromType 0：  用户/商家 1：平台管理员
* @param $dataId        来源记录id
* @param $oldDesc       旧商品描述
* @param $newDesc       新商品描述
*/
function WSTEditorImageRocord($fromType, $dataId, $oldDesc, $newDesc){
		// 解义
		$oldDesc = htmlspecialchars_decode($oldDesc);
		$newDesc = htmlspecialchars_decode($newDesc);
		//编辑器里的图片
		$rule = '/src=".*?\/(upload.*?)"/';
	    // 获取旧的src数组
	    preg_match_all($rule,$oldDesc,$images);
	    $oldImgPath = $images[1];
	    preg_match_all($rule,$newDesc,$images); 
	    // 获取新的src数组
	    $imgPath = $images[1];
		// 1.要设置为启用的文件
		$newImage = array_diff($imgPath, $oldImgPath);
		// 2.要标记为删除的文件
		$oldImgPath = array_diff($oldImgPath, $imgPath);

		//旧图数组跟新图数组相同则不需要继续执行
		if($newImage!=$oldImgPath){
			//标记新图启用
			WSTUseResource($fromType, $dataId, $newImage);
			//标记旧图删除
			WSTUnuseResource($oldImgPath);
		}
}


/**
* 标记删除资源文件
*/
function WSTUnuseResource($fromTable, $field = '' , $dataId = 0){
	if($fromTable=='')return;
	$resPath = $fromTable;
	if($field!=''){
		$tableName = $fromTable;
		$pk = Db::name("$tableName")->getPk();
		// 取出旧资源
		$resPath = Db::name("$fromTable")->where("$pk",$dataId)->value("$field");
	}
	if(!empty($resPath)){
		$resPath = is_array($resPath)?$resPath:explode(',',$resPath);//转数组
		Db::name('resources')->where([['resPath','in',$resPath]])->setField('isUse',0);
	}
}
/**
 * 获取系统根目录
 */
function WSTRootPath(){
	return dirname(dirname(__File__));
}
/**
 * 切换图片
 * @param $imgurl 图片路径
 * @param $imgType 图片类型    0:PC版大图   1:PC版缩略图       2:移动版大图    3:移动版缩略图
 * @param $default 图片默认    mallLogo:商城Logo goodsLogo:商品默认 shopLogo:店铺默认 userLogo:会员默认
 * 图片规则  
 * PC版版大图 :201635459344.jpg
 * PC版版缩略图 :201635459344_thumb.jpg
 * 移动版大图 :201635459344_m.jpg
 * 移动版缩略图 :201635459344_m_thumb.jpg
 */
function WSTImg($imgurl,$imgType = 1,$default = ''){
	$m = WSTConf('CONF.wstMobileImgSuffix');
	$imgurl = str_replace($m.'.','.',$imgurl);
	$imgurl = str_replace($m.'_thumb.','.',$imgurl);
	$imgurl = str_replace('_thumb.','.',$imgurl);
	$img = '';
	switch ($imgType){
		case 0:$img =  $imgurl;break;
		case 1:$img =  str_replace('.','_thumb.',$imgurl);break;
		case 2:$img =  str_replace('.',$m.'.',$imgurl);break;
		case 3:$img =  str_replace('.',$m.'_thumb.',$imgurl);break;
	}
    if(WSTConf('CONF.ossService')==""){
        $img = ((file_exists(WSTRootPath()."/".$img))?$img:$imgurl);
        if($default){
            $img = ((file_exists(WSTRootPath()."/".$img) && $img)?$img:WSTConf('CONF.'.$default));
        }
    }
	
	return $img;
}

/**
 * 根据送货城市获取运费
 * @param $cityId 送货城市Id
 * @param $shopId 店铺ID
 * @param $carts 购物车信息
 */
function WSTOrderFreight($shopId,$cityId,$carts=[]){
    $cnt = Db::name("shop_express")->where(["shopId"=>$shopId,"dataFlag"=>1,"isEnable"=>1])->count();
    $freight = 0;
    if($cnt>0){
        $freight = model("common/Carts")->getShopFreight($shopId,$cityId,$carts);
    }
    return ($freight>0)?$freight:0;
}
/**
 * 生成订单号
 */
function WSTOrderNo(){
    $orderId = Db::name('orderids')->insertGetId(['rnd'=>time()]);
	return $orderId.(fmod($orderId,7));
}
/**
 * 高精度数字相加
 * @param $num
 * @param number $i 保留小数位
 */
function WSTBCMoney($num1,$num2,$i=2){
	$num = bcadd($num1, $num2, $i);
	return (float)$num;
}
/**
 * 获取支付方式
 */
function WSTLangPayType($v){
	switch($v){
		case 0:return '货到付款';
		case 1:return '在线支付';
		case 2:return '线下支付';
	}
}
/**
 * 收货方式
 */
function WSTLangDeliverType($v){
	switch ($v) {
		case 1:return "自提";
		case 0:return "送货上门";
		default:return '';
	}
}
/**
 * 订单状态
 */
function WSTLangOrderStatus($v){
	switch($v){
		case -3:return '用户拒收';
		case -2:return '待支付';
		case -1:return '已取消';
		case 0:return '待发货';
		case 1:return '待收货';
		case 2:return '已收货';
	}
}
/**
 * 积分来源
 */
function WSTLangScore($v){
    switch($v){
		case 1:return '商品订单';
		case 2:return '评价订单';
		case 4:return '退款订单';
		case 5:return '积分签到';
		case 6:return '新用户注册';
		case 7:return '推荐用户注册';
		case 10001:return '管理员';
		default:return '其他';
	}
}
/**
 * 资金来源
 */
function WSTLangMoneySrc($v){
    switch($v){
		case 1:return '商品订单';
		case 2:return '订单结算';
		case 3:return '提现申请';
		case 4:return '钱包充值';
		case 5:return '商家入驻缴纳年费';
		case 6:return '供货商入驻缴纳年费';
		case 10001:return '管理员';
		default:return '其他';
	}
}
/**
 * 投诉状态
 */
function WSTLangComplainStatus($v){
    switch($v){
		case 0:return '等待处理';
		case 1:return '等待应诉人应诉';
		case 2:return '应诉人已应诉';
		case 3:return '等待仲裁';
		case 4:return '已仲裁';
	}
}

/**
 * 性别
 */
function WSTLangSex($v){
	switch($v){
		case 0:return '保密';
		case 1:return '男';
		case 2:return '女';
	}
}

/**
 * 品牌申请状态
 */
function WSTBrandApplyStatus($type){
    switch ($type) {
        case -1:return '已拒绝';
        case 0:return '待审核';
        case 1:return '已通过';
    }
}

/**
 * 支付来源
 */
function WSTLangPayFrom($pkey = '',$type = 0){
    $paySrc = cache('WST_PAY_SRC');
    if(!$paySrc){
        $paySrc = Db::name('payments')->order('payOrder asc')->select();
        cache('WST_PAY_SRC',$paySrc,31622400);
    }
    if($pkey=='' && $type == 1)return $paySrc;
    foreach($paySrc as $v){
       if($pkey==$v['payCode'])return $v['payName'];
    }
    return '其他';
}

/**
 * 插件状态
 */
function WSTLangAddonStatus($v){
	switch($v){
		case 0:return '未安装';
		case 1:return '启用';
		case 2:return '禁用';
	}
}

/**
 * 登录来源
 */
function WSTLangLoginSrc($v){
    switch($v){
        case 0:return '电脑端';
        case 1:return '微信端';
        case 2:return '手机端';
        case 3:return 'APP安卓端';
        case 4:return 'APP苹果端';
        case 5:return '小程序端';
    }
}

/**
 * 获取业务数据内容【根据catCode获取】
 */
function WSTDatas($catCode,$id = 0){
	$catId = (int)Db::name('data_cats')->where(['catCode'=>$catCode])->value('catId');
	$data = cache('WST_DATAS');
	if(!$data){
		$rs = Db::name('datas')->where(['dataFlag'=>1])->order('catId asc,dataSort asc,id asc')->select();
		$data = [];
		foreach ($rs as $key =>$v){
			$data[$v['catId']][$v['dataVal']] = $v;
		}
		cache('WST_DATAS',$data,378432000);
	}
	if(isset($data[$catId])){
		if($id==0)return $data[$catId];
		return isset($data[$catId][$id])?$data[$catId][$id]:'';
	}
	return [];
}
/**
 * 检测业务数据内容
 */
function WSTCheckDatas($catCode,$val){
	$data = WSTDatas($catCode);
	foreach ($data as $key => $v) {
		if($v['dataVal']==$val)return true;
	}
	return false;
}

/**
 * 获取业务数据内容【根据catCode获取】
 */
function WSTSubDatas($catCode,$id = 0){
	$catId = (int)Db::name('data_cats')->where(['catCode'=>$catCode])->value('catId');
	$data = cache('WST_SUBDATAS_'.$catId);
	if(!$data){
		$data = Db::name('datas')->where(['subCatId'=>$catId,'dataFlag'=>1])->order('catId asc,dataSort asc,id asc')->select();
		cache('WST_SUBDATAS_'.$catId,$data,378432000);
	}
	return $data;
}

/**
 * 获取消息模板
 */
function WSTMsgTemplates($tplCode){
    $data = cache('WST_MSG_TEMPLATES');
	if(!$data){
		$rs = Db::name('template_msgs')->order('id asc')->select();
		$data = [];
		foreach ($rs as $key =>$v){
			if(in_array($v['tplType'],[3,4]) && (WSTDatas('ADS_TYPE',3)!='' || WSTDatas('ADS_TYPE',4)!='')){
				$ps = Db::name('wx_template_params')->where('parentId',$v['id'])->select();
				$v['params'] = $ps;
			}
			if($v['tplContent']==''){
                $data[$v['tplCode']] = $v;
			}else{
				$v['content'] = htmlspecialchars_decode($v['tplContent']);
				$v['tplContent'] = strip_tags(htmlspecialchars_decode($v['tplContent']));
				$data[$v['tplCode']] = $v;
			}
		}
		cache('WST_MSG_TEMPLATES',$data,378432000);
	}
	return (isset($data[$tplCode]))?$data[$tplCode]:null;
}
/**
 * 发送微信消息
 */
function WSTWxMessage($params){
    $tpl = WSTMsgTemplates($params['CODE']);
	if($tpl && file_exists('wstmart'.DS.'wechat'.DS.'behavior'.DS.'InitWechatMessges.php')){
		Hook::exec(['wstmart\\wechat\\behavior\\InitWechatMessges','run'],$params);
	}
}
/**
 * 批量发送微信消息
 */
function WSTWxBatchMessage($params){
    $tpl = WSTMsgTemplates($params['CODE']);
	if($tpl && file_exists('wstmart'.DS.'wechat'.DS.'behavior'.DS.'InitWechatMessges.php')){
		\think\facade\Hook::exec(['wstmart\\wechat\\behavior\\InitWechatMessges','batchRun'],$params);
	}
}
/**
 * 发送小程序消息
 */
function WSTWeMessage($params){
    $tpl = WSTMsgTemplates($params['CODE']);
	if($tpl && file_exists('wstmart'.DS.'weapp'.DS.'behavior'.DS.'InitWeappMessges.php')){
		Hook::exec(['wstmart\\weapp\\behavior\\InitWeappMessges','run'],$params);
	}
}
/**
 * 截取字符串
 */
function WSTMSubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false){
	$newStr = '';
	if (function_exists ( "mb_substr" )) {
		$newStr = mb_substr ( $str, $start, $length, $charset );
		if ($suffix && (mb_strlen($str,$charset)>$length))$newStr .= "...";
	} elseif (function_exists ( 'iconv_substr' )) {
		$newStr = iconv_substr( $str, $start, $length, $charset );
		if ($suffix && (mb_strlen($str,$charset)>$length))$newStr .= "...";
	}
	if($newStr==''){
	$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all ( $re [$charset], $str, $match );
	$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	if ($suffix)
		$newStr = $slice;
	}
	return $newStr;
}
function WSTScore($score,$users,$type = 5,$len = 0,$total = 1){
	if((int)$score==0 || $users==0)return $type;
	switch($type){
		case 5:return round($score/$total/$users,0);
		case 10:return round($score/$total*2/$users,$len);
		case 100:return round($score/$total*2/$users,$len);
	}
}
function WSTShopEncrypt($shopId){
	return md5(base64_encode(WSTConf('urlSecretKey').date("Y-m-d").$shopId));
}
/**
 * 根据子分类循环获取其父级分类
 */
function WSTGoodsCatPath($catId, $data = []){
    if($catId==0)return $data;
    $data[] = $catId;
	$parentId = Db::name('goods_cats')->where('catId',$catId)->value('parentId');
	if($parentId==0){
		krsort($data);
		return $data;
	}else{
		return WSTGoodsCatPath($parentId, $data);
	}
}
/**
 * 提供原生分页处理
 */
function WSTPager($total,$rs,$page,$size = 0){
	$pageSize = ($size>0)?$size:config('paginate.list_rows');
	$totalPage = ($total%$pageSize==0)?($total/$pageSize):(intval($total/$pageSize)+1);
	return ['total'=>$total,'per_page'=>$pageSize,'current_page'=>$page,'last_page'=>$totalPage,'data'=>$rs];
}


/**
* 编辑器上传图片
* {"code":"000","message":"上传成功.","count":null,"page":null,"pagesize":null,"extra":null,"data":{"url":"upload/erdfderert043.png"}}
*/
function WSTEditUpload($fromType){
	$root = str_replace('/index.php','',request()->root());
    //PHP上传失败
    if (!empty($_FILES['imgFile']['error'])) {
        switch($_FILES['imgFile']['error']){
            case '1':
                $error = '超过php.ini允许的大小。';
                break;
            case '2':
                $error = '超过表单允许的大小。';
                break;
            case '3':
                $error = '图片只有部分被上传。';
                break;
            case '4':
                $error = '请选择图片。';
                break;
            case '6':
                $error = '找不到临时目录。';
                break;
            case '7':
                $error = '写文件到硬盘出错。';
                break;
            case '8':
                $error = 'File upload stopped by extension。';
                break;
            case '999':
            default:
                $error = '未知错误。';
        }
        return ['code'=>"001",'message'=>$error];
    }

    $fileKey = key($_FILES);
	$dir = 'image'; // 编辑器上传图片目录
	$dirs = WSTConf("CONF.wstUploads");
   	if(!in_array($dir, $dirs)){
   		return json_encode(['code'=>"001",'message'=>'非法文件目录！']);
   	}
   	// 上传文件
    $file = request()->file($fileKey);
    if($file===null){
    	return json_encode(["code"=>"001","message"=>'上传文件不存在或超过服务器限制']);
    }
    $rule = [
	    'type'=>'image/png,image/gif,image/jpeg,image/x-ms-bmp',
	    'ext'=>'jpg,jpeg,gif,png,bmp',
	    'size'=>'20971520'
	];
	$mediaType = 0;
	if(input('dir')=='media'){
		// 上传类型为视频或音频时，不限制大小
		$rule = array_diff_key($rule, ['size'=>'']);
		$videoExt = "3gp,mp4,rmvb,mov,avi,m4v";
		$rule['ext'] .= $videoExt;
		$typeArr = explode(',', $videoExt);
		foreach($typeArr as $v){
			$rule['type'] .= ",video/$v";
		}
		// 上传的资源类型为视频
		$mediaType = 1;
	}
	$info = $file->validate($rule)->rule('uniqid')->move(Env::get('root_path').'/upload/'.$dir."/".date('Y-m'));
	$mSrc = null;
    if($info){
    	$filePath = $info->getPathname();
    	$filePath = str_replace(Env::get('root_path'),'',$filePath);
    	$filePath = str_replace('\\','/',$filePath);
    	$name = $info->getFilename();
    	$filePath = str_replace($name,'',$filePath);

        $extension = $info->getExtension();
        if(in_array($extension,['jpg','jpeg','gif','png','bmp'])){
            //原图路径
            $imageSrc = trim($filePath.$name,'/');
            //打开原图
            $image = \image\Image::open($imageSrc);

            //手机版原图宽高
            $mWidth = min($image->width(),(int)input('mWidth',700));
            $mHeight = min($image->height(),((int)input('mHeight',700)>$image->height())?$image->height():$image->height()/2);
            /****************************** 生成移动大图 *********************************/

            //是否需要生成移动版的大图
            $suffix = WSTConf("CONF.wstMobileImgSuffix");
            if(!empty($suffix)){
                $image = \image\Image::open($imageSrc);
                $mSrc = str_replace('.',"$suffix.",$imageSrc);
                $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
            }

            /***************************** 添加水印 ***********************************/
            if((int)WSTConf('CONF.watermarkPosition')!==0){
                //取出水印配置
                $wmWord = WSTConf('CONF.watermarkWord');//文字
                $wmFile = trim(WSTConf('CONF.watermarkFile'),'/');//水印文件
                //判断水印文件是否存在
                if(!file_exists(WSTRootPath()."/".$wmFile))$wmFile = '';
                $wmPosition = (int)WSTConf('CONF.watermarkPosition');//水印位置
                $wmSize = ((int)WSTConf('CONF.watermarkSize')!=0)?WSTConf('CONF.watermarkSize'):'20';//大小
                $wmColor = (WSTConf('CONF.watermarkColor')!='')?WSTConf('CONF.watermarkColor'):'#000000';//颜色必须是16进制的
                $wmOpacity = ((int)WSTConf('CONF.watermarkOpacity')!=0)?WSTConf('CONF.watermarkOpacity'):'100';//水印透明度
                //是否有自定义字体文件
                $customTtf = Env::get('root_path').WSTConf('CONF.watermarkTtf');
                $ttf = is_file($customTtf)?$customTtf:Env::get('extend_path').'verify/verify/ttfs/3.ttf';
                $image = \image\Image::open($imageSrc);
                if(!empty($wmWord)){//当设置了文字水印 就一定会执行文字水印,不管是否设置了文件水印
                    // 文字偏移量
                    $offset = WSTConf('CONF.watermarkOffset');
                    if($offset!=''){
                        $offset = explode(',',str_replace('，', ',',$offset));
                        $offset = array_slice($offset,0,2);
                        $offset = array_map(function($val){return (int)$val;},$offset);
                        if(count($offset)<2)array_push($offset, 0);
                    }
                    //执行文字水印
                    $image->text($wmWord, $ttf, $wmSize, $wmColor, $wmPosition,$offset)->save($imageSrc);

                    //如果有生成手机版原图
                    if(!empty($mSrc)){
                        $image = \image\Image::open($imageSrc);
                        $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                    }
                }elseif(!empty($wmFile)){//设置了文件水印,并且没有设置文字水印
                    //执行图片水印
                    $image->water($wmFile, $wmPosition, $wmOpacity)->save($imageSrc);
                    //如果有生成手机版原图
                    if($mSrc!==null){
                        $image = \image\Image::open($imageSrc);
                        $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                    }
                }
            }
        }

        $rdata = ['status'=>1,'name'=>$name,'savePath'=>ltrim($filePath,'/')];
        $info = null;
    	hook('afterUploadPic',['data'=>&$rdata]);
    	//图片记录
    	WSTRecordResources($imageSrc, (int)$fromType, $mediaType);
    	return json_encode(["code"=>"000","message"=>"", 'data'=>['url' => WSTConf('CONF.resourcePath').'/'.$imageSrc]]);
	}
	return json_encode(["code"=>"001","message"=>$file->getError()]);
}
/**
 * 转义单引号
 */
function WSTHtmlspecialchars($v){
	return htmlspecialchars($v,ENT_QUOTES);
}

/**
* 发送商城消息
* @param int 	$to 接受者d
* @param string $content 内容
* @param array  $msgJson 存放json数据
*/
function WSTSendMsg($to,$content,$msgJson=[],$msgType = 1){
	$message = [];
	$message['msgType'] = $msgType;
	$message['sendUserId'] = 1;
	$message['createTime'] = date('Y-m-d H:i:s');
	$message['msgStatus'] = 0;
	$message['dataFlag'] = 1;

	$message['receiveUserId'] = $to;
	$message['msgContent'] = $content;
	$message['msgJson'] = json_encode($msgJson);
	// 推送消息
	hook('pushNoticeToApp', ["type"=>"pushToSingle", "userId"=>$to, "title"=>"新消息", "content"=>$content]);
	Db::name('messages')->insert($message);

}

/**
 * 获取分类的佣金
 */
function WSTGoodsCommissionRate($goodsCatId){
	$cats = Db::name('goods_cats')->where('catId',$goodsCatId)->field('parentId,commissionRate')->find();
	if(empty($cats)){
		return 0;
	}else{
		if((float)$cats['commissionRate']>=0)return (float)$cats['commissionRate'];
		return WSTGoodsCommissionRate($cats['parentId']);
	}
}
/**
 * 将传过来的字符串格式化为数值字符串
 * @param string $split 要格式的字符串
 * @param string $str 字符串中的分隔符号
 * @param boolean $isJoin 是否连成字符串返回 
 */
function WSTFormatIn($split,$str,$isJoin = true){
	if($str=='')return $isJoin?'':[];
	$strdatas = explode($split,$str);
	$data = array();
	for($i=0;$i<count($strdatas);$i++){
		$data[] = (int)$strdatas[$i];
	}
	$data = array_unique($data);
	if($isJoin)return implode($split,$data);
	return $data;
	
}
/**
 * 生成随机字符串
 * @param integer $len 要生成的字符串长度
 */
function WSTRandStr($len = 6){
    $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $tmp = '';
    for($i=0;$i<$len;$i++){
        $tmp.=$str[rand(0,35)];
    }
    return $tmp;
}
/**
 * 金额兑积分
 */
function WSTMoneyGiftScore($money){
    $moneyToScore = (float)WSTConf('CONF.moneyToScore');
    return intval($money*$moneyToScore);
}
/**
 * 积分兑金额
 * $isBack=true则$score实际上传入金额，通过金额反推需要兑换的积分
 */
function WSTScoreToMoney($score,$isBack = false){
    $scoreToMoney = (int)WSTConf('CONF.scoreToMoney');
    if($scoreToMoney<=0)return 0;
    if($isBack){
        return intval(strval($score*$scoreToMoney));
    }else{
    	return round($score/$scoreToMoney,2);
    }
}
/**
 * 头像处理
 */
function WSTUserPhoto($userPhoto='', $domain=false){
	$prefix = $domain?WSTConf('CONF.resourceDomain'):WSTConf('CONF.resourcePath');
	if(substr($userPhoto,0,4)!='http' && $userPhoto){
		$userPhoto  = $prefix.'/'.$userPhoto;
	}else if(!$userPhoto){
		$userPhoto  = $prefix.'/'.WSTConf('CONF.userLogo');
	}
	return $userPhoto;
}
/**
 * 清除管理员后台插件功能缓存
 */
function WSTClearHookCache(){
	WSTConf('listenUrl',null);
	$STAFF = session('WST_STAFF');
	if(!empty($STAFF)){
	    //获取角色权限
	 	$STAFF['privileges'] = Db::name('privileges')->where(['dataFlag'=>1])->column('privilegeCode');
	 	$STAFF['menuIds'] = Db::name('menus')->where('dataFlag',1)->column('menuId');
	 	session('WST_STAFF',$STAFF);
	}
	WSTConf('protectedUrl',null);
	cache('WST_HOME_MENUS',null);
	cache('WST_PRO_MENUS',null);
	cache('WST_MOBILE_BTN',null);
	cache('hooks',null);
	cache('WST_ADDONS',null);
	WSTConf('WST_ADDONS',null);
}
/**
 * 获取移动端首页按钮
 */
function WSTMobileBtns($src){
    $data = cache('WST_MOBILE_BTN');
    if(!$data){
        $rs = Db::name('mobile_btns')->order('btnSort asc')->select();
        $data = [];
        foreach ($rs as $key => $v) {
        	$data[$v['btnSrc']][] = $v;
        }
        cache('WST_MOBILE_BTN',$data,31536000);
    }
    return (isset($data[$src])?$data[$src]:[]);
}

/**
 * 获取星期几
 */
function WSTgetWeek($date){
	//强制转换日期格式
	$date_str=date('Y-m-d',strtotime($date));
	$number_wk=date("w",strtotime($date));
	$weekArr=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
	return $weekArr[$number_wk];
}
/**
 * 获取路由规则
 */
function WSTRoute(){
	$data = cache('WST_ROUTES');
	if(!$data){
		$routes = Route::getName();
		$data = [];
		foreach ($routes as $key => $v) {
			if($key=='addon/:route')continue;
			$data[$key] = $v[0][0];
		}
		cache('WST_ROUTES',$data,31536000);
	}
	return json_encode($data);
}

/**
 * 获取域名
 */
function WSTDomain(){
	$url  = request()->root(true);
	$data = explode("/index.php",$url);
	return $data[0];
}



/**
 * URL 64位加密处理
 * @param string $data 字符串内容
 * @param boolean $isEncode true:编码  false:解码
 */
function WSTBase64url($data,$isEncode = true) { 
  return ($isEncode)?rtrim(strtr(base64_encode($data), '+/', '-_'), '='):base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
}
/**
 * URL 64位加密处理[编码]
 * @param string $data 字符串内容
 */
function WSTBase64urlEncode($data){
	$secretKey = WSTConf('CONF.urlSecretKey');
	$base64 = new \org\Base64();
 	$key = WSTBase64url($base64->encrypt($data, $secretKey),true);
 	return $key;
}

/**
 * URL 64位加密处理[解码]
 * @param string $data 字符串内容
 */
function WSTBase64urlDecode($data){
	$secretKey = WSTConf('CONF.urlSecretKey');
	$key = WSTBase64url($data,false);
	$base64 = new \org\Base64();
  	$key = $base64->decrypt($key,$secretKey);
  	return $key;
}


/**
 * 将空内容设置为特定内容
 * @param string $v 要处理的字符串
 * @param string $defaultValue 若被处理的字符串的符合函数内的条件，则该值返回
 */
function WSTBlank($v,$defaultValue = ''){
	if($v=='')return $defaultValue;
	if($v=='0000-00-00')return $defaultValue;
	if($v=='0000-00-00 00:00:00')return $defaultValue;
}

/**
 * 判断访问端来源
 */
function WSTVisitModule(){
    $request = request();
    if($request->isMobile()){
        return (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)?'wechat':'mobile';
    }
    return 'home';
}
/**
* 获取图片颜色
* @imgUrl 图片地址
*/
function WSTImgColor($imgUrl){
    $imageInfo = getimagesize($imgUrl);
    //图片类型
    $imgType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
    //对应函数
    $imageFun = 'imagecreatefrom' . ($imgType == 'jpg' ? 'jpeg' : $imgType);
    $im = $imageFun($imgUrl);
    $rgb  =  imagecolorat ( $im ,  10 ,  15 );
    $arr = array();
	$arr['r']  = ( $rgb  >>  16 ) &  0xFF ;
	$arr['g']  = ( $rgb  >>  8 ) &  0xFF ;
	$arr['b']  =  $rgb  &  0xFF ;
    return implode(',',$arr);
}

/**
 * 获取用户等级
 */
function WSTUserRank($userScore){
	$data = cache('WST_USER_RANK');
	if(!$data){
		$data =  Db::name('user_ranks')->where('dataFlag',1)->order('startScore asc,rankId desc')->select();
	    cache('WST_USER_RANK',$data,2592000);
	}
	if(!$data)$data = [];
	foreach ($data as $key => $v) {
		if($userScore>=$v['startScore'] && $userScore<$v['endScore'])return $v;
	}
	return ['rankName'=>'','rankId'=>0,'userrankImg'=>''];

}

/**
 * 获取购物车数量
 */
function WSTCartNum(){
	$userId = session('WST_USER.userId');
	$cartNum = Db::name('carts')->where(['userId'=>$userId])->field('cartId')->select();
	$count = count($cartNum);
	return $count;
}
/**
* 增加文章访问数
*/
function WSTArticleVisitorNum($id){
	Db::name('articles')->where(['articleId'=>$id])->setInc('visitorNum',1);
}

/**
 * 保持数值为大于0的数值
 */
function WSTPositiveNum($num){
   return ($num>0)?round($num,2):0;
}

/**
 * 将字符串转换为时间戳，解决部分服务器时间不能超过2038的问题
 */
function WSTStrToTime($str){
   if(strtotime('2099-09-09 23:59:59')){
       return strtotime($str);
   }else{
   	   $date = new DateTime($str);
       return $date->format('U');
   }
}

/**
 * 计算剩余时间
 */
function WSTTimeToStr($second){
	$day = floor($second/(3600*24));
	$second = $second%(3600*24);//除去整天之后剩余的时间
	$hour = floor($second/3600);
	$second = $second%3600;//除去整小时之后剩余的时间
	$minute = floor($second/60);
	$second = $second%60;//除去整分钟之后剩余的时间
	//返回字符串
	return (($day>0)?($day.'天'):"").($hour<10?"0".$hour:$hour).':'.($minute<10?"0".$minute:$minute).':'.($second<10?"0".$second:$second);
}

/**
 * 适应mmgrid的表格返回结构
 */
function WSTGrid($page){
	if(!is_array($page))$page = $page->toArray();
	$rs = ['status'=>1,'msg'=>'','items'=>$page['data'],'totalCount'=>$page['total']];
	return $rs;
}

/**
 * RSA解密
 */
function WSTRSA($hex_encrypt_data){
	$hex_encrypt_data = trim($hex_encrypt_data);
	$isCrypt = WSTConf('CONF.isCryptPwd');
	if($isCrypt==0)return WSTReturn('success',1,$hex_encrypt_data);
	
	$private_key = WSTConf('CONF.pwdPrivateKey');
	if($private_key=='')return WSTReturn('fail');
	try{
		$encrypt_data = pack("H*", $hex_encrypt_data); //对十六进制数据进行转换
		openssl_private_decrypt($encrypt_data, $decrypt_data, $private_key); //解密数据
		return WSTReturn('success',1,$decrypt_data);
	}catch(\Exception $e){
        return WSTReturn('fail');
	}
}
/**
 * 获取订单来源模块
 */
function WSTOrderModule($orderCode = ''){
	$addonMaps = model("common/addons")->getAddonsMaps();
	if($orderCode!=''){
        return array_key_exists($orderCode,$addonMaps)?$addonMaps[$orderCode]:"普通订单";
	}else{
        $data = [];
        $data[] = ['name'=>'order','title'=>'普通订单'];
        foreach ($addonMaps as $key => $v) {
        	$data[] = ['name'=>strtolower($key),'title'=>$v];
        }
        return $data;
	}
}

/**
 * 循环删除指定目录下的文件及文件夹
 * @param string $dirpath 文件夹路径
 */
function WSTDelDir($dirpath){
	$dh=opendir($dirpath);
	while (($file=readdir($dh))!==false) {
		if($file!="." && $file!="..") {
		    $fullpath=$dirpath."/".$file;
		    if(!is_dir($fullpath)) {
		        unlink($fullpath);
		    } else {
		        WSTDelDir($fullpath);
		        @rmdir($fullpath);
		    }
	    }
	}	 
	closedir($dh);
    $isEmpty = true;
	$dh=opendir($dirpath);
	while (($file=readdir($dh))!== false) {
		if($file!="." && $file!="..") {
			$isEmpty = false;
			break;
		}
	}
	return $isEmpty;
}
/**
 * 循环删除指定目录下的文件及文件夹
 * @param string $dirpath 文件夹路径
 * @param string $currDir 当前文件夹名
 */
function WSTDelOtherDir($dirpath,$currDir){
	$arr = scandir($dirpath);
	foreach ($arr as $value) {
        // 若名字为.或者..的直接跳过
        if($value == '.' || $value == '..'){
            continue;
        }
        if($currDir != $value){
        	$path = $dirpath."/".$value;
        	WSTDelDir($path);
        	if(count(scandir($path)) == 2){
                rmdir($path);
            }
        }
 	}
}

/**
 * 清除整个所有缓存
 * 注意：此函数非迫不得己不要调用。能删除指定缓存的就尽量删除指定缓存。尽量只在后台管理员才做时调用，前台用户操作就不要调用了
 */
function WSTClearAllCache(){
	Cache::clear();
}
/**
 * 获取商家订单菜单
 */
function WSTShopOrderMenus(){
	$wst_user = session('WST_USER');
	if(isset($wst_user['shopId'])){
		$shop = model('common/Shops')->getFieldsById($wst_user['shopId'],'shopStatus');
		if(empty($shop) || $shop['shopStatus']==-1){
			return [];
		}
	}
	$orderMenus = array("waitPay"=>"shop/orders/waituserPay",
				"waitDeliver"=>"shop/orders/waitdelivery",
				"waitReceive"=>"shop/orders/delivered",
				"abnormal"=>"shop/orders/failure",
				"finish"=>"shop/orders/finished");
	if(!empty($wst_user)){
		$roleId = isset($wst_user["roleId"])?(int)$wst_user["roleId"]:0;
		if($roleId>0){
			$shopMenuUrls = model("common/HomeMenus")->getShopMenuUrls();
			foreach ($orderMenus as $key => $menuUrl) {
				if(!in_array($menuUrl,$shopMenuUrls)){
					unset($orderMenus[$key]);
				}
			}
		}
	}
	if(count($orderMenus)==5){
		$orderMenus["all"] = "";
	}
	return $orderMenus;
}
/**
 * IP定位
 */
function WSTIpLocation(){
	$key = WSTConf('CONF.mapKey');
	$ip = request()->ip();
	$url = "http://apis.map.qq.com/ws/location/v1/ip?ip=".$ip."&key=".$key;
	$curl = curl_init();  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);  
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_URL, $url);  
    $res = curl_exec($curl);  
    curl_close($curl);  
	$res = json_decode($res,true);
	$data['latitude'] = 0;
	$data['longitude'] = 0;
	if($res['status']==0){
		$data['latitude'] = $res['result']['location']['lat'];
		$data['longitude'] = $res['result']['location']['lng'];
	}
    return $data;  
}
/**
 * 分词
 */
function WSTAnalysis($str){
	$str = trim($str);
	$do_fork = true;
    $do_unit = true;//新词识别
    $do_multi = true;//多元切分
    $do_prop = false;//词性标注
    $pri_dict = false;//是否预载全部词条
    $pa = new \phpanalysis\phpanalysis('utf-8', 'utf-8', $pri_dict);
    //载入词典
    $pa->LoadDict();  
    //执行分词
    $pa->SetSource($str);
    $pa->differMax = $do_multi;
    $pa->unitWord = $do_unit;
    $pa->StartAnalysis( $do_fork );
    $str = $pa->GetFinallyResult(' ', $do_prop);
    $str = explode(' ',$str);
    $rs = array();
    foreach ($str as $key =>$v){
    	if(trim($v)=='' || trim($v)==')' || trim($v)=='(')continue;
    	$rs[] = $v;
    }
    return $rs;
}
/**
 * 获取搜索关键词
 * @param integer $goodsId 商品ID
 */
function WSTGroupGoodsSearchKey($goodsId){
	$goods = Db::name('goods')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->find();
	$searchKeys = [];
	$searchKeys[] = $goods['goodsName'];
    if($goods['isSpec']==1){
		//获取规格值
		$specs = Db::name('spec_cats')->alias('gc')->join('__SPEC_ITEMS__ sit','gc.catId=sit.catId','inner')
				    ->where(['sit.goodsId'=>$goodsId,'gc.isShow'=>1,'sit.dataFlag'=>1])
				    ->field('sit.itemName')
				    ->select();                     
		foreach ($specs as $key =>$v){
			$searchKeys[] = $v['itemName'];
		}
	}
	//获取商品属性
	$attrs = Db::name('attributes')->alias('a')->join('goods_attributes ga','a.attrId=ga.attrId','inner')
			         ->where(['a.isShow'=>1,'dataFlag'=>1,'goodsId'=>$goodsId])->field('ga.attrVal')
			         ->select();
	if(count($attrs)>0){
		foreach ($attrs as $key => $v) {
			$searchKeys[] = $v['attrVal'];
		}
	}
    return $searchKeys;
}

/**
 * 页面转换
 */
function WSTSwitchs($omodule = '',$ocontroller = '',$oaction = '',$oaddon = ''){
	$request = request();
	$module = ($omodule!='')?$omodule:strtolower($request->module());
	$controller = ($ocontroller!='')?$ocontroller:strtolower($request->controller());
	$action = ($oaction!='')?$oaction:strtolower($request->action());
	$currURL =  $module."/".$controller."/".$action;//当前页面
	$allowURL = ['weixinpays',
				 'unionpays',
				 'alipays',
				 'weixinpaysmo',
				 'weixinpayswx',
				 'cron'];	
	if($action=='download')return;	 
	//遇到放行的url则不转换
    if(in_array($controller,$allowURL))return;
    $data = cache('WST_SWITCHS');
    if(!$data){
    	$rs  = Db::name('switchs')->select();
    	$data = [];
        foreach ($rs as $key => $v) {
        	$data[strtolower($v['homeURL'])] = ['home'=>$v['homeURL'],'mobile'=>$v['mobileURL'],'wechat'=>$v['wechatURL']];
        	$data[strtolower($v['mobileURL'])] = ['home'=>$v['homeURL'],'mobile'=>$v['mobileURL'],'wechat'=>$v['wechatURL']];
        	$data[strtolower($v['wechatURL'])] = ['home'=>$v['homeURL'],'mobile'=>$v['mobileURL'],'wechat'=>$v['wechatURL']];
        }
    	cache('WST_SWITCHS',$data,2592000);
    }
    $isPC = !$request->isMobile();
	$isMobile = $request->isMobile();
	$isWeChat = (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);
    $hasPC = (WSTDatas('ADS_TYPE',1)!='')?true:false;
	$hasMobile = (WSTDatas('ADS_TYPE',3)!='')?true:false;
	$hasWechat = (WSTConf('CONF.wxenabled')==1)?true:false;
	$dispathURL = '';//应转发的页面
	$dispathModule = config('app.default_module');//应进入的端
    if(!empty($data) && isset($data[$currURL])){
	    $dispathArrs = $data[$currURL];//目标页面数组
		//按优先级依次判断
		if($isWeChat && $hasWechat)$dispathURL = $dispathArrs['wechat'];
		if($dispathURL=='' && $isMobile && $hasMobile)$dispathURL = $dispathArrs['mobile'];
		if($dispathURL=='' && $isPC && $hasPC)$dispathURL = $dispathArrs['home'];
		if($dispathURL=='')$dispathURL = $dispathArrs[$dispathModule];
		//找到对应的网址，进行跳转
		if($currURL!=$dispathURL){
			if($oaddon=='addon'){
				$dispath = explode('/',$dispathURL);
				$data = input();
				WSTUnset($data,'module,action,method');
                header("Location:".addon_url($dispath[0].'://'.$dispath[1].'/'.$dispath[2],$data));
			}else{
			    header("Location:".url($dispathURL,input()));
			}
		    exit();
		}
	}
	//找不到对应的网址，但是访问端也不匹配的话，就强制跳转到相应端的主页
	if($isWeChat && $hasWechat)$dispathModule = 'wechat';
	if($dispathModule=='home' && $isMobile && $hasMobile)$dispathModule = 'mobile';
	if($oaddon=='' && $module!=$dispathModule){
       header("Location:".url($dispathModule."/index/index"));
	   exit();
	}
}
/**
 * 获取https设置情况
 */
function WSTProtocol(){
	return request()->isSsl()?'https://':'http://';
}

/**
 * 获取毫秒级别的时间戳
 */
function WSTGetMillisecond(){
	//获取毫秒的时间戳
	$time = explode ( " ", microtime () );
	$time = $time[1] . ($time[0] * 1000);
	$time2 = explode( ".", $time );
	$time = $time2[0];
	return $time;
}

/**
 * 检测文件是否存在
 */
function WSTCheckResourceFile($url){
    if(WSTconf('CONF.ossService')==''){
        return file_exists(WSTRootPath()."/".$url)?true:false;
    }else{
        return @file_get_contents(WSTconf('CONF.resourcePath').'/'.$url,0,null,0,1)?true:false;
    }
}

/**
 * 获取广告信息
 */
function WSTAds($code,$num,$cache){
	$rs = model("common/Tags")->listAds($code,$num,$cache);
	if($num==1){
         return empty($rs)?[]:$rs[0];
	}else{
         return $rs;
	}
}

/**
 * 获取指定父级的商家店铺分类
 */
function WSTShopCats($parentId){
	$shopId = (int)session('WST_USER.shopId');
	$dbo = Db::table('__SHOP_CATS__')->where(['dataFlag'=>1, 'isShow' => 1,'parentId'=>$parentId,'shopId'=>$shopId]);
	return $dbo->field("catName,catId")->order('catSort asc')->select();
}
/**
 * 获取商城搜索关键字
 */
function WSTSearchKeys(){
	$keys = WSTConf("CONF.hotWordsSearch");
	if($keys!='')$keys = explode(',',$keys);
	return $keys;
}

/**
 * 根据指定的商品分类获取其路径
 */
function WSTPathGoodsCat($catId,$data = array()){
	$rs = Db::table('__GOODS_CATS__')->where(['isShow'=>1,'dataFlag'=>1,'catId'=>$catId])->field("parentId,catName,catId")->find();
	$data[] = $rs;
	if($rs['parentId']==0){;
		krsort($data);
		return $data;
	}else{
		return WSTPathGoodsCat($rs['parentId'],$data);
	}
}


/**
 * 加载系统访问路径
 */
function WSTVisitPrivilege(){
    $listenUrl = cache('WST_LISTEN_URL');
    if(!$listenUrl){
        $list = model('admin/Privileges')->getAllPrivileges();
        $listenUrl = [];
        foreach ($list as $v){
			$v['privilegeUrl'] = trim($v['privilegeUrl']);
            if($v['privilegeUrl']=='')continue;
            $listenUrl[strtolower($v['privilegeUrl'])][$v['privilegeCode']] = ['code'=>$v['privilegeCode'],
                'url'=>strtolower($v['privilegeUrl']),
                'name'=>$v['privilegeName'],
                'isParent'=>true,
                'menuId'=>$v['menuId']
            ];
            if(strpos($v['otherPrivilegeUrl'],'/')!==false){
                $t = explode(',',$v['otherPrivilegeUrl']);
                foreach ($t as $vv){
					$vv = trim($vv);
                    if(strpos($vv,'/')!==false){
                        $listenUrl[strtolower($vv)][$v['privilegeCode']] = ['code'=>$v['privilegeCode'],
                            'url'=>strtolower($vv),
                            'name'=>$v['privilegeName'],
                            'isParent'=>false,
                            'menuId'=>$v['menuId']
                        ];
                    }
                }
            }
        }
        cache('WST_LISTEN_URL',$listenUrl);
    }
    return $listenUrl;
}
/**
 * 查询网站帮助
 * @param $pnum 父级记录数
 * @param $cnum 子记录数
 */
function WSTHelps($pnum = 5,$cnum = 5){
    $cats = Db::table('__ARTICLE_CATS__')->where(['catType'=>1, 'dataFlag'=>1, 'isShow' => 1, 'parentId'=>7])
        ->field("catName,catId")->order('catSort asc')->limit($pnum)->select();
    if(!empty($cats)){
        foreach($cats as $key =>$v){
            $cats[$key]['articlecats'] = Db::table('__ARTICLES__')->where(['dataFlag'=>1,'isShow' => 1,'catId'=>$v['catId']])
                ->field("articleId, catId, articleTitle")->order('catSort asc,createTime asc')->limit($cnum)->select();
        }
    }
    return $cats;
}
/**
 * 获取导航菜单
 */
function WSTNavigations($navType){
    $data = cache('WST_NAVS');
    if(!$data){
        $rs = Db::table('__NAVS__')->where([['isShow','=',1],['navType','=',$navType]])->order('navSort asc')->select();
        foreach ($rs as $key => $v){
            if(stripos($v['navUrl'],'https://')===false &&  stripos($v['navUrl'],'http://')===false){
                $rs[$key]['navUrl'] = str_replace('/index.php','',Request::root())."/".$v['navUrl'];
            }
        }
        cache('WST_NAVS',$data);
        return $rs;
    }
    return $data;
}

/**
 * 获取首页左侧分类、推荐品牌和广告
 */
function WSTSideCategorys(){
    $data = cache('WST_SIDE_CATS');
    if(!$data){
        $cats1 = Db::table('__GOODS_CATS__')->where([['dataFlag','=',1], ['isShow','=',1],['parentId','=',0]])->field("catName,catId,catImg")->order('catSort asc')->select();
        if(count($cats1)>0){
            $ids1 = [];$ids2 = [];$cats2 = [];$cats3 = [];$mcats3 = [];$mcats2 = [];
            foreach ($cats1 as $key =>$v){
                $ids1[] = $v['catId'];
            }
            $tmp2 = Db::table('__GOODS_CATS__')->where([['dataFlag','=',1], ['isShow','=',1],['parentId','in',$ids1]])->field("catName,catId,parentId,catImg")->order('catSort asc')->select();
            if(count($tmp2)>0){
                foreach ($tmp2 as $key =>$v){
                    $ids2[] = $v['catId'];
                }
                $tmp3 = Db::table('__GOODS_CATS__')->where([['dataFlag','=',1], ['isShow','=',1],['parentId','in',$ids2]])->field("catName,catId,parentId,catImg")->order('catSort asc')->select();
                if(count($tmp3)>0){
                    //组装第三级
                    foreach ($tmp3 as $key =>$v){
                        $mcats3[$v['parentId']][] = $v;
                    }
                }
                //组装第二级
                foreach ($tmp2 as $key =>$v){
                    if(isset($mcats3[$v['catId']]))$v['list'] = $mcats3[$v['catId']];
                    $mcats2[$v['parentId']][] = $v;
                }
                //组装第一级
                foreach ($cats1 as $key =>$v){
                    if(isset($mcats2[$v['catId']]))$cats1[$key]['list'] = $mcats2[$v['catId']];
                }
            }
            unset($ids1,$ids2,$cats2,$cats3,$mcats3,$mcats2);
        }
        cache('WST_SIDE_CATS',$cats1);
        return $cats1;
    }
    return $data;
}
/**
 * 获取前台菜单
 */
function WSTHomeMenus($menuType){
    $m1 = array();
    $m1 = model('common/HomeMenus')->getMenus();
    $menuId1 = (int)input("get.homeMenuId");
    $menus = [];
    $menus['menus'] = $m1[$menuType];
    //判断menuId1是否有效
    if($menuId1==0){
        $menuId1 = (int)session('WST_MENID'.$menuType);
    }else{
        session('WST_MENID'.$menuType,$menuId1);
    }
    //检测第一级菜单是否有效
    $tmpMenuId1 = 0;
    $isFind = false;
    foreach ($menus['menus'] as $key => $v){
        if($tmpMenuId1==0)$tmpMenuId1 = $key;
        if($key==$menuId1)$isFind = true;
    }
    if($isFind){
        $menus['menuId1'] = $menuId1;
    }else{
        $menus['menuId1'] = $tmpMenuId1;
    }
    $menus['menuId3'] = session('WST_MENUID3');
    return $menus;
}

/**
 * 判断商家访问权限
 */
function WSTShopGrant($url){
    $SHOP = session('WST_USER');
    if($SHOP['userType']!=1)return false;
    if($SHOP['roleId']==0)return true;
    $privilegeUrl = $SHOP['privilegeUrls'];
    $hasPrivilege = false;
    if($privilegeUrl){
    	$url = strtolower($url);
    	$privilegeUrl = json_decode($privilegeUrl);
    	foreach ($privilegeUrl as $key => $rv) {
    		foreach ($rv as $rkey => $vv) {
    		    if(in_array($url,$vv->urls))$hasPrivilege = true;
    	    }
    	}
    }
    return $hasPrivilege;
}

/**
 * 处理商家结算信息提示
 */
function WSTShopMessageBox(){
	$USER = session('WST_USER');
	$msg = [];
	if($USER['shopMoney']<0){
		$msg[] = '您的账户欠费¥'.$USER['shopMoney'].'，请及时充值。';
	}
	if(($USER['noSettledOrderFee']+$USER['paymentMoney'])<0 && (($USER['shopMoney']+$USER['noSettledOrderFee']+$USER['paymentMoney'])<0)){
		$msg[] = '您的账户余额【¥'.$USER['shopMoney'].'】不足以缴纳订单佣金【¥'.(-1*($USER['noSettledOrderFee']+$USER['paymentMoney'])).'】，请及时充值。';
	}
    if((strtotime($USER['expireDate'])-strtotime(date('Y-m-d')))<2592000){
        $msg[] = '您的店铺即将到期，请及时续费。';
    }
	return implode('||',$msg);
}

/**
 * 微信配置
 */
function WSTWechat(){
	$wechat = new \wechat\WSTWechat(WSTConf('CONF.wxAppId'),WSTConf('CONF.wxAppKey'));
	return $wechat;
}


function WSTBindWeixin($type=1){
	$USER = session('WST_USER');
	$we = WSTWechat();
	if($USER['userId']=='' || $USER['wxOpenId']==''){
		$wdata = $we->getUserInfo(input('param.code'));//获取openid和access_token
		$userinfo = session('WST_WX_USERINFO');
		if(empty($userinfo['openid'])){
			$userinfo = $we->UserInfo($wdata);
			session('WST_WX_USERINFO',$userinfo);
		}
		WSTSigninfo($userinfo,$USER);
		$users = model("wechat/users");
		if($userinfo['openid']!=''){
			session('WST_WX_OPENID',$userinfo['openid']);
			$rs = Db::name('users')->where(['wxOpenId'=>$userinfo['openid'],'dataFlag'=>1])->field('wxOpenId,wxUnionId')->select();
			if(count($rs)==0 && session('WST_WX_OPENID')!=''){
				if($type==1){
					header("location:".url('wechat/users/login'));
					exit;
				}
			}else{
				$users->accordLogin();
				$url = session('WST_WX_WlADDRESS');
				if($url){
					header("location:".$url);
					exit;
				}
			}
		}
	}
	WSTSigninfo(0,$USER);
}
//获取subscribe(是否关注公众号)
function WSTSigninfo($info,$user){
	if(!empty($info['openid'])){
		$we = WSTWechat();
		$openid = ($user['wxOpenId'])?$user['wxOpenId']:$info['openid'];
		$signinfo = $we->wxUserInfo($openid);
		session('WST_WX_SIGNINFO',$signinfo);
	}
}

/**
 * 小程序配置
 */
function WSTWeapp(){
	$weapp = new \wechat\WSTWechat(WSTConf('CONF.weAppId'),WSTConf('CONF.weAppKey'));
	return $weapp;
}

/**
 * 判断有没有权限
 * @param $code 权限代码
 * @param $type 返回的类型  true-boolean   false-string
 */
function WSTGrant($code){
	$STAFF = session("WST_STAFF");
	if(in_array($code,$STAFF['privileges']))return true;
	return false;
}



/**
 * 建立文件夹
 * @param string $aimUrl
 * @return viod
 */
function WSTCreateDir($aimUrl) {
	$aimUrl = str_replace('', '/', $aimUrl);
	$result = true;
	if (!is_dir($aimUrl)) {
		$result = mkdir($aimUrl,0777,true);
	}
	return $result;
}


/**
* 文字自动换行
* @param  [type] $fontsize    [字体大小]
* @param  [type] $angle       [角度]
* @param  [type] $fontface    [字体名称]
* @param  [type] $string      [字符串]
* @param  [type] $width       [预设宽度]
*/
function WSTImageAutoWrap($fontsize, $angle, $fontface, $string, $width) {
    $content = "";
    // 将字符串拆分成一个个单字 保存到数组 letter 中
    preg_match_all("/./u", $string, $arr);
    $letter = $arr[0];
    foreach ($letter as $l) {
        $teststr = $content." ".$l;
        $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
        // 判断拼接后的字符串是否超过预设的宽度
        if (($testbox[2] > $width) && ($content !== "")) {
            $content .= PHP_EOL;
        }
        $content .= $l;
    }
    return $content;
}

/**
* 获取文字宽度
* @param  [type] $fontsize    [字体大小]
* @param  [type] $angle       [角度]
* @param  [type] $fontface    [字体名称]
* @param  [type] $string      [字符串]
* @param  [type] $width       [预设宽度]
*/
function WSTImageLetterWidth($fontsize, $angle, $fontface, $string, $width) {
    $content = "";
    // 将字符串拆分成一个个单字 保存到数组 letter 中
    preg_match_all("/./u", $string, $arr);
	$letter = $arr[0];
	$letterWidth = 0;
    foreach ($letter as $l) {
        $teststr = $content." ".$l;
        $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
        // 判断拼接后的字符串是否超过预设的宽度
        if (($testbox[2] > $width) && ($content !== "")) {
			// 超过预设宽度则返回当前文字宽度
			return $testbox[2];
		}
		$letterWidth = $testbox[2];
        $content .= $l;
	}
	return $letterWidth;
    return $content;
}


function WSTCreateQrcode($qr_url,$logo_url="",$dir='images',$time=3600,$margin=0){
        $subDir =  'upload/shares/'.$dir.'/'.date("Y-m");
        WSTCreateDir(WSTRootPath().'/'.$subDir);
        $rnd = md5($qr_url);
        $outImg = $subDir.'/share_'.$rnd.'.png';
        $shareImg = WSTRootPath().'/'.$outImg;
       
        require Env::get('root_path') . 'extend/qrcode/phpqrcode.php';
        $errorCorrectionLevel = 'L';//容错级别   
        $matrixPointSize = 10;//生成图片大小   
        //生成二维码图片   
        $qrcode = new \QRcode();
        $qrcodeImg = $subDir.'/qrcode_'.$rnd.'.png';
        $qr_code = WSTRootPath().'/'.$qrcodeImg;
        $qrcode->png($qr_url, $qr_code, $errorCorrectionLevel, $matrixPointSize, $margin);   

        if($logo_url!=""){
        	$share_bg = imagecreatefromstring(file_get_contents($qr_code));
	        $new_img = imagecreatefromstring(file_get_contents($logo_url));

	        $share_width = imagesx($share_bg);//二维码图片宽度   
	        $share_height = imagesy($share_bg);//二维码图片高度   
	        $new_width = imagesx($new_img);//logo图片宽度   
	        $new_height = imagesy($new_img);//logo图片高度   
	        $new_qr_width = $share_width / 4;   
	        $new_qr_height = $share_height / 4; 
	        $from_width = ($share_width - $new_qr_width) / 2;
	        $from_height = ($share_height - $new_qr_height) / 2;
	        //重新组合图片并调整大小   
	        imagecopyresampled($share_bg, $new_img, $from_width, $from_height, 0, 0, $new_qr_width, $new_qr_height, $new_width, $new_height); 
	    
	        //输出图片
	        $shareImg = WSTRootPath().'/'.$outImg;
	        imagepng($share_bg, $shareImg);
	        imagedestroy($new_img);
	        imagedestroy($share_bg);
	        unlink($qr_code);
        }else{
        	$outImg = $qrcodeImg;
        }
        return $outImg; 
}

/**
 * 将图片截取成圆形
 * @param [type] $imgpath 原图路径
 * @param [type] $outImg  输出路径
 */
function WSTCutFillet($imgpath,$outImg){
	ini_set('memory_limit', -1);
	$ext= pathinfo($imgpath);
	$src_img= null;

	switch (strtolower($ext['extension'])) {
		case 'jpg':
			$src_img = imagecreatefromjpeg($imgpath);
			break;
		case 'jpeg':
			$src_img = imagecreatefromjpeg($imgpath);
			break;
		case 'gif':
			$src_img = imagecreatefromgif($imgpath);
			break;
		case 'png':
			$src_img = imagecreatefrompng($imgpath);
			break;
		default:
			$src_img = imagecreatefromjpeg($imgpath);
			break;
	}

	
	$wh= getimagesize($imgpath);
	$w= $wh[0];
	$h= $wh[1];
	$w= min($w,$h);
	$h= $w;
	$img = imagecreatetruecolor($w, $h);
	//这一句一定要有
	imagesavealpha($img, true);
	//拾取一个完全透明的颜色,最后一个参数127为全透明
	$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
	imagefill($img, 0, 0, $bg);
	$r= $w / 2; //圆半径
	$y_x= $r; //圆心X坐标
	$y_y= $r; //圆心Y坐标
	for ($x=0;$x<$w;$x++) {
		for ($y=0;$y<$h;$y++) {
			$rgbColor=imagecolorat($src_img, $x, $y);
			if (((($x-$r)*($x-$r)+($y-$r)*($y-$r))<($r*$r))){
				imagesetpixel($img,$x,$y,$rgbColor);
			}
		}
	}
	imagepng($img,$outImg);
	imagedestroy($img);
	return $outImg;
}
/**
 * 处理多张图片合并解决png黑背景问题
 * @param [type] $dst_image 目标图象连接资源
 * @param [type] $src_image 源图象连接资源
 * @param [type] $dst_x  目标 X 坐标点
 * @param [type] $dst_y  目标 Y 坐标点
 * @param [type] $src_x  源的 X 坐标点
 * @param [type] $src_y  源的 Y 坐标点
 * @param [type] $src_w  源图象的宽度
 * @param [type] $src_h  源图象的高度
 * @param [type] $opacity    透明度
 */
function WSTImagecopymergeAlpha($bg_im, $src_im, $bg_x, $bg_y, $src_x, $src_y, $src_w, $src_h, $opacity=100){
    $w = imagesx($src_im);
    $h = imagesy($src_im);
    $src_im = WSTImageGetNewSize($src_im,$src_w, $src_h,$w);
    $cut = imagecreatetruecolor($src_w, $src_h);
    imagecopy($cut, $bg_im, 0, 0, $bg_x, $bg_y, $src_w, $src_h);
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
    imagecopymerge($bg_im, $cut, $bg_x, $bg_y, $src_x, $src_y, $src_w, $src_h, $opacity);
}

function WSTImageGetNewSize($imgpath,$new_width,$new_height,$w){
	$image_p = imagecreatetruecolor($new_width, $new_height);//新画布
	$bg = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
	imagefill($image_p, 0, 0, $bg);
	imagecopyresampled($image_p, $imgpath, 0, 0, 0, 0, $new_width, $new_height, $w, $w);
	return $image_p;
}


/**
 * 获取指定的记录
 */
function WSTTable($table,$where = [],$field = '*',$num = 1,$order = ''){
    $db = Db::name($table);
    if(!empty($where))$db->where($where);
    if($field!='*')$db->field($field);
    if($num>1){
        if($order!='')$db->order($order);
        return $db->select();
    }else{
        return $db->find();
    }
} 

/**
 * 经纬度获取地址
 */
function WSTLatLngAddress($lat,$lng){
    $key = WSTConf('CONF.mapKey');
    $url = WSTProtocol()."apis.map.qq.com/ws/geocoder/v1/?location=".$lat.",".$lng."&key=".$key."&get_poi=1&output=json";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($res,true);
    return $res;
}

/*
 * 是否开启首页自定义布局
 */
function WSTIsOpenIndexCustomPage($type){
    $pageId = Db::name('custom_pages')->where(['dataFlag'=>1,'isIndex'=>1,'pageType'=>$type])->value('id');
    return ($pageId > 0)?$pageId:0;
}

/*
 * 首页自定义布局底部导航栏
 */
function WSTIndexCustomPageMenu($pageId){
    $rs = Db::name('custom_page_decoration')->field('name,attr,sort')->where(['pageId'=>$pageId,'name'=>'tabbar','dataFlag'=>'1'])->find();
    $rs['attr'] = unserialize($rs["attr"]);
    $rs['color'] = $rs["attr"]["text_color"];
    $rs['selectedColor'] = $rs["attr"]["text_checked_color"];
    $rs['backgroundColor'] = $rs["attr"]["background_color"];
    $rs['borderStyle'] = $rs["attr"]["border_color"];
    if(isset($rs["attr"]["icon"])){
        for($i=0;$i<count($rs["attr"]["icon"]);$i++){
            $tabbarData['icon'] = $rs["attr"]["icon"][$i];
            $tabbarData['selectIcon'] = $rs["attr"]["select_icon"][$i];
            $tabbarData['link'] = $rs["attr"]["link"][$i];
            $tabbarData['text'] = $rs["attr"]["text"][$i];
            $tabbarData['menuFlag'] = $rs["attr"]["menu_flag"][$i];
            $rs["tabbars"][] = $tabbarData;
        }
    }
    
    unset($rs['attr']);
    return $rs;
}

/*
 * 自定义布局组件链接处理
 */
function WSTCustomPageLink($link){
    if(substr($link,0,4)!='http' && substr($link,0,5)!='https'){
        $link  = url('/','','',true).$link;
    }
    return $link;
}

/***
 * 对富文本编辑器进行过滤
 * @param string $html              要转换的内容
 * @param boolean $isTranferBefore  是否传入前已经进行了转义
 */
function WSTRichEditorFilter($html,$isTranferBefore = true){
    if($isTranferBefore)$html = htmlspecialchars_decode($html);
    require_once Env::get('root_path').'/extend/htmlpurifier/HTMLPurifier.auto.php';
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('HTML.SafeEmbed', true);
    $config->set('HTML.SafeObject', true);

    //$config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,li,br,span[style],img[width|height|alt|src]');
    $purifier = new \HTMLPurifier($config);
    $html = $purifier->purify($html);
    if($isTranferBefore)$html =  htmlspecialchars($html);
    $html = str_replace(['%7B','%7b'],'{',$html);
    $html = str_replace(['%7D','%7d'],'}',$html);
    $html = str_replace('application/x-shockwave-flash','',$html);
    return $html;
}
/**
 * 过滤内容里边的html标签
 * @param string $str                要处理的字符串内容
 * @param boolean $isTranferBefore   是否传入前已经进行了转义
 */
function WSTStripTags($str,$isTranferBefore = true){
    if($isTranferBefore)$str = htmlspecialchars_decode($str);
    $str = strip_tags($str);
    if($isTranferBefore)$str =  htmlspecialchars($str);
    return $str;
}

function WSTTargetTypes(){
	$isOpenSupplier = (int)WSTConf('CONF.isOpenSupplier');
	$targertTypes = [0=>"会员",1=>"商家",2=>"门店"];
	if($isOpenSupplier==1)$targertTypes[3]="供货商";
	return $targertTypes;
}

function WSTGetTargetTypeName($targetType=0){
	$targertTypes = WSTTargetTypes();
	return $targertTypes[$targetType];
}

function WSTGuid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        //$uuid = chr(123)// "{"
        $uuid = substr($charid, 0, 8)
                .substr($charid, 8, 4)
                .substr($charid,12, 4)
                .substr($charid,16, 4)
                .substr($charid,20,12);
               // .chr(125);// "}"
        return $uuid;
    }
}


/**
 * 获取订单核验码
 * type 0：商家订单 1：供货商订单
 */
function WSTOrderVerificationCode($id,$type=0){
    $time= microtime();
    $arr=explode(" ",$time);
    $micro=$arr[0]*1000000000;
    $micro_str=substr($micro."",0,8);
    $vCode = rand(0,9).($id+$micro_str).rand(100,1000);
    $cnt = 0;
    if($type==1){
        $cnt = Db::name("supplier_orders")->where(["supplierId"=>$id,"deliverType"=>1,"verificationCode"=>$vCode])->count();
    }else{
        $cnt = Db::name("orders")->where(["shopId"=>$id,"deliverType"=>1,"verificationCode"=>$vCode])->count();
    }
    if($cnt>0){
        $vCode = WSTOrderVerificationCode($id,$type);
    }
    return $vCode;
}
/**
 * 格式输出订单核验码
 */
function WSTFormatVerificationCode($vCode){
    return join(" ",str_split($vCode,4));
}