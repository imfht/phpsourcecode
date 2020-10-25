<?php

namespace App\Http\Controllers;

use App\Services\PomoService;
use Illuminate\Http\Request;

/**
 * 首页控制器
 *
 * @author edison.an
 *        
 */
class IndexController extends Controller {
	
	/**
	 * PomoService 实例.
	 *
	 * @var PomoService
	 */
	protected $pomoService;
	
	/**
	 * 构造方法
	 *
	 * @param PomoService $pomoService        	
	 *
	 * @return void
	 */
	public function __construct(PomoService $pomoService) {
		$this->middleware ( 'auth' );
		
		$this->pomoService = $pomoService;
	}
	
	/**
	 * 首页信息展示
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		// 获取当前活动信息
		$currentPomoInfo = $this->pomoService->getCurrentPomoInfo ( $request->user () );
		
		// 标题栏相关提示
		$tipInfo = $this->pomoService->getTipInfo ( $currentPomoInfo ['current_pomo_status'] );
		
		return view ( 'index.index', array_merge ( $currentPomoInfo, $tipInfo ) );
	}
}
