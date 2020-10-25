<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\FeedRepository;
use App\Feed;
use App\Http\Utils\SpideUtil;
use Log;

/**
 * get new article from common feed
 *
 * @author edison.an
 *        
 */
class Backup2Qiniu extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'backup2qiniu {file_path}';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Backup SQL File To Qiniu!';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$file_path = $this->argument ( 'file_path' );
		$path = $file_path . '/' . date ( 'Ymd' ) . '.sql.tar.gz';
		
		if (file_exists ( $path )) {
			try {
				\Storage::disk ( 'qiniu' )->put ( basename ( $path ), fopen ( $path, 'r+' ) );
				\Log::info ( 'upload to qiniu :' . $path );
			} catch ( Exception $e ) {
				\Log::info ( 'ERROR upload to qiniu :' . $path . '|' . serialize ( $e ) );
			}
		} else {
			\Log::info ( 'path not exist :' . $path );
		}
	}
}
