<?php

namespace App\Services;

use App\Http\Utils\OAuth1\FFClient;
use App\Models\Article;
use App\Models\Category;
use App\Models\Feed;
use App\Models\FeedSub;
use App\Models\User;
use App\Models\ArticleSub;
use App\Repositories\FeedRepository;
use App\Repositories\FeedSubRepository;
use App\Repositories\ThirdRepository;
use ArandiLopez\Feed\Factories\FeedFactory;
use Celd\Opml\Importer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Repositories\CategoryRepository;

/**
 * FeedService订阅相关Service
 *
 * @author edison.an
 *        
 */
class FeedService {
	
	/**
	 * FeedSubRepository 实例 .
	 *
	 * @var FeedSubRepository
	 */
	protected $feedSubs;
	
	/**
	 * FeedRepository 实例 .
	 *
	 * @var FeedRepository
	 */
	protected $feeds;
	
	/**
	 * CategoryRepository 实例 .
	 *
	 * @var CategoryRepository
	 */
	protected $categorys;
	
	/**
	 * 创建Service
	 *
	 * @param FeedSubRepository $feedSubs        	
	 * @param FeedRepository $feeds        	
	 */
	public function __construct(FeedSubRepository $feedSubs, FeedRepository $feeds, CategoryRepository $categorys) {
		$this->feedSubs = $feedSubs;
		$this->feeds = $feeds;
		$this->categorys = $categorys;
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param int $status        	
	 * @param boolean $needPage        	
	 * @return \App\Repositories\Collection
	 */
	public function getFeedSubListByStatus(User $user, $status, $needPage = true) {
		return $this->feedSubs->forUserByStatus ( $user, $status, $needPage );
	}
	
	/**
	 *
	 * @param int $is_recommend        	
	 * @param boolean $needPage        	
	 * @return unknown
	 */
	public function getRecommendFeed($is_recommend, $needPage = true) {
		return $this->feeds->forIsRecommend ( $is_recommend, $needPage );
	}
	
	/**
	 *
	 * @param User $user        	
	 * @return array
	 */
	public function getNavInfo(User $user) {
		$categoryFeedInfos = DB::select ( 'select c.id as category_id,c.name as category_name,f.feed_id as feed_id,f.feed_name as feed_name,f.id as feed_sub_id from feed_subs f right join categories c on f.category_id = c.id where c.user_id = :user_id2 and f.user_id = :user_id  and f.status =1 order by c.category_order asc,f.feed_order asc', [ 
				':user_id' => $user->id,
				':user_id2' => $user->id 
		] );
		
		$navInfos = array ();
		foreach ( $categoryFeedInfos as $item ) {
			$navInfos [$item->category_id] ['category_info'] = array (
					'category_name' => $item->category_name,
					'category_id' => $item->category_id 
			);
			
			$feed = array (
					'feed_id' => $item->feed_id,
					'feed_name' => $item->feed_name,
					'feed_count' => isset ( $countsInfo [$item->feed_id] ) ? $countsInfo [$item->feed_id] : 0,
					'feed_sub_id' => $item->feed_sub_id 
			);
			
			$navInfos [$item->category_id] ['list'] [] = $feed;
		}
		
		if (count ( $navInfos ) == 0) {
			$category = $user->categorys ()->create ( [ 
					'name' => '未分类',
					'category_order' => 0 
			] );
			$navInfos [] = array (
					'category_info' => array (
							'category_name' => $category->name,
							'category_id' => $category->id 
					),
					'list' => array () 
			);
		}
		return $navInfos;
	}
	public function findByRecommendCategoryId($recommendCategoryId, $needPage = true) {
		return $this->feeds->findByRecommendCategoryId ( $recommendCategoryId );
	}
	public function findByName($name, $needPage = true) {
		return $feeds = $this->feeds->findByName ( $name, $needPage = true );
	}
	public function store(User $user, $storeParams) {
		$category = $this->categorys->forCategoryId ( $user, $storeParams ['category_id'] );
		if (empty ( $category )) {
			return false;
		}
		
		$feed = Feed::where ( 'url', $storeParams ['url'] )->first ();
		if (empty ( $feed )) {
			$feed = new Feed ();
			$feed->user_id = $user->id;
			$feed->feed_name = $storeParams ['feed_name'];
			$feed->url = $storeParams ['url'];
			$feed->category_id = $storeParams ['category_id'];
			$feed->sub_count = 1;
			$feed->save ();
		} else {
			$feedSub = $this->feedSubs->forUserByFeedId ( $user, $feed->id, 1 );
			
			if (! empty ( $feedSub )) {
				return false;
			}
			
			// 如果未锁定，那么更改Feed的名称
			if (empty ( $feed->recommend_name ) && $feed->name != $storeParams ['feed_name']) {
				$feed->feed_name = $storeParams ['feed_name'];
			}
			$feed->sub_count = $feed->sub_count + 1;
			$feed->save ();
		}
		
		$feedSub = $user->feedSubs ()->create ( [ 
				'status' => 1,
				'feed_id' => $feed->id,
				'feed_name' => $storeParams ['feed_name'],
				'category_id' => $storeParams ['category_id'] 
		] );
		
		$this->checkNewArticle ( $user, $feed );
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param array $storeParams        	
	 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	public function quickStore(User $user, $storeParams) {
		$category = $this->categorys->forCategoryName ( $user, '未分类' );
		if (empty ( $category )) {
			$category = $user->categorys ()->create ( [ 
					'name' => '未分类',
					'category_order' => 0 
			] );
		}
		
		$feed = Feed::where ( 'id', $storeParams ['feed_id'] )->first ();
		if (! empty ( $feed )) {
			$feedSub = $this->feedSubs->forUserByFeedId ( $user, $feed->id, 1 );
			
			if (! empty ( $feedSub )) {
				$resp = $this->responseJson ( 1000, '', '已经关注' );
				return response ( $resp );
			}
			$feed->sub_count = $feed->sub_count + 1;
			$feed->save ();
		}
		
		$feedSub = $user->feedSubs ()->create ( [ 
				'status' => 1,
				'feed_id' => $feed->id,
				'feed_name' => $feed->feed_name,
				'category_id' => $category->id 
		] );
		$this->checkNewArticle ( $user, $feed );
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param Feed $feed        	
	 */
	public function checkNewArticle(User $user, Feed $feed) {
		$articles = Article::where ( 'feed_id', $feed->id )->orderBy ( 'published', 'desc' )->take ( 30 )->get ();
		
		if (! empty ( $articles )) {
			foreach ( $articles as $article ) {
				$articleSub = new ArticleSub ();
				$articleSub->feed_id = $feed->id;
				$articleSub->user_id = $user->id;
				$articleSub->article_id = $article->id;
				$articleSub->status = 'unread';
				$articleSub->save ();
			}
		} else {
			$this->feeds->checkFeed ( $feed );
		}
	}
	
	/**
	 *
	 * @param string $feed_name        	
	 * @param string $feed_url        	
	 * @param string $category_id        	
	 * @param string $user        	
	 * @return boolean
	 */
	private function storeFeedSub($feed_name, $feed_url, $category_id, $user) {
		$feed = Feed::where ( 'url', $feed_url )->first ();
		if (empty ( $feed )) {
			$feed = new Feed ();
			$feed->user_id = $user->id;
			$feed->feed_name = $feed_name;
			$feed->url = $feed_url;
			$feed->category_id = $category_id;
			$feed->sub_count = 1;
			$feed->save ();
		} else {
			$feedSub = $this->feedSubs->forUserByFeedId ( $user, $feed->id, 1 );
			
			if (! empty ( $feedSub )) {
				return false;
			}
			
			// 如果未锁定，那么更改Feed的名称
			if (empty ( $feed->recommend_name ) && $feed->name != $feed_name) {
				$feed->feed_name = $feed_name;
			}
			$feed->sub_count = $feed->sub_count + 1;
			$feed->save ();
		}
		
		$feedSub = $user->feedSubs ()->create ( [ 
				'feed_id' => $feed->id,
				'feed_name' => $feed_name,
				'category_id' => $category_id 
		] );
		
		return true;
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param string $path        	
	 */
	public function importOpml($user, $path) {
		$importer = new Importer ( file_get_contents ( $path ) );
		$feedList = $importer->getFeedList ();
		
		$categorys = $this->categorys->forUser ( $user );
		if (count ( $categorys ) == 0) {
			$category = $user->categorys ()->create ( [ 
					'name' => '未分类',
					'category_order' => 0 
			] );
			$categorys = array (
					$category 
			);
		}
		$category_arr = array ();
		foreach ( $categorys as $category ) {
			$category_arr [$category->name] = $category->id;
		}
		
		$category_id = $category_arr ['未分类'];
		foreach ( $feedList->getItems () as $item ) {
			if ($item->getType () == 'category') {
				
				if (! isset ( $category_arr [$item->getTitle ()] )) {
					$category = $user->categorys ()->create ( [ 
							'name' => '未分类',
							'category_order' => 0 
					] );
					$category_arr [$item->getTitle ()] = $category->id;
				}
				
				$category_id = $category_arr [$item->getTitle ()];
				
				foreach ( $item->getFeeds () as $feed ) {
					$this->storeFeedSub ( $feed->getTitle (), $feed->getXmlUrl (), $category_id, $user );
				}
			} else {
				$this->storeFeedSub ( $item->getTitle (), $item->getXmlUrl (), $category_id, $user );
			}
		}
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param array $feedSubIdsArr        	
	 * @param int $changeFeedSubId        	
	 * @param int $changeFeedSubCategoryId        	
	 * @return boolean
	 */
	public function sort($user, $feedSubIdsArr, $changeFeedSubId, $changeFeedSubCategoryId) {
		$sort = 0;
		foreach ( $feedSubIdsArr as $feedSubId ) {
			$feedSub = $this->feedSubs->forUserByFeedSubId ( $user, $feedSubId, 1 );
			if (! empty ( $feedSub )) {
				$feedSub->update ( array (
						'feed_order' => $sort ++ 
				) );
			}
		}
		
		if (! empty ( $changeFeedSubId ) && ! empty ( $changeFeedSubCategoryId )) {
			$category = Category::where ( 'user_id', $user->id )->where ( "id", $changeFeedSubCategoryId )->first ();
			if (! empty ( $category )) {
				$feedSub = FeedSub::where ( 'user_id', $user->id )->where ( "id", $changeFeedSubId )->first ();
				if (! empty ( $feedSub )) {
					$feedSub->update ( array (
							'category_id' => $changeFeedSubCategoryId 
					) );
				}
			}
		}
		return true;
	}
	public function checkFeed(Feed $feed) {
		Log::info ( "Check Feed:" . $feed->id . '|' . $feed->url );
		
		$feedFactory = new FeedFactory ( [ 
				'cache.enabled' => false 
		] );
		$feeder = $feedFactory->make ( $feed->url );
		$simplePieInstance = $feeder->getRawFeederObject ();
		
		if (! empty ( $simplePieInstance )) {
			$feedSubs = FeedSub::where ( 'feed_id', $feed->id )->where ( 'status', 1 )->get ();
			
			$previousweek = date ( 'Y-m-j H:i:s', strtotime ( '-7 days' ) );
			
			$feed_last_published = '';
			foreach ( $simplePieInstance->get_items () as $item ) {
				// count the number of items that already exist in the database with the item url and feed_id
				$results_url = Article::where ( [ 
						'feed_id' => $feed->id,
						'url' => $item->get_permalink () 
				] )->count ();
				$results_title = Article::where ( [ 
						'feed_id' => $feed->id,
						'subject' => $item->get_title () 
				] )->count ();
				$date = $item->get_date ( 'Y-m-j H:i:s' );
				
				if ($results_url == 0 && $results_title == 0 && ! (strtotime ( $date ) < strtotime ( $previousweek ))) {
					$article = new Article ();
					
					$article->feed_id = $feed->id;
					$article->status = 'unread';
					$article->url = $item->get_permalink ();
					$article->subject = $item->get_title ();
					$article->content = $item->get_description ();
					$article->published = $item->get_date ( 'Y-m-j H:i:s' );
					
					$article->user_id = $feed->user_id;
					
					$description = $item->get_description ();
					preg_match ( '/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $description, $image );
					if (array_key_exists ( 'src', $image )) {
						try {
							$arr = @getimagesize ( $image ['src'] );
							if (! empty ( $arr ) && $arr [0] > 50 && $arr [1] > 50) {
								$article->image_url = $image ['src'];
							}
						} catch ( Exception $e ) {
							Log::info ( "getimagesize error:" . $image ['src'] );
						}
					}
					
					$article->save ();
					
					Log::info ( "Save Article:" . $article->url );
					
					foreach ( $feedSubs as $feedSub ) {
						$articleSub = ArticleSub::where ( 'user_id', $feedSub->user_id )->where ( 'article_id', $article->id )->first ();
						if (empty ( $articleSub )) {
							$articleSub = new ArticleSub ();
							$articleSub->feed_id = $feedSub->feed_id;
							$articleSub->user_id = $feedSub->user_id;
							$articleSub->article_id = $article->id;
							$articleSub->status = 'unread';
							$articleSub->published = $article->published;
							$articleSub->save ();
							
							Log::info ( "Save ArticleSub:" . $articleSub->user_id . '|' . $articleSub->article_id );
						}
					}
					
					if (empty ( $feed_last_published ) || strtotime ( $feed_last_published ) < strtotime ( $article->published )) {
						$feed_last_published = $article->published;
					}
				}
			}
			
			// update feed updated_at record
			if (count ( $simplePieInstance->get_items () ) > 0) {
				if (! empty ( $feed_last_published )) {
					Feed::where ( 'id', $feed->id )->update ( [ 
							'updated_at' => date ( 'Y-m-j H:i:s' ),
							'feed_desc' => $simplePieInstance->get_description (),
							'favicon' => $simplePieInstance->get_image_url (),
							'last_published' => $feed_last_published 
					] );
				} else {
					Feed::where ( 'id', $feed->id )->update ( [ 
							'updated_at' => date ( 'Y-m-j H:i:s' ),
							'feed_desc' => $simplePieInstance->get_description (),
							'favicon' => $simplePieInstance->get_image_url () 
					] );
				}
			}
		}
	}
	public function checkFanfouFeed(Feed $feed) {
		// set previous week
		$previousweek = date ( 'Y-m-j H:i:s', strtotime ( '-7 days' ) );
		
		Log::info ( "Check Feed:" . $feed->id . '|' . $feed->url );
		
		$feedSubs = FeedSub::where ( 'feed_id', $feed->id )->where ( 'status', 1 )->get ();
		
		$config = config ( 'services.fanfou' );
		
		$user = new User ();
		$user->id = $feed->user_id;
		$thirdRepository = new ThirdRepository ();
		$third = $thirdRepository->forUserSource ( $user );
		if (empty ( $third )) {
			return;
		}
		
		$oauth_token = $third ['token_value'];
		$oauth_token_secret = $third ['token_secret'];
		
		$ff_user = new FFClient ( config ( "services.fanfou.client_id" ), config ( "services.fanfou.client_secret" ), $oauth_token, $oauth_token_secret );
		
		$items = $ff_user->friends_timeline ( 1, 50 );
		$items = json_decode ( $items, true );
		
		if (empty ( $items ) || isset ( $items ['error'] )) {
			return false;
		}
		
		foreach ( $items as $item ) {
			if (isset ( $item ['repost_status'] )) {
				$item = $item ['repost_status'];
			}
			
			$results_url = Article::where ( [ 
					'feed_id' => $feed->id,
					'url' => 'http://fanfou.com/statuses/' . $item ['id'] 
			] )->count ();
			$date = date ( 'Y-m-d H:i:s', strtotime ( $item ['created_at'] ) );
			
			$feed_last_published = '';
			if ($results_url == 0 && ! (strtotime ( $date ) < strtotime ( $previousweek ))) {
				$article = new Article ();
				
				$content = $item ['text'] . "&nbsp;&nbsp; ";
				
				if (isset ( $item ['user'] )) {
					$content = "<a href='http://fanfou.com/{$item['user']['unique_id']}'>@{$item['user']['name']}</a> &nbsp; $content";
				}
				
				if (isset ( $item ['photo'] )) {
					$content = "$content<br><img width='' src='{$item['photo']['largeurl']}'/><a href='{$item['photo']['largeurl']}' target='_blank'>大图</a>";
				}
				
				// get article content
				$article->feed_id = $feed->id;
				$article->status = 'unread';
				$article->url = 'http://fanfou.com/statuses/' . $item ['id'];
				$article->subject = '';
				$article->content = $content;
				$article->published = date ( 'Y-m-d H:i:s', strtotime ( $item ['created_at'] ) );
				
				$article->user_id = $feed->user_id;
				
				$description = $item ['text'];
				$article->save ();
				
				Log::info ( "Save Article:" . $article->url );
				
				foreach ( $feedSubs as $feedSub ) {
					$articleSub = ArticleSub::where ( 'user_id', $feedSub->user_id )->where ( 'article_id', $article->id )->first ();
					if (empty ( $articleSub )) {
						$articleSub = new ArticleSub ();
						$articleSub->feed_id = $feedSub->feed_id;
						$articleSub->user_id = $feedSub->user_id;
						$articleSub->article_id = $article->id;
						$articleSub->status = 'unread';
						$articleSub->published = $article->published;
						$articleSub->save ();
						
						Log::info ( "Save ArticleSub:" . $articleSub->user_id . '|' . $articleSub->article_id );
					}
				}
				
				if (empty ( $feed_last_published ) || strtotime ( $feed_last_published ) < strtotime ( $article->published )) {
					$feed_last_published = $article->published;
				}
			}
		}
		
		// update feed updated_at record
		if (count ( $items ) > 0) {
			if (! empty ( $feed_last_published )) {
				Feed::where ( 'id', $feed->id )->update ( [ 
						'updated_at' => date ( 'Y-m-j H:i:s' ),
						'last_published' => $feed_last_published 
				] );
			} else {
				Feed::where ( 'id', $feed->id )->update ( [ 
						'updated_at' => date ( 'Y-m-j H:i:s' ) 
				] );
			}
		}
	}
}
