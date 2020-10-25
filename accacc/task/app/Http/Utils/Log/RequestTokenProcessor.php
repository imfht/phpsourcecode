<?php

namespace App\Http\Utils\Log;

class RequestTokenProcessor {
	
	/**
	 *
	 * @param array $record        	
	 * @return array
	 */
	public function __invoke(array $record) {
		$record ['extra'] ['token'] = LogUtil::getRequestToken ();
		
		return $record;
	}
}