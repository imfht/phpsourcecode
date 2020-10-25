<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\KindleLog;
use Develpr\Phindle\Phindle;
use Develpr\Phindle\Content;
use App\Models\ArticleSub;
use App\Http\Utils\SpideUtil;
use App\Repositories\SettingRepository;
use App\Repositories\ArticleSubRepository;
use Log;
use Mail;

/**
 * push the rss content to kindle
 *
 * @author edison.an
 *        
 */
class KindlePush extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'kindle_push';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Push Kindle File Daily';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$settingRepository = new SettingRepository ();
		$articleSubRepository = new ArticleSubRepository ();
		
		// get start push list
		$settings = $settingRepository->getStartList ();
		
		foreach ( $settings as $setting ) {
			$user = $setting->user;
			
			// start record push log
			$kindleLog = new KindleLog ();
			$kindleLog->user_id = $user->id;
			$kindleLog->type = 2;
			$kindleLog->status = 1;
			$kindleLog->save ();
			
			// init phindle
			$phindle = new Phindle ( array (
					'title' => "Montage GTD每日订阅推送" . date ( 'Y-m-d' ),
					'publisher' => "Montage GTD " . $user->id,
					'creator' => $user->name,
					'language' => 'zh-CN',
					'subject' => 'Montage GTD每日订阅', // @see https://www.bisg.org/complete-bisac-subject-headings-2013-edition
					'description' => 'Montage GTD每日订阅推送' . date ( 'Y-m-d' ),
					'path' => config ( "app.storage_path" ) . '/ebooks', // The path that temp files will be stored, as well as the location of the final ebook mobi file
					'isbn' => '666666666666666',
					'staticResourcePath' => config ( "app.storage_path" ) . '/ebooks/static', // The absolute path to your static resources referenced in html (images, css, etc)
					'cover' => 'cover.jpg', // The relative path of your cover image
					'kindlegenPath' => '/usr/local/bin/kindlegen', // The path to the kindlegen utility
					'downloadImages' => true 
			) ) // Should images be downloaded from the web if found in your html?
;
			
			$start_time = date ( 'Y-m-d H:i:s', strtotime ( date ( 'Y-m-d H:i:s' ) ) - 86400 );
			$end_time = date ( 'Y-m-d H:i:s' );
			
			$feed_info = array ();
			
			$chapter_count = 0;
			$article_count = 0;
			
			// get recent publish list
			$articleSubs = $articleSubRepository->getRecentPublishList ( $user, 'unread', $start_time, $end_time, 300 );
			if (empty ( $articleSubs )) {
				continue;
			}
			foreach ( $articleSubs as $articleSub ) {
				$article = $articleSub->article;
				
				// new chapter process
				if (! isset ( $feed_info [$article->feed_id] )) {
					// article count set 0,chapter count +1,save chapter info
					$article_count = 0;
					$chapter_count ++;
					$feed_info [$article->feed_id] = $article->feed;
					
					$content = new Content ();
					$content->setHtml ( '<meta http-equiv="Content-Type" content="text/html;charset=utf-8"><h2>' . $article->feed->feed_name . '</h2>' . $article->feed->feed_desc );
					$content->setTitle ( $chapter_count . ' ' . $article->feed->feed_name );
					$content->setPosition ( $chapter_count * 1000 + $article_count );
					$phindle->addContent ( $content );
				}
				
				// article count +1 ,until article count > 20, break it!
				if ($article_count > 20)
					continue;
				$article_count ++;
				
				// if need with image, do something img
				if ($setting->with_image_push == 1) {
					$spideUtil = new SpideUtil ();
					$article_content = $spideUtil->processKindleImgContent ( $article->content, config ( "app.storage_path" ) . '/ebooks/temp' );
				} else {
					$article_content = preg_replace ( "#<img.*>#iUs", "", $article->content ); // 无图
				}
				
				$content = new Content ();
				$content->setHtml ( '<meta http-equiv="Content-Type" content="text/html;charset=utf-8"><h3>' . $article->subject . '</h3>' . $article_content . '<a href="' . $article->url . '">查看原文</a>' );
				$content->setTitle ( $chapter_count . '.' . $article_count . ' ' . $article->subject );
				$content->setPosition ( $chapter_count * 1000 + $article_count );
				$phindle->addContent ( $content );
			}
			
			try {
				// do something build mobi file
				$phindle->process ();
				
				// get mobi file path
				$path = $phindle->getMobiPath ();
				
				$kindleLog->path = $path;
				$kindleLog->status = 2;
				$kindleLog->save ();
				
				// send to kindle address
				Log::info ( 'send to kindle:' . $user->id . '|' . count ( $articleSubs ) . '|' . $path );
				Mail::send ( 'emails.kindle', [ 
						'setting' => $setting,
						'path' => $path 
				], function ($m) use ($setting, $path) {
					$m->to ( $setting->kindle_email, 'user' )->subject ( 'Send To Kindle' );
					$m->attach ( $path );
				} );
				
				$kindleLog->status = 3;
				$kindleLog->save ();
			} catch ( Exception $e ) {
				Log::info ( 'ERROR:' . serialize ( $e ) . ':' . serialize ( $phindle ) );
			}
		}
	}
}
