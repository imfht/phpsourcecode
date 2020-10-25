<?php

namespace App\Services;

use App\Models\OauthInfo;
use App\Models\User;

/**
 * 账号管理业务逻辑
 *
 * @author edison.an
 *        
 */
class AccountService {
	
	/**
	 * 构造方法
	 *
	 * @return void
	 */
	public function __construct() {
	}
	
	/**
	 * 获取某用户Oauth账户信息
	 *
	 * @param User $user        	
	 * @return NULL[][]
	 */
	public function getOauthInfos(User $user) {
		$oauths = array (
				'github' => array (),
				'weibo' => array () 
		);
		
		$oauthInfos = OauthInfo::where ( 'user_id', $user->id )->orderBy ( 'updated_at', 'desc' )->get ();
		foreach ( $oauthInfos as $oauthInfo ) {
			$oauths [$oauthInfo->driver] = array (
					'expire' => $oauthInfo->expire 
			);
		}
		
		return $oauths;
	}
	
	/**
	 * 根据第三方用户信息和类型获取Oatuth账户信息
	 *
	 * @param string $thirdUid        	
	 * @param string $driver        	
	 */
	public function forByThirdUidAndDriver(string $thirdUid, string $driver) {
		return OauthInfo::where ( 'third_uid', $thirdUid )->where ( 'driver', $driver )->orderBy ( 'updated_at', 'desc' )->first ();
	}
}
