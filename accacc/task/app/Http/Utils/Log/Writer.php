<?php

namespace App\Http\Utils\Log;

use Illuminate\Log\Writer as BaseWriter;
use Monolog\Formatter\LineFormatter;

class Writer extends BaseWriter {
	const LOG_FORMAT = "[%datetime%]        %channel%.%level_name%        %extra%        %context%        %message%\n";
	
	/**
	 * Get a default Monolog formatter instance.
	 *
	 * @return \Monolog\Formatter\LineFormatter
	 */
	protected function getDefaultFormatter() {
		return new LineFormatter ( self::LOG_FORMAT, 'Y-m-d H:i:s.u', false, false );
	}
}