<?php
namespace wstmart\store\model;
use wstmart\common\model\Users as CUsers;
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
use think\Db;
class Users extends CUsers{

	public function checkStoreLogin(){
    	$loginName = input("post.loginName");
    	$loginPwd = input("post.loginPwd");
    	$code = input("post.verifyCode");
        $typ = (int)input("post.typ");
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
    	
    	if(!empty($rs)){
            if($rs['loginPwd']!=md5($loginPwd.$rs['loginSecret']))return WSTReturn("密码错误");
            if($rs['userPhoto']=='')$rs['userPhoto'] = WSTConf('CONF.userLogo');
    		$userId = $rs['userId'];

    		
			$shoprs=$this->where(["dataFlag"=>1, "userStatus"=>1,"userType"=>2,"userId"=>$userId])->find();
			if(empty($shoprs)){
				return WSTReturn('您还没开通门店!');
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
    		if($rs['userType']==2){

    			$store = Db::name("stores s")
                        ->join("__STORE_USERS__ su","s.storeId=su.storeId")
                        ->join("__STORE_ROLES__ sr","sr.id=su.roleId",'left')
                        ->field("s.*,su.dataFlag dataFlag2,su.roleId,sr.roleName,sr.privilegeUrls")
                        ->where(["su.userId"=>$userId,"s.dataFlag" =>1,"s.storeStatus" =>1])->find();
                        
                if(empty($store)){
                    return WSTReturn("门店已停用，不能登录!",-1);
                }else{
                	$shopId = $store['shopId'];
                	$shop = Db::name("shops")->where(['shopId'=>$shopId,'shopStatus'=>1,'dataFlag'=>1])->find();
                	if(empty($shop)){
                		return WSTReturn("店铺已失效，不能登录!",-1);
                	}
                    //处理处店铺权限
                    $store['STORE_MASTER'] = ($store['userId']==$userId)?true:false;//判断是否主账号
                    $store['visitPrivilegeUrls'] = [];
                    if($store['userId']!=$userId){//非主账号，取出有权限的请求
                        $menuUrls = isset($store["privilegeUrls"])?json_decode($store["privilegeUrls"],true):[];
                        $urls = ['store/index/index','store/index/main'];
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
                        $store['visitPrivilegeUrls'] = $urls;
                    }
                    if(!empty($store))$rs = array_merge($store,$rs->toArray());
                }
                
    		}


    		//记住密码
    		cookie("loginName", $loginName, 3600*24*90);
    		
    		session('WST_STORE',$rs);
    		
    		return WSTReturn("登录成功","1");
    	
    	}
    	return WSTReturn("用户不存在");
    }
	/**
	* 获取各订单状态数、未读消息数、账户安全等级
	*/ 
	function getStatusNum(){
		$userId = (int)session('WST_STORE.userId');
		$data = [];
		// 用户消息
	    $data['message'] = Db::name('messages')->where(['receiveUserId'=>$userId,'msgStatus'=>0,'dataFlag'=>1])->count();
		//获取用户订单状态
	    $data['waitPay'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>-2,'dataFlag'=>1])->count();
	    $data['waitReceive'] = Db::name('orders')->where([['orderStatus','in',[0,1]],['userId','=',$userId],['dataFlag','=',1]])->count();
	    $data['received'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>2,'dataFlag'=>1])->count();
	    $data['waitAppr'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>2,'isAppraise'=>0,'dataFlag'=>1])->count();
	    // 账户安全等级
	    $level = 1;
	    $users = $this->where(['userId'=>$userId])->field('userPhone,userEmail')->find();
	    if(!empty($users['userPhone']))++$level;
	    if(!empty($users['userEmail']))++$level;
	    $data['level'] = $level;
	    //关注商品
	    $data['gfavorite'] = Db::name('favorites')->where(['userId'=>$userId,'favoriteType'=>0])->count();
	    return $data;
	}
}
