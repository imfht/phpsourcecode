<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal;
use App\Services\GoalService;
use App\Http\Utils\ErrorCodeUtil;

/**
 * 目标控制器
 *
 * @author edison.an
 *        
 */
class GoalController extends Controller {
	
	/**
	 * GoalService 实例
	 *
	 * @var GoalService
	 */
	protected $goalService;
	
	/**
	 * 构造方法
	 *
	 * @param GoalService $goalService        	
	 * @return void
	 */
	public function __construct(GoalService $goalService) {
		$this->middleware ( 'auth' );
		
		$this->goalService = $goalService;
	}
	
	/**
	 * 首页
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		$status = $request->status;
		if (empty ( $status ) || ! in_array ( $status, array (
				1,
				2 
		) )) {
			$status = 1;
		}
		return view ( 'goals.index', [ 
				'goals' => $this->goalService->forUserByStatus ( $request->user (), $status, $needPage = true ) 
		] );
	}
	
	/**
	 * 新建目标
	 *
	 * @param Request $request        	
	 */
	public function store(Request $request) {
		$this->validate ( $request, [ 
				'name' => 'required|max:255' 
		] );
		
		$params = array ();
		$params ['name'] = $request->name;
		
		$request->user ()->goals ()->create ( $params );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/goals' )->with ( 'message', 'IT WORKS!' );
		}
	}
	
	/**
	 * 删除目标
	 *
	 * @param Request $request        	
	 * @param Goal $goal        	
	 */
	public function destroy(Request $request, Goal $goal) {
		$this->authorize ( 'destroy', $goal );
		
		$params = array ();
		
		if ($request->type == 'finish') {
			$params ['status'] = 2;
		} else {
			$params ['status'] = 3;
		}
		$flag = $goal->update ( $params );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/goals' )->with ( 'message', 'IT WORKS!' );
		}
	}
	
	/**
	 * 更新目标
	 *
	 * @param Request $request        	
	 * @param Goal $goal        	
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function update(Request $request, Goal $goal) {
		$this->authorize ( 'destroy', $goal );
		
		if ($request->method () == 'GET') {
			return view ( 'goals.update', array (
					'goal' => $goal 
			) );
		}
		
		$flag = $goal->update ( $request->all () );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/goals' )->with ( 'message', 'IT WORKS!' );
		}
	}
}
