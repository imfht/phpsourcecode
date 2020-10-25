<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\FeedRepository;
use Log;

/**
 * get new article from type 3 feed
 *
 * @author edison.an
 *        
 */
class FeedType3 extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'feed_type3';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Type 3 Feed get New Article';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$feedRepository = new FeedRepository ();
		$feeds = $feedRepository->getListByTypeStatus ( 3, 1 );
		
		foreach ( $feeds as $feed ) {
			$feedRepository->checkFanfouFeed ( $feed );
			Log::info ( 'process feed ! url:' . $feed->url );
		}
	}
}
