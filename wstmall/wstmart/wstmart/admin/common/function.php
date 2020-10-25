<?php
use think\Db;
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

/**
* 删除app端
*/
function delAppToken($userId){
	try{
		$prefix = config('database.prefix');
		
		// 删除app端的token
		$appTableName = $prefix."app_session";
		$rs = Db::query("SHOW TABLES like '{$appTableName}'");
		if(!empty($rs))Db::name('app_session')->where(['userId'=>$userId])->delete();

		// 删除小程序端的token
		$weAppTableName = $prefix."weapp_session";
		$rs = Db::query("SHOW TABLES like '{$weAppTableName}'");
		if(!empty($rs))Db::name('weapp_session')->where(['userId'=>$userId])->delete();
	}catch(\Exception $e){

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
 * 微信配置
 */
function WXAdmin(){
	$wechat = new \wechat\WSTWechat(WSTConf('CONF.wxAppId'),WSTConf('CONF.wxAppKey'));
	return $wechat;
}
