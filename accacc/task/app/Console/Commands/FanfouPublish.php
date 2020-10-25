<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ThirdService;
use App\Http\Utils\OAuth1\FFClient;
use Log;

/**
 * push fanfou message
 *
 * @author edison.an
 *        
 */
class FanfouPublish extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fanfou_publish';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish Fanfou Daily';
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$third = new ThirdService ();
		$third->sceduleFanfouFave ();
		$third = Third::where ( 'third_id', env ( 'FANFOU_ID' ) )->first ();
		if (empty ( $third )) {
			Log::info ( "[__CLASS__->__FUNCTION__]:not third info|{env('FANFOU_ID')}" );
			return null;
		}
		
		$oauth_token = $third ['token_value'];
		$oauth_token_secret = $third ['token_secret'];
		
		// fanfou client
		$ff_user = new FFClient ( config ( "services.fanfou.client_id" ), config ( "services.fanfou.client_secret" ), $oauth_token, $oauth_token_secret );
		
		$imojs = array (
				'(｀･ω･´) (´･ω･｀)',
				'(ÒωÓױ)呃！！！！',
				'(￣▽￣")',
				'(。-`ω´-)',
				'╮(￣▽￣)╭' 
		);
		
		$message = env ( 'FANFOU_MESSAGE' );
		$message_arr = explode ( '|', $message );
		
		// foreach message array to push message
		foreach ( $message_arr as $item ) {
			$result = $ff_user->update ( $item . $imojs [rand ( 0, 4 )] );
			Log::info ( "[__CLASS__->__FUNCTION__]:result info|{$result}" );
		}
	}
}
