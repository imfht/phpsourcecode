<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class DateRangeCore extends ObjectModel
{	
	public $time_start;
	public $time_end;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'date_range',
		'primary' => 'id_date_range',
		'fields' => array(
			'time_start' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
			'time_end' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
		),
	);

	public static function getCurrentRange()
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_date_range`, `time_end`
		FROM `'._DB_PREFIX_.'date_range`
		WHERE `time_end` = (SELECT MAX(`time_end`) FROM `'._DB_PREFIX_.'date_range`)');
		if (!$result['id_date_range'] || strtotime($result['time_end']) < strtotime(date('Y-m-d H:i:s')))
		{
			// The default range is set to 1 day less 1 second (in seconds)
			$rangeSize = 86399;
			$dateRange = new DateRange();
			$dateRange->time_start = date('Y-m-d');
			$dateRange->time_end = strftime('%Y-%m-%d %H:%M:%S', strtotime($dateRange->time_start) + $rangeSize);
			$dateRange->add();
			return $dateRange->id;
		}
		return $result['id_date_range'];
	}
}


