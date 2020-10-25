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

class DateHelper
{
	const TYPE_DATE = 'date';
	const TYPE_TIME = 'time';

	const PAT_SPACE = '[\s\t]+';

	const PAT_YEAR     = '[\d]{2}|[\d]{4}';
	const PAT_MONTH    = '[\d]{1,2}';
	const PAT_DAY      = '[\d]{1,2}';
	const PAT_DATE_SPR = '[\/-]';

	const PAT_HOUR     = '[\d]{1,2}';
	const PAT_MINUTE   = '[\d]{1,2}';
	const PAT_SECOND   = '[\d]{1,2}';
	const PAT_TIME_SPR = '[:]';

	const DEFAULT_DATE_FORMAT     = 'Y-m-d';
	const DEFAULT_TIME_FORMAT     = 'H:i:s';
	const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

	const DEFAULT_DATE_EMPTY_VALUE     = '0000-00-00';
	const DEFAULT_TIME_EMPTY_VALUE     = '00:00:00';
	const DEFAULT_DATETIME_EMPTY_VALUE = '0000-00-00 00:00:00';

	const DEFAULT_DATE_PATTERN = '(' . self::PAT_YEAR . ')' . self::PAT_DATE_SPR . '(' . self::PAT_MONTH . ')' . self::PAT_DATE_SPR . '(' . self::PAT_DAY . ')';
	const DEFAULT_TIME_PATTERN = '(' . self::PAT_HOUR . ')(?:' . self::PAT_TIME_SPR . '(' . self::PAT_MINUTE . ')(?:' . self::PAT_TIME_SPR . '(' . self::PAT_SECOND . '))?)?';
	// DateTime 应该是允许只输入了年月日，但是没有输入时间的可能性
	const DEFAULT_DATETIME_PATTERN = '(' . self::DEFAULT_DATE_PATTERN . '((' . self::PAT_SPACE . ')?' . self::DEFAULT_TIME_PATTERN . ')?)';

	protected $date         = 0;
	protected $weekRangeDay = 7;

	protected $isIgnoreTime = true;

	protected $formatDate = 'Y-m-d';
	protected $formatTime = 'H:i:s';

	protected $patternDate = self::DEFAULT_DATE_PATTERN;
	protected $patternTime = self::DEFAULT_TIME_PATTERN;

	/**
	 * @param null $date
	 * @param null $weekRangeDays
	 *
	 * @return static
	 */
	public static function withTime($date = null, $weekRangeDays = null)
	{
		return new static($date, false, $weekRangeDays);
	}

	/**
	 * @param null $date
	 * @param null $weekRangeDays
	 *
	 * @return static
	 */
	public static function withoutTime($date = null, $weekRangeDays = null)
	{
		return new static($date, true, $weekRangeDays);
	}

	public function __construct($date = null, bool $isIgnoreTime = null, int $weekRangeDay = null)
	{
		$this->isIgnoreTime = !!$isIgnoreTime;
		$this->setDate($date);
		if (isset($isIgnoreTime))
			$this->setIgnoreTime($isIgnoreTime);
		if (isset($weekRangeDay))
			$this->setWeekRangeDay($weekRangeDay);
	}

