<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountService;

/**
 * 账户管理控制器
 *
 * @author edison.an
 *        
 */
class AccountController extends Controller {
	
	/**
	 * AccountService 实例.
	 *
	 * @var AccountService
	 */
	protected $accountService;
	
	/**
	 * 构造方法
	 *
	 * @param AccountService $accountService        	
	 * @return void
	 */
	public function __construct(AccountService $accountService) {
		$this->middleware ( 'auth' );
		
		$this->accountService = $accountService;
	}
	
	/**
	 * 用户Oauth账户信息列表
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		$oauths = $this->accountService->getOauthInfos ( $request->user () );
		return view ( 'accounts.index', [ 
				'oauths' => $oauths 
		] );
	}
}
