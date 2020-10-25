<?php
namespace wstmart\common\model;
use think\Db;
use Env;
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
 * 用户类
 */
class Users extends Base{
	protected $pk = 'userId';
    /**
     * 用户登录验证
     * 登录来源$loginSrc,pc:0,mobile:2,wechat:1,app-android:3,app-ios:4,weapp:5
     */
    public function checkLogin($loginSrc = 0){
    	$loginName = input("post.loginName");
    	$loginPwd = input("post.loginPwd");
    	$code = input("post.verifyCode");
        $typ = (int)input("post.typ");
    	$rememberPwd = input("post.rememberPwd",1);
    	if(!WSTVerifyCheck($code) && strpos(WSTConf("CONF.captcha_model"),"4")>=0){
    		return WSTReturn('验证码错误!');
    	}
    	$decrypt_data = WSTRSA($loginPwd);
    	if($decrypt_data['status']==1){
    		$loginPwd = $decrypt_data['data'];
    	}else{
    		return WSTReturn('登录失败');
    	}
    	$rs = $this->where("loginName|userEmail|userPhone",$loginName)
    				->where(["dataFlag"=>1, "userStatus"=>1])
    				->find();
    	
    	hook("beforeUserLogin",["user"=>&$rs,'loginType'=>'account']);
    	if(!empty($rs)){
            if($rs['loginPwd']!=md5($loginPwd.$rs['loginSecret']))return WSTReturn("密码错误");
            if($rs['userPhoto']=='')$rs['userPhoto'] = WSTConf('CONF.userLogo');
    		$userId = $rs['userId'];
    		//获取用户等级
	    	$rrs = Db::name('user_ranks')->where(['dataFlag'=>1])->where('startScore','<=',$rs['userTotalScore'])->where('endScore','>=',$rs['userTotalScore'])->field('rankId,rankName,userrankImg')->find();
	    	$rs['rankId'] = $rrs['rankId'];
	    	$rs['rankName'] = $rrs['rankName'];
	    	$rs['userrankImg'] = $rrs['userrankImg'];
    		if(input("post.typ")==2){
    			$shoprs=$this->where(["dataFlag"=>1, "userStatus"=>1,"userType"=>1,"userId"=>$userId])->find();
    			if(empty($shoprs)){
    				return WSTReturn('您还没申请店铺!');
    			}
    		}
    		$ip = request()->ip();
    		$update = [];
    		$update = ["lastTime"=>date('Y-m-d H:i:s'),"lastIP"=>$ip];
    		$wxOpenId = session('WST_WX_OPENID');
    		if($wxOpenId){
    			$update['wxOpenId'] = $rs['wxOpenId'] = session('WST_WX_OPENID');
                // 保存unionId【若存在】 详见 unionId说明 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140839
                $userinfo = session('WST_WX_USERINFO');
                $update['wxUnionId'] = isset($userinfo['unionid'])?$userinfo['unionid']:'';
                if($rs['userPhoto']==WSTConf('CONF.userLogo')){
                    $rs['userPhoto'] = $userinfo['headimgurl'];
                    $update['userPhoto'] = $userinfo['headimgurl'];
                }
    		}
    		$this->where(["userId"=>$userId])->update($update);
    		
    		
    		//如果是店铺则加载店铺信息
    		if($rs['userType']==1){
    			$shop = Db::name("shops s")
                        ->join("__SHOP_USERS__ su","s.shopId=su.shopId")
                        ->join("__SHOP_ROLES__ sr","sr.id=su.roleId",'left')
                        ->field("s.*,su.roleId,sr.roleName,sr.privilegeUrls")
                        ->where(["su.userId"=>$userId,"s.dataFlag" =>1,"s.shopStatus" =>1])->find();
                if($typ==2 && empty($shop)){
                    return WSTReturn("店铺已停用，不能登录!",-1);
                }
                if(!empty($shop)){
                    
                    $shopMenuMaps = $this->shopMenuMaps();
                    $shop["shopMenuMaps"] = $shopMenuMaps;
                    //处理处店铺权限
                    $shop['SHOP_MASTER'] = ($shop['userId']==$userId)?true:false;//判断是否主账号
                    $shop['visitPrivilegeUrls'] = [];
                    if($shop['userId']!=$userId){//非主账号，取出有权限的请求
                        $menuUrls = isset($shop["privilegeUrls"])?json_decode($shop["privilegeUrls"],true):[];
                        $urls = ['shop/index/index','shop/index/main','shop/index/getsysmessages','shop/index/clearcache'];
                        foreach ($menuUrls as $key => $v) {
                            foreach ($v as $key2 => $v2) {
                                if(count($v2['urls'])>0){
                                    foreach ($v2['urls'] as $ukey => $uv) {
                                        $uv = trim($uv);
                                        if($uv!='' && !in_array($uv,$urls))$urls[] = $uv;
                                    }
                                }
                                if(count($v2['otherUrls'])>0){
                                    if($v2['otherUrls']=='')continue;
                                    foreach ($v2['otherUrls'] as $ukey => $uv) {
                                        $uv = explode(',',$uv);
                                        foreach ($uv as $ukey2 => $uv2) {
                                            $uv2 = trim($uv2);
                                            if($uv2!='' && !in_array($uv2,$urls))$urls[] = $uv2;
                                        }
                                    }
                                }
                            }
                        }
                        $shop['visitPrivilegeUrls'] = $urls;
                    }
                    if(!empty($shop))$rs = array_merge($shop,$rs->toArray());
                }else{
                   $rs['userType'] = 0; 
                }
                
    		}

    		//签到时间
    		if(WSTConf('CONF.signScoreSwitch')==1){
    			$rs['signScoreTime'] = 0;
				$userscores = Db::name('user_scores')->where([["userId",'=',$userId],["dataSrc",'=',5]])->where('left(createTime,7)="'.date('Y-m').'"')->order('createTime desc')->find();
    			if($userscores)$rs['signScoreTime'] = date("Y-m-d",strtotime($userscores['createTime']));
    		}
    		//记录登录日志
    		$data = array();
    		$data["userId"] = $userId;
    		$data["loginTime"] = date('Y-m-d H:i:s');
    		$data["loginIp"] = $ip;
            $data['loginSrc'] = $loginSrc;
    		Db::name('log_user_logins')->insert($data);
    		
    		$rd = $rs;
    		//记住密码
    		cookie("loginName", $loginName, 3600*24*90);
    		if($rememberPwd == "on"){
    			$datakey = md5($rs['loginName'])."_".md5($rs['loginPwd']);
    			$key = $rs['loginSecret'];
    			//加密
    			require Env::get('root_path') . 'extend/org/Base64.php';
    			$base64 = new \org\Base64();
    			$loginKey = $base64->encrypt($datakey, $key);

    			cookie("loginPwd", $loginKey, 3600*24*90);
    		}else{
    			cookie("loginPwd", null);
    		}
    		session('WST_USER',$rs);
    		
    		hook('afterUserLogin',['user'=>$rs]);
    		
    		return WSTReturn("登录成功","1");
    	
    	}
    	return WSTReturn("用户不存在");
    }

