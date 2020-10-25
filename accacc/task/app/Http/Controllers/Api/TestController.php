<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pomo;
use App\Models\Thing;
use App\Models\User;
use App\Services\PomoService;
use Illuminate\Http\Request;

class TestController extends Controller {
	
	/**
	 * The pomo servie instance.
	 *
	 * @var PomoService
	 */
	protected $pomoService;
	
	/**
	 * Create a new controller instance.
	 *
	 * @param PomoService $pomoService        	
	 * @return void
	 */
	public function __construct(PomoService $pomoService) {
		$this->pomoService = $pomoService;
	}
	public function index(Request $request) {
		$user = new User ();
		$user->id = 1;
		if ($request->has ( 'type' )) {
			$pomos = Pomo::where ( 'user_id', $user->id )->where ( 'status', 2 )->where ( 'created_at', '>', date ( 'Ymd' ) )->orderBy ( 'created_at', 'desc' )->get ();
		} else {
			$pomos = $pomo = Pomo::where ( 'user_id', $user->id )->where ( 'status', 2 )->orderBy ( 'updated_at', 'desc' )->paginate ( 50 );
		}
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE, $pomos );
			return response ( $resp );
		} else {
			return view ( 'pomos.index', [ 
					'pomos' => $pomos 
			] );
		}
	}
	public function info(Request $request) {
		$user = new User ();
		$user->id = 1;
		
		$currentPomoInfo = $this->pomoService->getCurrentPomoInfo ( $user );
		
		$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE, $currentPomoInfo );
		return response ( $resp );
	}
	
	/**
	 * Start a new pomo.
	 *
	 * @param Request $request        	
	 */
	public function start(Request $request) {
		$user = new User ();
		$user->id = 1;
		$request->session ()->forget ( 'rest_start_time' );
		
		$pomoInfo = $this->pomoService->startPomo ( $user );
		
		$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE, $pomoInfo );
		return response ( $resp );
	}
	
	/**
	 * Discard a new pomo.
	 *
	 * @param Request $request        	
	 */
	public function discard(Request $request, Pomo $pomo) {
		$user = new User ();
		$user->id = 1;
		if ($pomo->exists == false) {
			$request->session ()->forget ( 'rest_start_time' );
		} else {
			// 判断是否有权限，并置失败
			$this->authorize ( 'destroy', $pomo );
			$pomo->update ( array (
					'status' => 3 
			) );
		}
		
		$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
		return response ( $resp );
	}
	
	/**
	 *
	 * @param Request $request        	
	 */
	public function store(Request $request, Pomo $pomo) {
		$user = User::where ( 'id', 1 )->first ();
		$setting = $user->setting;
		$pomo_time = isset ( $setting->pomo_time ) && ! empty ( $setting->pomo_time ) ? $setting->pomo_time * 60 : Pomo::DEFAULT_INTERVAL;
		
		if (time () > strtotime ( $pomo->created_at ) + $pomo_time) {
			$this->validate ( $request, [ 
					'name' => 'required|max:255' 
			] );
			
			// $this->authorize ( 'destroy', $pomo );
			$pomo->update ( [ 
					'name' => $request->name,
					'status' => 2 
			] );
			
			$thing = new Thing ();
			$thing->user_id = $user->id;
			$thing->type = 3;
			$thing->name = $pomo->name;
			$thing->end_time = $pomo->created_at;
			$thing->start_time = date ( 'Y-m-d H:i:s' );
			$thing->save ();
			
			// auto resting
			$request->session ()->put ( 'rest_start_time', time () );
		}
		
		$currentPomoInfo = $this->pomoService->getCurrentPomoInfo ( $user );
		$currentPomoInfo ['active_pomo'] = $pomo;
		$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE, $currentPomoInfo );
		return response ( $resp );
	}
	
	/**
	 * Destroy the given task.
	 *
	 * @param Request $request        	
	 * @param Pomo $pomo        	
	 */
	public function destroy(Request $request, Pomo $pomo) {
		$user = new User ();
		$user->id = 1;
		$this->authorize ( 'destroy', $pomo );
		
		$pomo->delete ();
		
		$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
		return response ( $resp );
	}
}
