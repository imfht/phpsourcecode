<?php

namespace App\Http\Utils;

use App\Models\Article;
use App\Models\ArticleSub;
use App\Models\FeedSub;
use App\Models\Feed;
use function GuzzleHttp\json_encode;
use Illuminate\Support\Facades\Log;
use Exception;

include 'Other/simple_html_dom.php';
class SpideUtil {
	public function processFeed($feed) {
		$result = $this->request ( $feed ['url'] );
		if (empty ( $result )) {
			return false;
		}
		
		// get list
		$list = $this->getList ( $result, $feed->type );
		if (empty ( $list )) {
			return false;
		}
		// 获取该Feed的订阅者
		$feedSubs = FeedSub::where ( 'feed_id', $feed->id )->get ();
		
		$feed_last_published = '';
		foreach ( $list as $item ) {
			$article = Article::where ( 'feed_id', $feed->id )->where ( 'user_id', $feed->user_id )->where ( 'url', $item ['url'] )->first ();
			if (empty ( $article )) {
				$article = new Article ();
				$article->feed_id = $feed->id;
				$article->user_id = $feed->user_id;
				$article->status = 'unread';
				$article->url = $item ['url'];
				$article->subject = $item ['subject'];
				$article->published = $item ['published'];
				$article->save ();
			}
			
			foreach ( $feedSubs as $feedSub ) {
				$articleSub = ArticleSub::where ( 'user_id', $feedSub->user_id )->where ( 'article_id', $article->id )->first ();
				if (empty ( $articleSub )) {
					$articleSub = new ArticleSub ();
					$articleSub->feed_id = $feedSub->feed_id;
					$articleSub->user_id = $feedSub->user_id;
					$articleSub->article_id = $article->id;
					$articleSub->status = 'unread';
					$article->published = $item ['published'];
					$articleSub->save ();
				}
			}
			
			if (empty ( $feed_last_published ) || strtotime ( $feed_last_published ) < strtotime ( $article->published )) {
				$feed_last_published = $article->published;
			}
			
			// get content
			if (empty ( $article->content )) {
				$result = $this->request ( $item ['url'] );
				if (empty ( $result )) {
					continue;
				}
				$params = $this->getContent ( $result, $feed->type );
				if (empty ( $params )) {
					continue;
				}
				$article->content = $params ['content'];
				$article->save ();
			}
		}
		
		if (count ( $list ) > 0) {
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
	public function getList($result, $type) {
		$html = @str_get_html ( $result );
		$list = array ();
		
		if (empty ( $html )) {
			return $list;
		}
		
		if ($type == 2) {
			$articles = $html->find ( ".post-item" );
			foreach ( $articles as $article ) {
				$params = array ();
				$url = $article->find ( "h2 a", - 1 )->href;
				$subject = $article->find ( "h2 a", - 1 )->plaintext;
				$time = $article->find ( ".comment-date", - 1 )->plaintext;
				if (empty ( $url )) {
					continue;
				}
				$params ['url'] = 'http://www.mafengwo.cn' . $url;
				$params ['subject'] = $subject;
				$params ['published'] = date ( 'Y-m-d H:i:s', strtotime ( $time ) );
				
				$list [] = $params;
			}
		}
		return $list;
	}
	public function getContent($result, $type) {
		$html = @str_get_html ( $result );
		$params = array ();
		if (empty ( $html )) {
			return $params;
		}
		
		if ($type == 2) {
			$article = @$html->find ( ".view_con", 0 );
			if (empty ( $article )) {
				return $params;
			}
			$params ['content'] = $article->innertext;
			$params ['content'] = str_replace ( array (
					"src=\"data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==\"",
					"data-src",
					"_j_lazyload" 
			), array (
					"",
					"src",
					"lazy" 
			), $params ['content'] );
		}
		return $params;
	}
	public function request($url) {
		$try_count = 0;
		$result = '';
		while ( $try_count < 3 ) {
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
			$result = curl_exec ( $ch );
			curl_close ( $ch );
			
			$try_count ++;
		}
		return $result;
	}
	public function processKindleImgContent($content, $path) {
		preg_match_all ( '#<img src="(.*?)"#is', $content, $matches );
		$img_urls = $matches [1];
		
		Log::info ( \json_encode ( $img_urls ) );
		
		$args = array ();
		foreach ( $img_urls as $img_url ) {
			$args [] = '/images/temp/';
		}
		
		$content = str_replace ( $img_urls, array_map ( 'get_image_filename', $img_urls, $args ), $content );
		$content = preg_replace ( "/style=.+?['|\"]/i", '', $content );
		$content = str_replace ( '<img', '<img style="margin:0 auto;display:block;height:300px"', $content );
		
		foreach ( $img_urls as $img_url ) {
			$this->download ( $img_url, config ( "app.storage_path" ) . '/ebooks/static' . "/images/temp/" );
		}
		
		return $content;
	}
	
	// 下载图片
	public function download($url, $store_dir) {
		$filename = get_image_filename ( $url, $store_dir );
		if (file_exists ( $filename ))
			return; // 存在时不下载
				        // $curl = new Curl\Curl();
				        // $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
				        // $curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:40.0) Gecko/20100101 Firefox/40.0');
				        // $curl->setHeader('Accept-Language', 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3');
				        // $curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
		@mkdir ( $store_dir, 0755, true );
		// $curl->get($url);
		// $data = $curl->response;
		$data = $this->request ( $url );
		file_put_contents ( $filename, $data );
		try {
			$image = new \Eventviva\ImageResize ( $filename );
			$image->resizeToWidth ( 200 );
			$image->save ( $filename );
		} catch ( Exception $e ) {
			Log::info ( 'ERROR ' . serialize ( $e ) . '|' . $filename );
		}
		return $filename;
	}
}