    public function shopMenuMaps(){
        $menuMaps = [];
        $list = Db::name("home_menus")->where([["menuType",'in','1,2']])->field("menuId,menuName,menuUrl,menuOtherUrl")->select();
        foreach ($list as $k => $vo) {
            $menuUrls = [];
            if(strlen($vo["menuOtherUrl"])>2){
                $menuUrls = explode(",",strtolower($vo['menuOtherUrl']));
            }
            if(strlen($vo["menuUrl"])>2){
                $menuUrls[] = $vo['menuUrl'];
            }
            $menuUrls = array_unique($menuUrls);

            foreach ($menuUrls as $k2 => $vo2) {
                if(!array_key_exists($vo2,$menuMaps)){
                    $obj = [];
                    $obj["menuId"] = $vo['menuId'];
                    $obj["menuName"] = $vo['menuName'];
                    $menuMaps[$vo2] = $obj;
                }
            }
        }
        
        return $menuMaps;
    }
    /**
     * 用户手机登录验证
     * 登录来源$loginSrc,pc:0,mobile:2,wechat:1,app-android:3,app-ios:4,weapp:5
     */
    public function checkLoginByPhone($loginSrc = 0){
        $typ = (int)input("post.typ");
        $loginName = input("post.loginNamea");
        $phoneVerify = input("post.mobileCode");
        $timeVerify = session('VerifyCode_userPhone_Time2');
        if($loginName!=session('VerifyCode_userPhone2')){
            return WSTReturn("登录手机号与校验手机号不一致，请重新输入！",-1);
        }
        if(!session('VerifyCode_userPhone_Verify2') || time()>floatval($timeVerify)+10*60){
            return WSTReturn("短信验证码已失效，请重新发送！",-1);
        }
        if($phoneVerify!=session('VerifyCode_userPhone_Verify2')){
            return WSTReturn("短信验证码不一致，请重新输入！",-1);
        }
        $rs = $this->where("userPhone",$loginName)
            ->where(["dataFlag"=>1, "userStatus"=>1])
            ->find();

        hook("beforeUserLogin",["user"=>&$rs,'loginType'=>'phone']);
        if(!empty($rs)){
            if($rs['userPhoto']=='')$rs['userPhoto'] = WSTConf('CONF.userLogo');
            $userId = $rs['userId'];
            //获取用户等级
            $rrs = Db::name('user_ranks')->where(['dataFlag'=>1])->where('startScore','<=',$rs['userTotalScore'])->where('endScore','>=',$rs['userTotalScore'])->field('rankId,rankName,userrankImg')->find();
            $rs['rankId'] = $rrs['rankId'];
            $rs['rankName'] = $rrs['rankName'];
            $rs['userrankImg'] = $rrs['userrankImg'];
            if(input("post.typ")==2){
                $shoprs=$this->where(["dataFlag"=>1, "userStatus"=>1,"userType"=>1,"userId"=>$userId])->find();
                if(empty($shoprs)){
                        return WSTReturn('您还没申请店铺!');
                }
            }
            $ip = request()->ip();
            $update = [];
            $update = ["lastTime"=>date('Y-m-d H:i:s'),"lastIP"=>$ip];
            $wxOpenId = session('WST_WX_OPENID');
            if($wxOpenId){
                $update['wxOpenId'] = $rs['wxOpenId'] = session('WST_WX_OPENID');
                    // 保存unionId【若存在】 详见 unionId说明 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140839
                $userinfo = session('WST_WX_USERINFO');
                $update['wxUnionId'] = isset($userinfo['unionid'])?$userinfo['unionid']:'';
                if($rs['userPhoto']==WSTConf('CONF.userLogo')){
                    $rs['userPhoto'] = $userinfo['headimgurl'];
                    $update['userPhoto'] = $userinfo['headimgurl'];
                }
            }
            $this->where(["userId"=>$userId])->update($update);
            //如果是店铺则加载店铺信息
            if($rs['userType']>=1){
                    $shop = Db::name("shops s")
                        ->join("__SHOP_USERS__ su","s.shopId=su.shopId")
                        ->join("__SHOP_ROLES__ sr","sr.id=su.roleId",'left')
                        ->field("s.*,su.roleId,sr.roleName,sr.privilegeUrls")
                        ->where(["su.userId"=>$userId,"s.dataFlag" =>1,"s.shopStatus" =>1])->find();
                if($typ==2 && empty($shop)){
                    return WSTReturn("店铺已停用，不能登录!",-1);
                }
                if(!empty($shop)){
                    //处理处店铺权限
                    $shop['SHOP_MASTER'] = ($shop['userId']==$userId)?true:false;//判断是否主账号
                    $shop['visitPrivilegeUrls'] = [];
                    if($shop['userId']!=$userId){//非主账号，取出有权限的请求
                        $menuUrls = isset($shop["privilegeUrls"])?json_decode($shop["privilegeUrls"],true):[];
                        $urls = ['shop/index/index','shop/index/main'];
                        foreach ($menuUrls as $key => $v) {
                            foreach ($v as $key2 => $v2) {
                                if(count($v2['urls'])>0){
                                    foreach ($v2['urls'] as $ukey => $uv) {
                                        if($uv!='' && !in_array($uv,$urls))$urls[] = $uv;
                                    }
                                }
                                if(count($v2['otherUrls'])>0){
                                    if($v2['otherUrls']=='')continue;
                                    foreach ($v2['otherUrls'] as $ukey => $uv) {
                                        $uv = explode(',',$uv);
                                        foreach ($uv as $ukey2 => $uv2) {
                                            if($uv2!='' && !in_array($uv2,$urls))$urls[] = $uv2;
                                        }
                                    }
                                }
                            }
                        }
                        $shop['visitPrivilegeUrls'] = $urls;
                    }
                    if(!empty($shop))$rs = array_merge($shop,$rs->toArray());
                }else{
                    $rs['userType'] = 0; 
                }
            }

            //签到时间
            if(WSTConf('CONF.signScoreSwitch')==1){
                $rs['signScoreTime'] = 0;
				    $userscores = Db::name('user_scores')->where([["userId",'=',$userId],["dataSrc",'=',5]])->where('left(createTime,7)="'.date('Y-m').'"')->order('createTime desc')->find();
                if($userscores)$rs['signScoreTime'] = date("Y-m-d",strtotime($userscores['createTime']));
            }
            //记录登录日志
            $data = array();
            $data["userId"] = $userId;
            $data["loginTime"] = date('Y-m-d H:i:s');
            $data["loginIp"] = $ip;
            $data['loginSrc'] = $loginSrc;
            Db::name('log_user_logins')->insert($data);

            $rd = $rs;
            cookie("loginName", $loginName, 3600*24*90);

            session('WST_USER',$rs);

            hook('afterUserLogin',['user'=>$rs]);
            session('VerifyCode_userPhone2',null);
            session('VerifyCode_userPhone_Verify2',null);
            session('VerifyCode_userPhone_Time2',null);
            return WSTReturn("登录成功","1");
        }
        return WSTReturn("用户不存在");
    }
    
