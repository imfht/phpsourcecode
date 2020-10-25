<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorCodeUtil;
use App\Services\ThirdService;
use Illuminate\Http\Request;

/**
 * 第三方服务控制器
 *
 * @author edison.an
 *        
 */
class ThirdController extends Controller {
	
	/**
	 * ThirdService 实例.
	 *
	 * @var ThirdService
	 */
	protected $thirdService;
	
	/**
	 * 构造方法
	 *
	 * @param ThirdService $third        	
	 * @return void
	 */
	public function __construct(ThirdService $thirdService) {
		$this->middleware ( 'auth' );
		
		$this->thirdService = $thirdService;
	}
	
	/**
	 * 首页
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		echo '<a href="/third/fanfouIndex">Go Fanfou!</a><br/><a href="/third/twitterIndex">Go twitter!</a>';
		exit ();
	}
	
	/**
	 * 获取饭否requestToken
	 *
	 * @param Request $request        	
	 * @return Response
	 */
	public function fanfouIndex(Request $request) {
		$url = $this->thirdService->fanfouRequest ( $request );
		return redirect ( ( string ) $url );
	}
	
	/**
	 * 饭否回调
	 *
	 * @param Request $request        	
	 * @return Response
	 */
	public function fanfouCallback(Request $request) {
		$this->thirdService->fanfouCallback ( $request );
		echo '<a href="/">处理结束返回首页</a>';
		exit ();
	}
	
	/**
	 * testFave
	 *
	 * @param Request $request        	
	 */
	public function testFave(Request $request) {
		try {
			echo $this->thirdService->testFave ( $request->user (), $message = 'test!!!robot dog!!!' );
			exit ();
		} catch ( Exception $e ) {
			if ($e->getCode () == ErrorCodeUtil::THIRD_NOT_EXSIT) {
				return redirect ( 'third/fanfouIndex' )->with ( 'message', 'IT WORKS!' );
			} else {
				echo 'unknown error';
				exit ();
			}
		}
	}
	
	/**
	 * twitter request token
	 *
	 * @param Request $request        	
	 * @return Response
	 */
	public function twitterIndex(Request $request) {
	}
	
	/**
	 * twitter callback
	 *
	 * @param Request $request        	
	 * @return Response
	 */
	public function twitterCallback(Request $request) {
	}
}
