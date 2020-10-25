<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Feed;
use App\Models\Article;
use App\Models\FeedSub;
use App\Models\ArticleSub;
use ArandiLopez\Feed\Factories\FeedFactory; // use SimplePie to parse RSS feeds, see: https://github.com/arandilopez/laravel-feed-parser
use App\Http\Utils\OAuth1\FFClient;
use Illuminate\Support\Facades\Log;
use Exception;

class FeedRepository {
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user, $needPage = false) {
		$feed = Feed::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'asc' );
		
		if ($needPage) {
			return $feed->paginate ( 50 );
		} else {
			return $feed->get ();
		}
	}
	public function forIsRecommend($is_recommend, $needPage = false) {
		$feed = Feed::where ( 'is_recommend', $is_recommend )->orderBy ( 'recommend_order', 'desc' );
		
		if ($needPage) {
			return $feed->paginate ( 9 );
		} else {
			return $feed->get ();
		}
	}
	public function findByRecommendCategoryId($recommend_category_id, $needPage = false) {
		$feed = Feed::where ( 'recommend_category_id', $recommend_category_id )->orderBy ( 'recommend_order', 'desc' );
		
		if ($needPage) {
			return $feed->paginate ( 48 );
		} else {
			return $feed->get ();
		}
	}
	public function findByName($name, $needPage = false) {
		$feed = Feed::where ( 'feed_name', 'like', '%' . $name . '%' )->orderBy ( 'recommend_order', 'desc' );
		
		if ($needPage) {
			return $feed->paginate ( 48 );
		} else {
			return $feed->get ();
		}
	}
	
	/**
	 * get feed list by type and status
	 * 
	 * @param unknown $type        	
	 * @param unknown $status        	
	 */
	public function getListByActiveLevelStatus($active_level, $status) {
		return Feed::where ( 'status', $status )->where ( 'active_level', $active_level )->get ();
	}
	
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, string $status) {
		return Feed::where ( 'user_id', $user->id )->where ( 'status', $status )->get ();
	}
	
	/**
	 * Get goal for goal id.
	 *
	 * @param User $user        	
	 * @param int $goal_id        	
	 * @return Collection
	 */
	public function forFeedId(User $user, $feedId) {
		return Feed::where ( 'user_id', $user->id )->where ( 'id', $feedId )->get ();
	}
}
