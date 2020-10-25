<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorCodeUtil;
use Illuminate\Http\Request;
use App\Models\Pomo;
use App\Models\Thing;
use App\Repositories\PomoRepository;
use App\Services\PomoService;

/**
 * 番茄工作法控制器
 *
 * @author edison.an
 *        
 */
class PomoController extends Controller {
	
	/**
	 * The pomo repository instance.
	 *
	 * @var PomoRepository
	 */
	protected $pomos;
	
	/**
	 * The pomo servie instance.
	 *
	 * @var PomoService
	 */
	protected $pomoService;
	
	/**
	 * Create a new controller instance.
	 *
	 * @param PomoRepository $pomos        	
	 * @return void
	 */
	public function __construct(PomoService $pomoService, PomoRepository $pomos) {
		$this->middleware ( 'auth', [ 
				'except' => [ 
						'welcome' 
				] 
		] );
		$this->pomoService = $pomoService;
		$this->pomos = $pomos;
	}
	
	/**
	 * 欢迎页
	 *
	 * @param Request $request        	
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function welcome(Request $request) {
		return view ( 'pomos.welcome', [ ] );
	}
	
	/**
	 * 首页.
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		if ($request->has ( 'type' )) {
			$pomos = $this->pomos->forUserByTime ( $request->user (), date ( 'Ymd' ) );
		} else {
			$pomos = $this->pomos->forUserByStatus ( $request->user (), 2, $needPage = true );
		}
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE, $pomos );
			return response ( $resp );
		} else {
			return view ( 'pomos.index', [ 
					'pomos' => $pomos 
			] );
		}
	}
	
	/**
	 * 开始做番茄.
	 *
	 * @param Request $request        	
	 */
	public function start(Request $request) {
		$request->session ()->forget ( 'rest_start_time' );
		
		$pomoInfo = $this->pomoService->startPomo ( $request->user () );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE, $pomoInfo );
			return response ( $resp );
		} else {
			return redirect ( '/index' );
		}
	}
	
	/**
	 * 放弃番茄/休息.
	 *
	 * @param Request $request        	
	 */
	public function discard(Request $request, Pomo $pomo) {
		if ($pomo->exists == false) {
			$request->session ()->forget ( 'rest_start_time' );
		} else {
			// 判断是否有权限，并置失败
			$this->authorize ( 'destroy', $pomo );
			$pomo->update ( array (
					'status' => 3 
			) );
			$this->pomoService->clearpomonotify ( $request->user () );
		}
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/index' );
		}
	}
	
	/**
	 * 记录番茄
	 *
	 * @param Request $request        	
	 */
	public function store(Request $request, Pomo $pomo) {
		$setting = $request->user ()->setting;
		$pomo_time = isset ( $setting->pomo_time ) && ! empty ( $setting->pomo_time ) ? $setting->pomo_time * 60 : Pomo::DEFAULT_INTERVAL;
		
		if (time () > strtotime ( $pomo->created_at ) + $pomo_time) {
			$this->validate ( $request, [ 
					'name' => 'required|max:255' 
			] );
			
			$this->authorize ( 'destroy', $pomo );
			$pomo->update ( [ 
					'name' => $request->name,
					'status' => 2 
			] );
			
			$thing = new Thing ();
			$thing->user_id = $request->user ()->id;
			$thing->type = 3;
			$thing->name = $pomo->name;
			$thing->end_time = $pomo->created_at;
			$thing->start_time = date ( 'Y-m-d H:i:s' );
			$thing->save ();
			
			// auto resting
			$request->session ()->put ( 'rest_start_time', time () );
		}
		
		if ($request->ajax () || $request->wantsJson ()) {
			$currentPomoInfo = $this->pomoService->getCurrentPomoInfo ( $request->user () );
			$currentPomoInfo ['active_pomo'] = $pomo;
			$this->pomoService->pomonotify ( $request->user (), $currentPomoInfo ['current_pomo_status'] == Pomo::STATUS_PROCESSING ? '您已经完成了一个番茄，快来记录一下吧~' : '休息完成，快来开始下一个番茄吧~', $currentPomoInfo ['current_pomo_remain'] );
			$resp = $this->responseJson ( self::OK_CODE, $currentPomoInfo );
			return response ( $resp );
		} else {
			return redirect ( '/index' );
		}
	}
	
	/**
	 * 删除.
	 *
	 * @param Request $request        	
	 * @param Pomo $pomo        	
	 */
	public function destroy(Request $request, Pomo $pomo) {
		$this->authorize ( 'destroy', $pomo );
		
		$pomo->delete ();
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/index' )->with ( 'message', '操作成功!' );
		}
	}
	public function pomostatus(Request $request) {
		// 获取当前活动信息
		$currentPomoInfo = $this->pomoService->getCurrentPomoInfo ( $request->user () );
		$resp = $this->responseJson ( self::OK_CODE, $currentPomoInfo );
		return response ( $resp );
	}
}
