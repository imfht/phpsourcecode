<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Helper;


class AnsiColorHelper
{

	const RESET = '0';

	const BOLD     = '1';
	const BOLD_OFF = '22';

	const ITALIC     = '3';
	const ITALIC_OFF = '23';

	const UNDERLINE     = '4';
	const UNDERLINE_OFF = '24';

	const STRIKE     = '9';
	const STRIKE_OFF = '29';

	const BLACK   = '30';
	const RED     = '31';
	const GREEN   = '32';
	const YELLOW  = '33';
	const BLUE    = '34';
	const PURPLE  = '35';
	const CYAN    = '36';
	const WHITE   = '37';
	const DEFAULT = '39';

	const BLACK_BACK   = '40';
	const RED_BACK     = '41';
	const GREEN_BACK   = '42';
	const YELLOW_BACK  = '43';
	const BLUE_BACK    = '44';
	const PURPLE_BACK  = '45';
	const CYAN_BACK    = '46';
	const WHITE_BACK   = '47';
	const DEFAULT_BACK = '49';

	const REVERSE_FORE_BACK = '7';

	const EFFECTIVE_CODES = [
		self::RESET,
		self::BOLD,
		self::BOLD_OFF,
		self::ITALIC,
		self::ITALIC_OFF,
		self::UNDERLINE,
		self::UNDERLINE_OFF,
		self::STRIKE,
		self::STRIKE_OFF,
		self::BLACK,
		self::BLACK_BACK,
		self::RED,
		self::RED_BACK,
		self::GREEN,
		self::GREEN_BACK,
		self::YELLOW,
		self::YELLOW_BACK,
		self::BLUE,
		self::BLUE_BACK,
		self::PURPLE,
		self::PURPLE_BACK,
		self::CYAN,
		self::CYAN_BACK,
		self::WHITE,
		self::WHITE_BACK,
		self::DEFAULT,
		self::DEFAULT_BACK,
	];

	protected $namedCodes = [
		'reset'         => self::RESET,
		'bold'          => self::BOLD,
		'bold:off'      => self::BOLD_OFF,
		'italic'        => self::ITALIC,
		'italic:off'    => self::ITALIC_OFF,
		'underline'     => self::UNDERLINE,
		'underline:off' => self::UNDERLINE_OFF,
		'strike'        => self::STRIKE,
		'strike:off'    => self::STRIKE_OFF,
		'black'         => self::BLACK,
		'red'           => self::RED,
		'green'         => self::GREEN,
		'yellow'        => self::YELLOW,
		'blue'          => self::BLUE,
		'purple'        => self::PURPLE,
		'cyan'          => self::CYAN,
		'white'         => self::WHITE,
		'default'       => self::DEFAULT,
		'black:back'    => self::BLACK_BACK,
		'red:back'      => self::RED_BACK,
		'green:back'    => self::GREEN_BACK,
		'yellow:back'   => self::YELLOW_BACK,
		'blue:back'     => self::BLUE_BACK,
		'purple:back'   => self::PURPLE_BACK,
		'cyan:back'     => self::CYAN_BACK,
		'white:back'    => self::WHITE_BACK,
		'reverse'       => self::REVERSE_FORE_BACK,
	];

	public function filterAnsiCodes($codes)
	{
		$results = [];
		if (is_string($codes)) {
			$codes = preg_split('/[;,|]/i', $codes);
		}
		if (is_array($codes)) {
			foreach ($codes as $code) {
				$code = trim($code, ' ;,|');
				if (empty($code)) continue;
				if (isset($this->namedCodes[$code])) {
					$results[] = $this->namedCodes[$code];
				} elseif (array_search($code, self::EFFECTIVE_CODES) !== false) {
					$results[] = $code;
				} else {
					continue;
				}
			}
		}
		return implode(';', $results);
	}

	public function ansiStart($codes)
	{
		$codes = $this->filterAnsiCodes($codes);
		if (empty($codes)) return '';
		return "\033[{$codes}m";
	}

	public function ansiClose()
	{
		return "\033[0m";
	}

	public function wrap(string $msg, $codes)
	{
		$codes = $this->filterAnsiCodes($codes);
		if (empty($msg)) return '';
		if (empty($codes)) return $msg;
		return "\033[{$codes}m{$msg}\033[0m";
	}
}