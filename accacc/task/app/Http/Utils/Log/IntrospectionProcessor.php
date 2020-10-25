<?php

namespace App\Http\Utils\Log;

use Monolog\Logger;

class IntrospectionProcessor {
	private $level;
	private $skipClassesPartials;
	public function __construct($level = Logger::DEBUG, array $skipClassesPartials = ['Monolog\\', 'Illuminate\\']) {
		$this->level = Logger::toMonologLevel ( $level );
		$this->skipClassesPartials = $skipClassesPartials;
	}
	
	/**
	 *
	 * @param array $record        	
	 * @return array
	 */
	public function __invoke(array $record) {
		// return if the level is not high enough
		if ($record ['level'] < $this->level) {
			return $record;
		}
		
		$trace = debug_backtrace ();
		
		// skip first since it's always the current method
		array_shift ( $trace );
		// the call_user_func call is also skipped
		array_shift ( $trace );
		
		$i = 0;
		
		while ( isset ( $trace [$i] ['class'] ) ) {
			foreach ( $this->skipClassesPartials as $part ) {
				if (strpos ( $trace [$i] ['class'], $part ) !== false) {
					$i ++;
					continue 2;
				}
			}
			break;
		}
		
		$request = null;
		if (isset ( $trace [$i] ['class'] )) {
			$request = $trace [$i] ['class'];
			if (isset ( $trace [$i] ['class'] )) {
				$request = $request . '->' . $trace [$i] ['function'];
			}
		} else if (isset ( $trace [$i - 1] ['file'] )) {
			$request = $trace [$i - 1] ['file'];
		}
		
		$record ['extra'] ['request'] = $request;
		$record ['extra'] ['line'] = $trace [$i - 1] ['line'];
		$record ['extra'] ['ip'] = isset ( $_SERVER ["REMOTE_ADDR"] ) ? $_SERVER ["REMOTE_ADDR"] : '';
		return $record;
	}
}