<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-9
 * Time: 11:37
 */

namespace DataComposer\Providers\excel;

use Log;
abstract class _base
{

	public function check($filepath){
		$b= file_exists($filepath);
		if(!$b){
			Log::error('no: '.$filepath );
		}
		return $b;
	}
	
	
	
}