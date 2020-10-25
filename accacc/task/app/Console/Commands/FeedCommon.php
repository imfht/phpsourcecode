<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\FeedRepository;
use App\Feed;
use App\Http\Utils\SpideUtil;
use Log;
use App\Services\FeedService;

/**
 * get new article from common feed
 *
 * @author edison.an
 *        
 */
class FeedCommon extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'feed_common {active_level}';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Common Feed get New Article';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$active_level = $this->argument ( 'active_level' );
		
		$feedService = app ( FeedService::class );
		$feedRepository = new FeedRepository ();
		$spideUtil = new SpideUtil ();
		
		$feeds = $feedRepository->getListByActiveLevelStatus ( $active_level, 1 );
		
		foreach ( $feeds as $feed ) {
			if ($feed->type == 3) {
				$feedService->checkFanfouFeed ( $feed );
			} else if ($feed->type == 2) {
				$spideUtil->processFeed ( $feed );
			} else {
				$feedService->checkFeed ( $feed );
			}
		}
	}
}
