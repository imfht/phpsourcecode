<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\InfoBox;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {
	use ModelForm;
	
	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content ( function (Content $content) {
			
			$content->header ( 'header' );
			$content->description ( 'description' );
			
			$content->body ( $this->grid () );
		} );
	}
	
	/**
	 * Edit interface.
	 *
	 * @param
	 *        	$id
	 * @return Content
	 */
	public function edit($id) {
		return Admin::content ( function (Content $content) use ($id) {
			
			$content->header ( 'header' );
			$content->description ( 'description' );
			
			$content->body ( $this->form ()->edit ( $id ) );
		} );
	}
	
	/**
	 * Create interface.
	 *
	 * @return Content
	 */
	public function create() {
		return Admin::content ( function (Content $content) {
			
			$content->header ( 'header' );
			$content->description ( 'description' );
			
			$content->body ( $this->form () );
		} );
	}
	
	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid ( User::class, function (Grid $grid) {
			
			$grid->model ()->orderBy ( 'id', 'desc' );
			
			$grid->id ( 'ID' )->sortable ();
			
			$grid->name ()->display ( function ($name) {
				return "<a href='/admin/statistic?user_id={$this->id}'>$name</span>";
			} );
			$grid->email ();
			$grid->created_at ()->display ( function ($created_at) {
				return \App\Http\Utils\CommonUtil::prettyDate ( $created_at );
			} )->sortable ();
			// $grid->updated_at()->sortable();
			// $grid->last_login()->sortable();
			$grid->last_login ()->display ( function ($last_login) {
				return \App\Http\Utils\CommonUtil::prettyDate ( $last_login );
			} )->sortable ();
			
			$grid->column ( '活跃等级' )->display ( function () {
				$curr_time = time ();
				$created_at_time = strtotime ( $this->created_at );
				$last_login_time = strtotime ( $this->last_login );
				
				if ($curr_time - strtotime ( $this->created_at ) < 7 * 24 * 3600) {
					return '最近一周注册';
				} else if ($curr_time - $last_login_time < 7 * 24 * 3600) {
					return '最近一周活跃';
				} else {
					return $last_login_time - $created_at_time < 24 * 3600 ? '注册后不活跃' : '注册后曾活跃过';
				}
			} );
			
			$grid->disableActions ();
			$grid->disableCreation ();
			$grid->disableRowSelector ();
		} );
	}
	
	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form ( User::class, function (Form $form) {
			
			$form->display ( 'id', 'ID' );
			
			$form->display ( 'name', 'Name' );
			$form->display ( 'email', 'Email' );
			$form->display ( 'created_at', 'Created At' );
			$form->display ( 'updated_at', 'Updated At' );
			$form->display ( 'last_login', 'Last Login' );
		} );
	}
	public function statistic() {
		return Admin::content ( function (Content $content) {
			
			$content->header ( 'Dashboard' );
			$content->description ( 'Description...' );
			
			// $content->row(Dashboard::title());
			
			if (! isset ( $_GET ['user_id'] )) {
				echo 'error param no user_id!';
				die ();
			}
			
			$content->row ( function (Row $row) {
				$rows1 = array ();
				$rows2 = array ();
				$rows3 = array ();
				
				$user_id = $_GET ['user_id'];
				$user = DB::table ( 'users' )->where ( 'id', $user_id )->first ();
				
				$pomos = DB::table ( 'pomos' )->select ( 'status', DB::raw ( 'count(*) as total' ) )->where ( 'user_id', $user_id )->groupBy ( 'status' )->get ();
				foreach ( $pomos as $pomo ) {
					$rows1 [] = array (
							'Pomo ' . $pomo->status,
							$pomo->total 
					);
				}
				
				$tasks = DB::table ( 'tasks' )->select ( 'status', DB::raw ( 'count(*) as total' ) )->where ( 'user_id', $user_id )->groupBy ( 'status' )->get ();
				foreach ( $tasks as $task ) {
					$rows1 [] = array (
							'Task ' . $task->status,
							$task->total 
					);
				}
				
				$things = DB::table ( 'things' )->select ( 'type', DB::raw ( 'count(*) as total' ) )->where ( 'user_id', $user_id )->groupBy ( 'type' )->get ();
				foreach ( $things as $thing ) {
					$rows1 [] = array (
							'Thing ' . $thing->type,
							$thing->total 
					);
				}
				
				$minds = DB::table ( 'minds' )->select ( 'status', DB::raw ( 'count(*) as total' ) )->where ( 'user_id', $user_id )->groupBy ( 'status' )->get ();
				foreach ( $minds as $mind ) {
					$rows1 [] = array (
							'思维导图:' . $mind->status,
							$mind->total 
					);
				}
				
				$notes = DB::table ( 'notes' )->select ( 'status', DB::raw ( 'count(*) as total' ) )->where ( 'user_id', $user_id )->groupBy ( 'status' )->get ();
				foreach ( $notes as $note ) {
					$rows1 [] = array (
							'Note ' . $note->status,
							$note->total 
					);
				}
				
				$box1 = new Box ( '用户GTD内容统计', new Table ( array (), $rows1 ) );
				$box1->removable ();
				$box1->collapsable ();
				$box1->style ( 'info' );
				$box1->solid ();
				
				$feed_subs = DB::table ( 'feed_subs' )->select ( 'status', DB::raw ( 'count(*) as total' ) )->where ( 'user_id', $user_id )->groupBy ( 'status' )->get ();
				foreach ( $feed_subs as $feed_sub ) {
					$rows2 [] = array (
							'Feed Sub ' . $feed_sub->status,
							$feed_sub->total 
					);
				}
				
				$article_subs = DB::table ( 'articles' )->select ( 'status', DB::raw ( 'count(*) as total' ) )->where ( 'user_id', $user_id )->groupBy ( 'status' )->get ();
				foreach ( $article_subs as $article_sub ) {
					$rows2 [] = array (
							'Article Sub ' . $article_sub->status,
							$article_sub->total 
					);
				}
				
				$box2 = new Box ( '用户订阅内容统计', new Table ( array (), $rows2 ) );
				$box2->removable ();
				$box2->collapsable ();
				$box2->style ( 'info' );
				$box2->solid ();
				
				$rows3 [] = array (
						'账户名',
						$user->name 
				);
				$rows3 [] = array (
						'账户创建时间',
						$user->created_at 
				);
				$rows3 [] = array (
						'上次登录时间',
						$user->last_login 
				);
				
				$box3 = new Box ( '用户基础信息', new Table ( array (), $rows3 ) );
				$box3->removable ();
				$box3->collapsable ();
				$box3->style ( 'info' );
				$box3->solid ();
				
				$row->column ( 4, $box1 );
				$row->column ( 4, $box2 );
				$row->column ( 4, $box3 );
				
				// $row->column(4, function (Column $column) {
				// $column->append(Dashboard::extensions());
				// });
			} );
		} );
	}
}
