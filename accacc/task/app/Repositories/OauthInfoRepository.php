<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\OauthInfo;

/**
 *
 * @author edison.an
 *        
 */
class OauthInfoRepository {
	/**
	 *
	 * @param User $user        	
	 * @param unknown $needPage        	
	 */
	public function forUser(User $user, $needPage) {
		$oauth_info = OauthInfo::where ( 'user_id', $user->id )->orderBy ( 'updated_at', 'desc' );
		if ($needPage) {
			return $oauth_info->paginate ( 50 );
		} else {
			return $oauth_info->get ();
		}
	}
	
	/**
	 *
	 * @param string $third_uid        	
	 * @param string $driver        	
	 */
	public function forByThirdUidAndDriver(string $third_uid, string $driver) {
		return OauthInfo::where ( 'third_uid', $third_uid )->where ( 'driver', $driver )->orderBy ( 'updated_at', 'desc' )->first ();
	}
}
