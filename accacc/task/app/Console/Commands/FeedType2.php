<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\FeedRepository;
use App\Http\Utils\SpideUtil;
use Log;

/**
 * get new article from type 2 feed
 *
 * @author edison.an
 *        
 */
class FeedType2 extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'feed_type2';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Type 2 Feed get New Article';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$feedRepository = new FeedRepository ();
		$feeds = $feedRepository->getListByTypeStatus ( 2, 1 );
		
		$spideUtil = new SpideUtil ();
		
		foreach ( $feeds as $feed ) {
			$spideUtil->processFeed ( $feed );
			Log::info ( 'process feed ! url:' . $feed->url );
		}
	}
}
