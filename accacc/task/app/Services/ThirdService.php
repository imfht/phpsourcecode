<?php

namespace App\Services;

use App\Http\Utils\OAuth1\OAuth;
use App\Http\Utils\OAuth1\FFClient;
use Illuminate\Support\Facades\Session;
use App\Models\Third;
use App\Http\Utils\ErrorCodeUtil;

/**
 * 第三方服务业务逻辑
 *
 * @author edison.an
 *        
 */
class ThirdService {
	
	/**
	 */
	public function __construct() {
	}
	
	/**
	 *
	 * @param unknown $request        	
	 * @return string
	 */
	public function fanfouRequest($request) {
		$oauth = new OAuth ( config ( "services.fanfou.client_id" ), config ( "services.fanfou.client_secret" ) );
		
		$keys = $oauth->getRequestToken ();
		$url = $oauth->getAuthorizeURL ( $keys ['oauth_token'], false, config ( "services.fanfou.redirect" ) );
		
		$request->session ()->put ( 'thirdFanfouTempKeys', $keys );
		
		return $url;
	}
	
	/**
	 *
	 * @param unknown $request        	
	 */
	public function fanfouCallback($request) {
		$oauth = new OAuth ( config ( "services.fanfou.client_id" ), config ( "services.fanfou.client_secret" ), $temp ['oauth_token'], $temp ['oauth_token_secret'] );
		
		// 获取access_token
		$tempKeys = $request->session ()->get ( 'thirdFanfouTempKeys' );
		$lastKey = $oauth->getAccessToken ( $tempKeys ['oauth_token'] );
		
		// 创造一个新的请求
		$ffClient = new FFClient ( config ( "services.fanfou.client_id" ), config ( "services.fanfou.client_secret" ), $lastKey ['oauth_token'], $lastKey ['oauth_token_secret'] );
		$result = $ffClient->verify_credentials ();
		$result_arr = json_decode ( $result, true );
		
		$third = Third::where ( 'user_id', $request->user ()->id )->where ( 'third_id', $result_arr ['id'] )->where ( 'source', Third::SOURCE_FANFOU )->orderBy ( 'created_at', 'asc' )->first ();
		if (empty ( $third )) {
			$request->user ()->thirds ()->create ( [ 
					'third_id' => $result_arr ['id'],
					'third_name' => $result_arr ['name'],
					'token' => $last_key ['oauth_token'],
					'token_value' => $last_key ['oauth_token'],
					'token_secret' => $last_key ['oauth_token_secret'],
					'source' => 'fanfou' 
			] );
		} else {
			$third->update ( [ 
					'third_name' => $result_arr ['name'],
					'token' => $last_key ['oauth_token'],
					'token_value' => $last_key ['oauth_token'],
					'token_secret' => $last_key ['oauth_token_secret'] 
			] );
		}
	}
	
	/**
	 *
	 * @param Request $request        	
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function testFave($user, $message) {
		$third = Third::where ( 'user_id', $user->id )->where ( 'source', Third::SOURCE_FANFOU )->orderBy ( 'created_at', 'asc' )->first ();
		if (empty ( $third )) {
			throw new \Exception ( ErrorCodeUtil::getMessage ( ErrorCodeUtil::MESSAGE_THIRD_NOT_EXSIT ), ErrorCodeUtil::MESSAGE_THIRD_NOT_EXSIT );
		}
		return $this->fanfouFave ( $message, $third ['token_value'], $third ['token_secret'] );
	}
	
	/**
	 *
	 * @param unknown $third        	
	 * @param unknown $message        	
	 */
	private function fanfouFave($message, $tokenValue, $tokenSecret) {
		$ffClient = new FFClient ( config ( "services.fanfou.client_id" ), config ( "services.fanfou.client_secret" ), $tokenValue, $tokenSecret );
		if (is_array ( $message )) {
			foreach ( $message as $msg ) {
				$result = $ffClient->update ( $msg );
			}
		} else {
			$result = $ffClient->update ( $message );
		}
		return $result;
	}
	
	/**
	 *
	 * @return NULL
	 */
	public function sceduleFanfouFave() {
		$third = Third::where ( 'third_id', env ( 'FANFOU_ID' ) )->first ();
		if (empty ( $third )) {
			Log::info ( "[__CLASS__->__FUNCTION__]:not third info|{env('FANFOU_ID')}" );
			return null;
		}
		
		$message = env ( 'FANFOU_MESSAGE' );
		$message_arr = explode ( '|', $message );
		
		return $this->fanfouFave ( $message_arr, $third ['token_value'], $third ['token_secret'] );
	}
}
