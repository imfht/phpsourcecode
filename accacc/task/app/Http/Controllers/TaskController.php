<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorCodeUtil;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskTagMap;
use App\Models\Thing;
use App\Services\TaskService;
use App\Services\GoalService;
use Illuminate\Http\Request;
use App\Services\TagService;

/**
 * 待办事项控制器
 *
 * @author edison.an
 *        
 */
class TaskController extends Controller {
	
	/**
	 * The task repository instance.
	 *
	 * @var TaskRepository
	 */
	protected $tasks;
	protected $goals;
	protected $tags;
	
	/**
	 * Create a new controller instance.
	 *
	 * @param TaskRepository $tasks        	
	 * @return void
	 */
	public function __construct(TaskService $tasks, GoalService $goals, TagService $tags) {
		$this->middleware ( 'auth', [ 
				'except' => [ 
						'ics' 
				] 
		] );
		
		$this->tasks = $tasks;
		$this->goals = $goals;
		$this->tags = $tags;
	}
	
	/**
	 * 首页.
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		if ($request->has ( 'status' )) {
			$need_page = $request->has ( 'need_page' ) ? true : false;
			$tasks = $this->tasks->forUserByStatus ( $request->user (), $request->status, $need_page, $request->has ( "mode" ) ? $request->mode : '' );
		} else {
			$tasks = $this->tasks->forUser ( $request->user (), $needPage = true );
		}
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE, $tasks );
			return response ( $resp );
		} else {
			return view ( 'tasks.index', [ 
					'tasks' => $tasks 
			] );
		}
	}
	public function getAllList(Request $request) {
		$tasks = $this->tasks->forUserByStatus ( $request->user (), $request->status, false, $request->mode );
		
		// 组装子待办
		$temp = '';
		foreach ( $tasks as $task ) {
			if ($task->parent_task_id != null) {
				$temp [$task->parent_task_id] [] = $task;
			}
		}
		
		// 格式化待办顺序
		$format_tasks = '';
		foreach ( $tasks as $task ) {
			if ($task->parent_task_id == null) {
				$format_tasks [] = $task;
				if (isset ( $temp [$task->id] )) {
					foreach ( $temp [$task->id] as $val ) {
						$format_tasks [] = $val;
					}
				}
			}
		}
		
		$resp = $this->responseJson ( self::OK_CODE, $format_tasks );
		return response ( $resp );
	}
	public function priority(Request $request) {
		$models = $this->tasks->forUserByStatus ( $request->user (), 1 );
		$tasks = array (
				1 => array (),
				2 => array (),
				3 => array (),
				4 => array () 
		);
		foreach ( $models as $model ) {
			$tasks [$model->priority] [] = $model;
		}
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE, $tasks );
			return response ( $resp );
		} else {
			return view ( 'tasks.priority', [ 
					'tasks' => $tasks 
			] );
		}
	}
	
	/**
	 * 创建.
	 *
	 * @param Request $request        	
	 */
	public function store(Request $request) {
		$this->validate ( $request, [ 
				'name' => 'required|max:255',
				'remindtime' => 'nullable|date_format:Y-m-d H:i:s',
				'deadline' => 'nullable|date_format:Y-m-d H:i:s' 
		] );
		
		$params = array ();
		$params ['name'] = $request->name;
		$params ['mode'] = $request->mode;
		
		if ($request->has ( 'priority' ) && in_array ( $request->priority, array (
				1,
				2,
				3,
				4 
		) )) {
			$params ['priority'] = $request->priority;
		}
		
		if ($request->has ( 'remindtime' ) && strtotime ( $request->remindtime ) > time ()) {
			$params ['remindtime'] = $request->remindtime;
		}
		
		if ($request->has ( 'deadline' ) && strtotime ( $request->deadline ) > time ()) {
			$params ['deadline'] = $request->deadline;
		}
		
		if ($request->has ( 'parent_task_id' )) {
			$parent_task = Task::where ( 'user_id', $request->user ()->id )->where ( 'id', $request->parent_task_id )->first ();
			if (! empty ( $parent_task )) {
				$params ['parent_task_id'] = $request->parent_task_id;
			}
		}
		
		if (isset ( $request->goal_id )) {
			$goal = $this->goals->forGoalId ( $request->user (), $request->goal_id );
			if (! empty ( $goal )) {
				$params ['goal_id'] = $request->goal_id;
			}
		}
		
		$task = $request->user ()->tasks ()->create ( $params );
		
		preg_match_all ( '/#(.*?)#/i', $request->name, $match );
		foreach ( $match [0] as $item ) {
			$tag_name = trim ( $item, '#' );
			if (empty ( $tag_name )) {
				continue;
			}
			
			$tag = $this->tags->forTagName ( $tag_name );
			if (empty ( $tag )) {
				$tag = Tag::create ( array (
						'name' => $tag_name 
				) );
			}
			
			$taskNote = new TaskTagMap ();
			$taskNote->create ( array (
					'tag_id' => $tag->id,
					'task_id' => $task->id 
			) );
		}
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE, $task );
			return response ( $resp );
		} else {
			return redirect ( '/index' );
		}
	}
	
	/**
	 * 删除.
	 *
	 * @param Request $request        	
	 * @param Task $task        	
	 */
	public function destroy(Request $request, Task $task) {
		$this->authorize ( 'destroy', $task );
		
		$params = array ();
		
		if ($request->type == 'finish') {
			$params ['status'] = 2;
			
			$thing = new Thing ();
			$thing->user_id = $request->user ()->id;
			$thing->type = 2;
			$thing->name = $task->name;
			$thing->start_time = date ( 'Y-m-d H:i:s' );
			$thing->save ();
		} else {
			$params ['status'] = 3;
		}
		$flag = $task->update ( $params );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/index' )->with ( 'message', '操作成功!' );
		}
	}
	
	/**
	 * 更新
	 *
	 * @param Request $request        	
	 * @param Task $task        	
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function update(Request $request, Task $task) {
		$this->authorize ( 'destroy', $task );
		
		if ($request->method () == 'GET') {
			return view ( 'tasks.update', array (
					'task' => $task 
			) );
		}
		
		$flag = $task->update ( $request->all () );
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/index' )->with ( 'message', '操作成功!' );
		}
	}
}