    /**
     * 会员注册（手机号）
     * 登录来源$loginSrc,pc:0,mobile:2,wechat:1,app-android:3,app-ios:4,weapp:5
     */
    public function regist($loginSrc = 0){
    	$data = array();
    	$loginName = input("post.loginName");
        $data['loginName'] = $loginName;
    	$data['loginPwd'] = input("post.loginPwd");
        $data['userPhone'] = $loginName;
        $startTime = (int)session('VerifyCode_userPhone_Time');
        if((time()-$startTime)>120){
            return WSTReturn("验证码已超过有效期!");
        }
        $userPhone = session('VerifyCode_userPhone');
        if($data['loginName']!=$userPhone){
            return WSTReturn("注册手机号与验证手机号不一致!");
        }
        $mobileCode = input("post.mobileCode");
        $verify = session('VerifyCode_userPhone_Verify');
        if($mobileCode=="" || $verify != $mobileCode){
            return WSTReturn("短信验证码错误!");
        }
    	//检测账号是否存在
    	$crs = WSTCheckLoginKey($data['loginName']);
    	if($crs['status']!=1)return $crs;
    	$decrypt_data = WSTRSA($data['loginPwd']);
    	if($decrypt_data['status']==1){
    		$data['loginPwd'] = $decrypt_data['data'];
    	}else{
    		return WSTReturn('注册失败');
    	}
    	foreach ($data as $v){
    		if($v ==''){
    			return WSTReturn("注册信息不完整!");
    		}
    	}
		$loginName = WSTRandomLoginName($loginName);
    	if($loginName=='')return WSTReturn("注册失败!");//分派不了登录名
    	$data['loginName'] = $loginName;
    	unset($data['reUserPwd']);
    	unset($data['protocol']);
    	//检测账号，邮箱，手机是否存在
    	$data["loginSecret"] = rand(1000,9999);
    	$data['loginPwd'] = md5($data['loginPwd'].$data['loginSecret']);
    	$data['userType'] = 0;
    	$data['userName'] = '手机用户'.substr($data['userPhone'],-4);
    	$data['userQQ'] = "";
    	$data['userScore'] = 0;
    	$data['createTime'] = date('Y-m-d H:i:s');
    	$data['dataFlag'] = 1;
        $data['lastTime'] = date('Y-m-d H:i:s');
        $data['lastIP'] = request()->ip();
        $wxOpenId = session('WST_WX_OPENID');
    	if($wxOpenId){
    		$data['wxOpenId'] = session('WST_WX_OPENID');
			$userinfo = session('WST_WX_USERINFO');
			if($userinfo){
                $nickname = json_encode($userinfo['nickname']);
                $nickname = preg_replace("/\\\u[ed][0-9a-f]{3}\\\u[ed][0-9a-f]{3}/","*",$nickname);//替换成*
                $nickname = json_decode($nickname);
                if($nickname=="") $nickname = "微信用户";
                $data['userName'] = $nickname;
				$data['userSex'] = $userinfo['sex'];
				$data['userPhoto'] = $userinfo['headimgurl'];
                // 保存unionId【若存在】 详见 unionId说明 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140839
                $data['wxUnionId'] = isset($userinfo['unionid'])?$userinfo['unionid']:'';
			}
    	}
    	Db::startTrans();
        try{
	    	$userId = $this->data($data)->save();
	    	if(false !== $userId){
                $userId = $this->userId;
	    		//记录登录日志
	    		$data = array();
	    		$data["userId"] = $userId;
	    		$data["loginTime"] = date('Y-m-d H:i:s');
	    		$data["loginIp"] = request()->ip();
                $data['loginSrc'] = $loginSrc;
	    		Db::name('log_user_logins')->insert($data);
	    		$user = $this->get(['userId'=>$userId]);
	    	    if($user['userPhoto']=='')$user['userPhoto'] = WSTConf('CONF.userLogo');
	    		session('WST_USER',$user);
	    		//注册成功后执行钩子
	    		hook('afterUserRegist',['user'=>$user]);
                //发送消息
                $tpl = WSTMsgTemplates('USER_REGISTER');
                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
                    $find = ['${LOGIN_NAME}','${MALL_NAME}'];
                    $replace = [$user['loginName'],WSTConf('CONF.mallName')];
                    WSTSendMsg($userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>0]);
                }
	    		Db::commit();
                session('VerifyCode_userPhone',null);
                session('VerifyCode_userPhone_Time',null);
                session('VerifyCode_userPhone_Verify',null);
	    		return WSTReturn("注册成功",1);
	    	}
        }catch (\Exception $e) {
            session('WST_USER',null);
        	Db::rollback();
        }
    	return WSTReturn("注册失败!");
    }

    /**
     * 会员注册（账号）
     * 登录来源$loginSrc,pc:0,mobile:2,wechat:1,app-android:3,app-ios:4,weapp:5
     */
    public function registByAccount($loginSrc = 0){
        $code = input("post.verifyCode");
        if(!WSTVerifyCheck($code)){
            return WSTReturn('验证码错误!');
        }
        $data = array();
        $data['loginName'] = input("post.loginName");
        $data['loginPwd'] = input("post.loginPwd");
        if (!preg_match('/^[A-Za-z0-9_]+$/', $data['loginName'])) {
            return WSTReturn("用户名必须是数字、字母或下划线!");
        }
        $loginName =  $data['loginName'];
        //检测账号是否存在
        $crs = WSTCheckLoginKey($loginName);
        if($crs['status']!=1)return $crs;
        $decrypt_data = WSTRSA($data['loginPwd']);
        if($decrypt_data['status']==1){
            $data['loginPwd'] = $decrypt_data['data'];
        }else{
            return WSTReturn('注册失败');
        }
        foreach ($data as $v){
            if($v ==''){
                return WSTReturn("注册信息不完整!");
            }
        }
        //$loginName = WSTRandomLoginName($loginName);
        if($loginName=='')return WSTReturn("注册失败!");//分派不了登录名
        $data['loginName'] = $loginName;
        unset($data['reUserPwd']);
        unset($data['protocol']);
        $data["loginSecret"] = rand(1000,9999);
        $data['loginPwd'] = md5($data['loginPwd'].$data['loginSecret']);
        $data['userType'] = 0;
        $data['userName'] = $loginName;
        $data['userQQ'] = "";
        $data['userScore'] = 0;
        $data['createTime'] = date('Y-m-d H:i:s');
        $data['dataFlag'] = 1;
        $data['lastTime'] = date('Y-m-d H:i:s');
        $data['lastIP'] = request()->ip();
        $wxOpenId = session('WST_WX_OPENID');
        if($wxOpenId){
            $data['wxOpenId'] = session('WST_WX_OPENID');
            $userinfo = session('WST_WX_USERINFO');
            if($userinfo){
                $nickname = json_encode($userinfo['nickname']);
                $nickname = preg_replace("/\\\u[ed][0-9a-f]{3}\\\u[ed][0-9a-f]{3}/","*",$nickname);//替换成*
                $nickname = json_decode($nickname);
                if($nickname=="") $nickname = "微信用户";
                $data['userName'] = $nickname;
                $data['userSex'] = $userinfo['sex'];
                $data['userPhoto'] = $userinfo['headimgurl'];
                // 保存unionId【若存在】 详见 unionId说明 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140839
                $data['wxUnionId'] = isset($userinfo['unionid'])?$userinfo['unionid']:'';
            }
        }
        Db::startTrans();
        try{
            $userId = $this->data($data)->save();
            if(false !== $userId){
                $userId = $this->userId;
                //记录登录日志
                $data = array();
                $data["userId"] = $userId;
                $data["loginTime"] = date('Y-m-d H:i:s');
                $data["loginIp"] = request()->ip();
                $data['loginSrc'] = $loginSrc;
                Db::name('log_user_logins')->insert($data);
                $user = $this->get(['userId'=>$userId]);
                if($user['userPhoto']=='')$user['userPhoto'] = WSTConf('CONF.userLogo');
                session('WST_USER',$user);
                //注册成功后执行钩子
                hook('afterUserRegist',['user'=>$user]);
                //发送消息
                $tpl = WSTMsgTemplates('USER_REGISTER');
                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
                    $find = ['${LOGIN_NAME}','${MALL_NAME}'];
                    $replace = [$user['loginName'],WSTConf('CONF.mallName')];
                    WSTSendMsg($userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>0]);
                }
                Db::commit();
                return WSTReturn("注册成功",1);
            }
        }catch (\Exception $e) {
            session('WST_USER',null);
            Db::rollback();
        }
        return WSTReturn("注册失败!");
    }
    /**
     * 查询用户手机是否存在
     * 
     */
    public function checkUserPhone($userPhone,$userId = 0){
    	$dbo = $this->where(["dataFlag"=>1, "userPhone"=>$userPhone]);
    	if($userId>0){
    		$dbo->where("userId","<>",$userId);
    	}
    	$rs = $dbo->count();
    	if($rs>0){
    		return WSTReturn("手机号已存在!");
    	}else{
    		return WSTReturn("",1);
    	}
    }

    /**
     * 修改用户密码
     */
    public function editPass($id){
    	$data = array();
    	$newPass = input("post.newPass");
        if($newPass=='')return WSTReturn('新密码不能为空',-1);
    	$decrypt_data = WSTRSA($newPass);
    	if($decrypt_data['status']==1){
    		$newPass = $decrypt_data['data'];
    	}else{
    		return WSTReturn('修改失败');
    	}
    	$rs = $this->where('userId='.$id)->find();
    	//核对密码
    	if($rs['loginPwd']){
    		$oldPass = input("post.oldPass");
    		$decrypt_data2 = WSTRSA($oldPass);
    		if($decrypt_data2['status']==1){
    			$oldPass = $decrypt_data2['data'];
    		}else{
    			return WSTReturn('修改失败');
    		}
    		if($rs['loginPwd']==md5($oldPass.$rs['loginSecret'])){
    			$data["loginPwd"] = md5($newPass.$rs['loginSecret']);
    			$rs = $this->update($data,['userId'=>$id]);
    			if(false !== $rs){
    				hook("afterEditPass",["userId"=>$id]);
    				return WSTReturn("密码修改成功", 1);
    			}else{
    				return WSTReturn($this->getError(),-1);
    			}
    		}else{
    			return WSTReturn('原始密码错误',-1);
    		}
    	}else{
    		$data["loginPwd"] = md5($newPass.$rs['loginSecret']);
    		$rs = $this->update($data,['userId'=>$id]);
    		if(false !== $rs){
    			hook("afterEditPass",["userId"=>$id]);
    			return WSTReturn("密码修改成功", 1);
    		}else{
    			return WSTReturn($this->getError(),-1);
    		}
    	}
    }
    /**
     * 修改用户支付密码
     */
    public function editPayPass($id){
        $data = array();
        $newPass = input("post.newPass");
        $decrypt_data = WSTRSA($newPass);
        if($decrypt_data['status']==1){
        	$newPass = $decrypt_data['data'];
        }else{
        	return WSTReturn('修改失败');
        }
        if(!$newPass){
            return WSTReturn('支付密码不能为空',-1);
        }
        $rs = $this->where('userId='.$id)->find();
        //核对密码
        if($rs['payPwd']){
        	$oldPass = input("post.oldPass");
        	$decrypt_data2 = WSTRSA($oldPass);
        	if($decrypt_data2['status']==1){
        		$oldPass = $decrypt_data2['data'];
        	}else{
        		return WSTReturn('修改失败');
        	}
            if($rs['payPwd']==md5($oldPass.$rs['loginSecret'])){
                $data["payPwd"] = md5($newPass.$rs['loginSecret']);
                $rs = $this->update($data,['userId'=>$id]);
                if(false !== $rs){
                    return WSTReturn("支付密码修改成功", 1);
                }else{
                    return WSTReturn("支付密码修改失败",-1);
                }
            }else{
                return WSTReturn('原始支付密码错误',-1);
            }
        }else{
            $data["payPwd"] = md5($newPass.$rs['loginSecret']);
            $rs = $this->update($data,['userId'=>$id]);
            if(false !== $rs){
                return WSTReturn("支付密码设置成功", 1);
            }else{
                return WSTReturn("支付密码修改失败",-1);
            }
        }
    }
    /**
     * 重置用户支付密码
     */
    public function resetbackPay($uId=0,$type=0){
    	$timeVerify = session('Verify_backPaypwd_Time');
    	if(time()>floatval($timeVerify)+10*60){
    		session('Type_backPaypwd',null);
    		return WSTReturn("校验码已失效，请重新验证！");
    		exit();
    	}
    	$data = array();
    	$data["payPwd"] = input("post.newPass");
    	
        if($uId==0 || $type==1){
            $decrypt_data = WSTRSA($data["payPwd"]);
        	if($decrypt_data['status']==1){
        		$data["payPwd"] = $decrypt_data['data'];
        	}else{
        		return WSTReturn('修改失败');
        	}
        }
    	if(!$data["payPwd"]){
    		return WSTReturn('支付密码不能为空',-1);
    	}
    	$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
    	$rs = $this->where('userId='.$userId)->find();
    	$data["payPwd"] = md5($data["payPwd"].$rs['loginSecret']);
    	$rs = $this->update($data,['userId'=>$userId]);
    	if(false !== $rs){
    		session('Type_backPaypwd',null);
    		session('Verify_backPaypwd_info',null);
    		session('Verify_backPaypwd_Time',null);
    		return WSTReturn("支付密码设置成功", 1);
    	}else{
    		return WSTReturn("支付密码修改失败",-1);
    	}
    }
   /**
    *  获取用户信息
    */
    public function getById($id){
    	$rs = $this->get(['userId'=>(int)$id]);
        if(!empty($rs)){
            $rs['ranks'] = WSTUserRank($rs['userTotalScore']);
        }
    	return $rs;
    }
    /**
     * 编辑资料
    */
    public function edit($userId=0){
    	$Id = ($userId>0)?$userId:(int)session('WST_USER.userId');
    	$data = input('post.');
        unset($data['userId']);
        if(isset($data['brithday']))$data['brithday'] = ($data['brithday']=='')?date('Y-m-d'):$data['brithday'];
    	WSTAllow($data,'brithday,trueName,userName,userPhoto,userQQ,userSex');
    	Db::startTrans();
		try{
            if(isset($data['userPhoto']) && $data['userPhoto']!='')
			     WSTUseResource(0, $Id, $data['userPhoto'],'users','userPhoto');
	    	$result = $this->allowField(true)->save($data,['userId'=>$Id]);
	    	if(false !== $result){
                $USER = session('WST_USER');
                if(!empty($USER)){
                    if(isset($data['userName']) && $data['userName']!='')$USER['userName'] = $data['userName'];
                    if(isset($data['userPhoto']) && $data['userPhoto']!='')$USER['userPhoto'] = $data['userPhoto'];
                    session('WST_USER',$USER);
                }
                
                if(isset($data['userPhoto']) && file_exists(WSTRootPath()."/".$data['userPhoto'])){
                    $str = explode('/',$data['userPhoto']);
                    $name = $str[count($str)-1];
                    array_pop($str);
                    $filePath = implode('/',$str);
                    $rdata = ['status'=>1,'savePath'=>$filePath."/",'name'=>$name];
                    hook('afterUploadPic',['data'=>&$rdata]);
                }
	    		Db::commit();
	    		return WSTReturn("编辑成功", 1);
	    	}
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败'.$e->getMessage(),-1);
        }	
    }
    /**
    * 绑定邮箱
     */
    public function editEmail($userId,$userEmail){
    	$data = array();
    	$data["userEmail"] = $userEmail;
    	Db::startTrans();
    	try{
    		$user = Db::name('users')->where(["userId"=>$userId])->field(["userId","loginName,userEmail"])->find();
			$rs = $this->update($data,['userId'=>$userId]);
			if(false !== $rs){
				hook("afterEditEmail",["user"=>$user]);
				Db::commit();
				return WSTReturn("绑定成功",1);
			}else{
				Db::rollback();
				return WSTReturn("",-1);
			}
		}catch (\Exception $e) {
    		Db::rollback();
    		return WSTReturn('编辑失败',-1);
    	}
    }
    /**
     * 绑定手机
     */
    public function editPhone($userId,$userPhone){
    	$data = array();
    	$data["userPhone"] = $userPhone;
    	$rs = $this->update($data,['userId'=>$userId]);
    	if(false !== $rs){
            session('Verify_info',null);
            session('Verify_userPhone_Time',null);
    		return WSTReturn("绑定成功", 1);
    	}else{
    		return WSTReturn($this->getError(),-1);
    	}
    }
    /**
     * 查询并加载用户资料
     */
    public function checkAndGetLoginInfo($key){
    	if($key=='')return array();
    	$rs = Db::name('users')->where([["loginName|userEmail|userPhone",'=',$key],['dataFlag','=',1]])->find();
    	return $rs;
    }
    /**
     * 重置用户密码
     */
    public function resetPass($uId=0){
    	if(time()>floatval(session('REST_Time'))+30*60){
    		return WSTReturn("连接已失效！", -1);
    	}
    	$reset_userId = (int)session('REST_userId');
    	if($reset_userId==0){
    		return WSTReturn("无效的用户！", -1);
    	}
    	$user = $this->where(["dataFlag"=>1,"userStatus"=>1,"userId"=>$reset_userId])->find();
    	if(empty($user)){
    		return WSTReturn("无效的用户！", -1);
    	}
    	$loginPwd = input("post.loginPwd");
        if($uId==0){// 大于0表示来自app端
            $decrypt_data = WSTRSA($loginPwd);
            if($decrypt_data['status']==1){
                $loginPwd = $decrypt_data['data'];
            }else{
                return WSTReturn('修改失败');
            }
        }
    	if(trim($loginPwd)==''){
    		return WSTReturn("无效的密码！", -1);
    	}
    	$data['loginPwd'] = md5($loginPwd.$user["loginSecret"]);
    	$rc = $this->update($data,['userId'=>$reset_userId]);
    	if(false !== $rc){
            session('REST_userId',null);
            session('REST_Time',null);
            session('REST_success',null);
            session('findPass',null);
            session('findPhone',null);
    		return WSTReturn("修改成功", 1);
    	}
    	return WSTReturn('修改失败');
    }
    
    /**
     * 获取用户可用积分
     */
    public function getFieldsById($userId,$fields){
    	return $this->where(['userId'=>$userId,'dataFlag'=>1])->field($fields)->find();
    }


    /**
     * 用户退出
     */
    public function logout(){
        session('WST_USER',null);
        setcookie("loginPwd", null);
        session('WST_HO_CURRENTURL', null);
        hook('afterUserLogout');
        return WSTReturn("退出成功",1);
    }

}
