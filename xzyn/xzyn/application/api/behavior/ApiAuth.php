<?php
// 应用行为扩展定义文件

namespace app\api\behavior;

use app\common\model\ApiApp as ApiApps;
use app\common\model\ApiList as ApiLists;
use app\common\model\TokenUser as TokenUsers;
use app\common\model\ApiApptoken;
use expand\ApiReturn;

// 处理Api接入认证
class ApiAuth {

	//api 信息
    private $apiInfo;

    public function run($params) {
    	$hash = input('hash');
		$this->apiInfo = ApiLists::get( ['hash'=>$hash,'status'=>1] );
		if( empty($this->apiInfo) ){
			return ApiReturn::r('-1');	//hash参数无效
		}
        if ($this->apiInfo['accessToken'] && !$this->apiInfo['isTest']) {	//如果是测试模式,忽略验证AppToken令牌
            $accessRes = $this->checkAccessToken();	//AppToken令牌
            if ($accessRes) {
                return $accessRes;
            }
        }
		if ($this->apiInfo['needLogin'] && !$this->apiInfo['isTest']) {	//如果是测试模式,忽略验证用户UserToken令牌
	        $loginRes = $this->checkLogin();	//验证用户UserToken令牌
	        if ($loginRes) {
	            return $loginRes;
	        }
		}
		$apiInfo = cache('apiInfo_'.$hash);	//接口信息
		if( empty($apiInfo) ){
			cache('apiInfo_'.$hash,$this->apiInfo,7200);	//接口信息
		}
    }

    /**
     * Api接口合法性检测
     */
    private function checkAccessToken() {
		$header = request()->header();
		if( empty($header['apptoken']) && !isset($header['apptoken']) ){
			return ApiReturn::r('-3');	// 缺少AppToken令牌
		}else{
			$apptoken = ApiApptoken::get( ['apptoken'=>$header['apptoken']] );
			if( empty($apptoken) ){
				return ApiReturn::r('-4');	// AppToken令牌无效
			}else{
				if( $apptoken['ApiApp']['app_status'] != 1 ){
					return ApiReturn::r('-230');	// 应用已禁用
				}else if( $apptoken['app_tokenTime'] < time() ){
					return ApiReturn::r('-5');	// AppToken令牌过期
				}
			}
		}
    }

    /**
     * 检测用户登录情况
     */
    private function checkLogin() {
		$header = request()->header();
        if ( $this->apiInfo['needLogin'] == 1 ) {
            if ( empty($header['usertoken']) && !isset($header['usertoken']) ) {
            	return ApiReturn::r('-6');	// 缺少UserToken令牌
            }else{
				$db_user_token = TokenUsers::get( ['token'=>$header['usertoken']] );	//数据库token
				if( empty($db_user_token) ){
					return ApiReturn::r('-7');	// UserToken令牌无效
				}else{
					if( $db_user_token['token_time'] < time() ){
						return ApiReturn::r('-8');	// UserToken令牌过期
					}
				}
            }
        }
	}


}