	public function setDate($date)
	{
		if (is_numeric($date)) {
			$this->date = $date;
		} elseif (empty($date) || is_array($date))
			$date = time();
		elseif (is_object($date)) {
			if ($date instanceof DateHelper)
				$date = $date->date;
			else if ($date instanceof \DateTime)
				$date = $date->getTimestamp();
			else if (is_callable([$date, 'toDateTime']))
				$date = $date->toDateTime();
			else
				$date = time();
		} else {
			$strDate = strval($date);
			if (($rs = $this->match($strDate, 'only-date')) !== false) {
				$date = mktime(...$rs);
				// $this->setIgnoreTime(true);
			} elseif (($rs = $this->match($strDate)) !== false) {
				$date = mktime(...$rs);
			} else {
				$date = strtotime($strDate);
				if ($date === false)
					throw new \Exception("Invalid datetime input");
			}
		}
		if ($this->isIgnoreTime) {
			$this->date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));
		} else {
			$this->date = $date;
		}
		return $this;
	}

	public function setIgnoreTime(bool $isIgnore = false)
	{
		$this->isIgnoreTime = $isIgnore;
		if ($this->isIgnoreTime) {
			$this->date = mktime(0, 0, 0, date('n', $this->date), date('j', $this->date), date('Y', $this->date));
		}
		return $this;
	}

	public function date()
	{
		return $this->date;
	}

	public function getFormat(string $type = null)
	{
		switch ($type) {
			case 'date' :
				return $this->formatDate;
			case 'time' :
				return $this->formatTime;
			default :
				if ($this->isIgnoreTime)
					return $this->formatDate;
				return $this->formatDate . ' ' . $this->formatTime;
		}
	}

	public function format(string $format = null)
	{
		if (empty($format))
			$format = $this->getFormat();
		return date($format, $this->date);
	}

	public function setDateFormat(string $format)
	{
		if (!empty($format))
			$this->formatDate = $format;
		return $this;
	}

	public function setTimeFormat(string $format)
	{
		if (!empty($format))
			$this->formatTime = $format;
		return $this;
	}

	public function __toString()
	{
		return $this->format($this->getFormat());
	}

	public function string()
	{
		return $this->format($this->getFormat());
	}

	public function pattern(string $type = null, bool $isRegex = false)
	{
		$pattern = '';
		switch ($type) {
			case 'date' :
				$pattern = $this->patternDate;
				break;
			case 'time' :
				$pattern = $this->patternTime;
				break;
			case 'only-date' :
				$pattern = '(' . self::PAT_YEAR . ')([\d]{2})([\d]{2})$';
				break;
			default :
				$pattern = $this->patternDate;
				$pattern .= '(' . self::PAT_SPACE . $this->patternTime . ')?';
				break;
		}
		if ($isRegex)
			$pattern = '#^' . $pattern . '#';
		return $pattern;
	}

	public function match(string $value, string $type = null)
	{
		$pattern = $this->pattern($type, true);
		$results = [0, 0, 0, 0, 0, 0];
		if (preg_match($pattern, $value, $matches)) {
			$results[5] = intval($matches[1] ?? 0); // 年
			$results[3] = intval($matches[2] ?? 0); // 月
			$results[4] = intval($matches[3] ?? 0); // 日
			$results[0] = intval($matches[5] ?? 0); // 日
			$results[1] = intval($matches[6] ?? 0); // 日
			$results[2] = intval($matches[7] ?? 0); // 日
			return $results;
		}
		return false;
	}

	public function getWeekDay()
	{
		return intval(date('N', $this->date));
	}

	public function setWeekRangeDay(int $day)
	{
		if ($day === 5 || $day === 7 || $day === 6) {
			$this->weekRangeDay = $day;
		}
		return $this;
	}

	public function getWeekRange()
	{
		$weekDay = $this->getWeekDay();
		$startIndex = ($weekDay - 1);
		$start = strtotime("-{$startIndex} days", $this->date);
		$endIndex = ($this->weekRangeDay - $weekDay);
		// if ($isForShow)
		// 	$endIndex -= 1;
		$end = strtotime("+{$endIndex} days", $this->date);
		return new DateRangeHelper($start, $end, $this);
	}

	public function getMonthRange()
	{
		$year = date('Y', $this->date);
		$month = date('n', $this->date);
		$day = 1;
		$start = mktime(0, 0, 0, $month, $day, $year);
		$end = strtotime('+1 month -1 days', $start);
		return new DateRangeHelper($start, $end, $this);
	}

	public function get30DayRange()
	{
		$start = strtotime('-30 days', $this->date);
		return new DateRangeHelper($start, $this, $this);
	}

	public function getDaysRange(int $days)
	{
		$days = abs($days); // 取绝对值，1 = -1
		if ($days === 0) $days = 1; // 最小1天
		elseif ($days > 365) $days = 365; // 最大365天
		$start = strtotime("-{$days} days", $this->date);
		return new DateRangeHelper($start, $this, $this);
	}

	public function ref(DateHelper $date)
	{
		$this->setIgnoreTime($date->isIgnoreTime);
		$this->setWeekRangeDay($date->weekRangeDay);
		$this->setDateFormat($date->formatDate);
		$this->setTimeFormat($date->formatTime);
		return $this;
	}

	public function modify(string $modify)
	{
		$date = strtotime($modify, $this->date);
		if ($date !== false) {
			$newDate = clone $this;
			$newDate->setDate($date);
			return $newDate;
		}
		return $this;
	}

	public function getWeekYear()
	{
		return intval(date('W', $this->date));
	}

	public function diffDays(DateHelper $date)
	{
		return ($this->date() - $date->date()) / 86400;
	}

	/**
	 * @param $mode
	 *
	 * @return \Ke\Helper\DateRangeHelper
	 */
	public function getDefaultRange($mode)
	{
		if (is_numeric($mode))
			return $this->getDaysRange($mode);
		switch ($mode) {
			case 'm' :
			case 'month' :
				return new DateRangeHelper($this, $this->modify('-30 days'));
			// return $this->getMonthRange();
		}
		return $this->getWeekRange();
	}

	/**
	 * @param string $defaultRangeMode
	 * @param null   $start
	 * @param null   $end
	 *
	 * @return \Ke\Helper\DateRangeHelper
	 */
	public function mkRange($defaultRangeMode = 'w', $start = null, $end = null)
	{
		if (empty($start) && empty($end)) {
			return $this->getDefaultRange($defaultRangeMode);
		} elseif (!empty($start) && empty($end)) {
			$start = new DateHelper($start);
			// $end = $this;
			// // $diffDays = $end->diffDays($start);
			return new DateRangeHelper($start, $start);
		} elseif (!empty($end) && empty($start)) {
			$end = new DateHelper($end);
			// $start = $this;
			// return new DateRangeHelper($start, $end);
			return new DateRangeHelper($end, $end);
		} else {
			return new DateRangeHelper($start, $end);
		}
	}
}