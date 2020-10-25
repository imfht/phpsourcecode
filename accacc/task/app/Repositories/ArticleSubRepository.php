<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\ArticleSub;
use Illuminate\Support\Facades\DB;

class ArticleSubRepository {
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user) {
		return ArticleSub::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'asc' )->get ();
	}
	
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, string $status, $needPage = false, $pageCount = 30) {
		// $article = DB::table('article_subs')->join('articles', 'article_subs.article_id', '=', 'articles.id')
		// ->join('feeds', 'articles.feed_id', '=', 'feeds.id')->where('article_subs.status',$status)->where('article_subs.user_id', $user->id)
		// ->orderBy('article_subs.updated_at', 'desc')->limit($pageCount)->get();
		
		// return $article;
		$article = ArticleSub::with ( 'article.feed' )->where ( 'user_id', $user->id )->where ( 'status', $status )->orderBy ( 'id', 'desc' );
		
		if ($needPage) {
			return $article->simplePaginate ( $pageCount );
		} else {
			return $article->get ();
		}
	}
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByCategoryStatusFeedId(User $user, string $status, $category_id, $needPage = false, $pageCount = 30) {
		$feedsubs = DB::table ( 'feed_subs' )->select ( 'feed_id' )->where ( 'category_id', $category_id )->where ( 'status', 1 )->get ();
		
		$feedId_arr = array ();
		foreach ( $feedsubs as $feedsub ) {
			$feedId_arr [] = $feedsub->feed_id;
		}
		
		$article = ArticleSub::with ( 'article.feed' )->where ( 'user_id', $user->id )->whereIn ( 'feed_id', $feedId_arr )->where ( 'status', $status );
		
		// $article = ArticleSub::with('article.feed')->where('user_id', $user->id)
		// ->whereIn('feed_id', function($query) use($category_id){
		// \Log::info('sub query start:'.time());
		// $query->select('feed_id')
		// ->from('feed_subs')
		// ->where('category_id', $category_id)
		// ->where('status', 1);
		// \Log::info('sub query end:'.time());
		// })
		// ->where('status',$status);
		/*
		 * $article = \DB::table('article_subs')->with('articles')
		 * ->where(['article_subs.user_id'=>$user->id])
		 * ->where(['article_subs.status'=>$status])
		 * ->join('articles', 'articles.id', '=', 'article_subs.article_id')
		 * ->leftJoin("feed_subs",'feed_subs.feed_id','=','article_subs.feed_id')
		 * ->where(['feed_subs.category_id'=>$category_id])
		 * ->where(['feed_subs.status'=>1]);
		 */
		
		if ($needPage) {
			return $article->paginate ( $pageCount );
		} else {
			return $article->get ();
		}
	}
	public function forUserByStatusFeedId(User $user, string $status, $feedId, $needPage = false, $pageCount = 30) {
		$article = ArticleSub::with ( 'article.feed' )->where ( 'user_id', $user->id )->where ( 'status', $status )->where ( 'feed_id', $feedId )->orderBy ( 'updated_at', 'desc' );
		if ($needPage) {
			return $article->paginate ( $pageCount );
		} else {
			return $article->get ();
		}
	}
	public function forUserByFeedId(User $user, $feedId, $needPage = false, $pageCount = 30) {
		$article = ArticleSub::with ( 'article.feed' )->where ( 'user_id', $user->id )->where ( 'feed_id', $feedId )->orderBy ( 'updated_at', 'desc' );
		if ($needPage) {
			return $article->simplePaginate ( $pageCount );
		} else {
			return $article->get ();
		}
	}
	public function getRecentPublishList(User $user, string $status, $start_time, $end_time, $limit) {
		return ArticleSub::with ( 'article.feed' )->where ( 'user_id', $user->id )->where ( 'status', $status )->where ( 'published', '<', $end_time )->where ( 'published', '>', $start_time )->orderBy ( 'feed_id' )->limit ( $limit )->get ();
	}
}
