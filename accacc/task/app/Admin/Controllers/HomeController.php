<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller {
	public function index() {
		return Admin::content ( function (Content $content) {
			
			$content->header ( 'Dashboard' );
			$content->description ( 'Description...' );
			
			// $content->row(Dashboard::title());
			
			$content->row ( function (Row $row) {
				
				$now = date ( "Y-m-d 00:00:00", strtotime ( "-1 week Monday" ) );
				$users = DB::table ( 'users' )->select ( DB::raw ( 'count(*) as total' ) )->where ( 'created_at', '>', $now )->first ();
				$feed_subs = DB::table ( 'feed_subs' )->select ( DB::raw ( 'count(*) as total' ) )->where ( 'created_at', '>', $now )->first ();
				$articles = DB::table ( 'articles' )->select ( DB::raw ( 'count(*) as total' ) )->where ( 'created_at', '>', $now )->first ();
				$pomos = DB::table ( 'pomos' )->select ( DB::raw ( 'count(*) as total' ) )->where ( 'created_at', '>', $now )->first ();
				
				$userInfoBox = new InfoBox ( '最近一周新增用户', 'users', 'aqua', '/admin/users', $users->total );
				$feedSubInfoBox = new InfoBox ( '最近一周新增订阅', 'feed', 'green', '/admin/feedsubs', $feed_subs->total );
				$articleInfoBox = new InfoBox ( '最近一周新增文章', 'book', 'yellow-gradient', '/admin/articles', $articles->total );
				$pomoInfoBox = new InfoBox ( '最近一周新增番茄', 'tasks', 'light-blue', '/admin/pomos', $pomos->total );
				
				$row->column ( 3, $userInfoBox );
				$row->column ( 3, $feedSubInfoBox );
				$row->column ( 3, $articleInfoBox );
				$row->column ( 3, $pomoInfoBox );
				
				// $row->column(4, function (Column $column) {
				// $column->append(Dashboard::extensions());
				// });
			} );
		} );
	}
}
