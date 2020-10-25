<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorCodeUtil;
use App\Models\Thing;
use App\Services\ThingService;
use Illuminate\Http\Request;

/**
 * 记事控制器
 *
 * @author edison.an
 *        
 */
class ThingController extends Controller {
	
	/**
	 * ThingService 实例.
	 *
	 * @var ThingService
	 */
	protected $thingService;
	
	/**
	 * 构造方法
	 *
	 * @param ThingService $thingService        	
	 * @return void
	 */
	public function __construct(ThingService $thingService) {
		$this->middleware ( 'auth' );
		
		$this->thingService = $thingService;
	}
	
	/**
	 * 首页
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		return view ( 'things.index', [ 
				'things' => $this->thingService->forUser ( $request->user (), $needPage = true ) 
		] );
	}
	
	/**
	 * 新建记事
	 *
	 * @param Request $request        	
	 */
	public function store(Request $request) {
		$this->validate ( $request, [ 
				'name' => 'required|max:255',
				'start_time' => 'date_format:Y-m-d H:i:s',
				'end_time' => 'date_format:Y-m-d H:i:s' 
		] );
		
		$params = array ();
		$params ['name'] = $request->name;
		
		if ($request->has ( 'start_time' )) {
			$params ['start_time'] = $request->start_time;
		} else {
			$params ['start_time'] = date ( 'Y-m-d H:i:s' );
		}
		
		if ($request->has ( 'end_time' )) {
			$params ['end_time'] = $request->end_time;
			if (strtotime ( $params ['start_time'] ) > strtotime ( $params ['end_time'] )) {
				return redirect ( '/things' )->with ( 'message', 'Error End Time:' . $params ['end_time'] );
			}
		}
		
		$thing = $request->user ()->things ()->create ( $params );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/things' )->with ( 'message', 'IT WORKS!' );
		}
	}
	
	/**
	 * 删除记事
	 *
	 * @param Request $request        	
	 * @param Thing $thing        	
	 */
	public function destroy(Request $request, Thing $thing) {
		$this->authorize ( 'destroy', $thing );
		
		$thing->delete ();
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/things' )->with ( 'message', 'IT WORKS!' );
		}
	}
	
	/**
	 * 更新记事
	 *
	 * @param Request $request        	
	 * @param Thing $thing        	
	 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	public function update(Request $request, Thing $thing) {
		$this->authorize ( 'destroy', $thing );
		
		$flag = $thing->update ( $request->all () );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/things' )->with ( 'message', 'IT WORKS!' );
		}
	}
}
