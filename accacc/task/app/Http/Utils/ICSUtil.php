<?php

namespace App\Http\Utils;

class ICSUtil {
	const DT_FORMAT = 'Ymd\THis';
	protected $properties = array ();
	private $available_properties = array (
			'description',
			'dtend',
			'dtstart',
			'location',
			'summary',
			'url' 
	);
	public $cal_name = "Montage Tasks";
	public $cal_desc = "Montage GTD Util By Edison An";
	public function __construct($props) {
		$this->set ( $props );
	}
	public function set($key, $val = false, $num = 0) {
		if (is_array ( $key )) {
			foreach ( $key as $k => $v ) {
				$this->set ( $k, $v );
			}
		} elseif (is_array ( $val )) {
			foreach ( $val as $k => $v ) {
				$this->set ( $k, $v, $key );
			}
		} else {
			if (in_array ( $key, $this->available_properties )) {
				if (! isset ( $this->properties [$num] ) || ! is_array ( $this->properties [$num] )) {
					$this->properties [$num] = [ ];
				}
				$this->properties [$num] [$key] = $this->sanitize_val ( $val, $key );
			}
		}
	}
	public function to_string() {
		$rows = $this->build_props ();
		return implode ( "\r\n", $rows );
	}
	private function build_props() {
		// Build ICS properties - add header
		$ics_props = array (
				'BEGIN:VCALENDAR',
				'VERSION:2.0',
				'PRODID:-//Edison An//NONSGML v1.0//EN',
				'CALSCALE:GREGORIAN',
				'X-WR-CALNAME:' . $this->cal_name,
				'X-WR-TIMEZONE:Asia/Shanghai',
				'X-WR-CALDESC:' . $this->cal_desc 
		);
		
		foreach ( $this->properties as $key => $val ) {
			$ics_props [] = 'BEGIN:VEVENT';
			foreach ( $val as $k => $v ) {
				$ics_props [] = strtoupper ( $k . ($k === 'url' ? ';VALUE=URI' : '') ) . ':' . $v;
			}
			$ics_props [] = 'DTSTAMP:' . $this->format_timestamp ( 'now' );
			$ics_props [] = 'UID:' . uniqid ();
			$ics_props [] = 'END:VEVENT';
		}
		
		$ics_props [] = 'END:VCALENDAR';
		
		return $ics_props;
	}
	private function sanitize_val($val, $key = false) {
		switch ($key) {
			case 'dtend' :
			case 'dtstamp' :
			case 'dtstart' :
				$val = $this->format_timestamp ( $val );
				break;
			default :
				$val = $this->escape_string ( $val );
		}
		
		return $val;
	}
	private function format_timestamp($timestamp) {
		$dt = new \DateTime ( $timestamp );
		return $dt->format ( self::DT_FORMAT );
	}
	private function escape_string($str) {
		return preg_replace ( '/([\,;])/', '\\\$1', $str );
	}
}
class ICSUtil2 {
	const DT_FORMAT = 'Ymd\THis';
	protected $properties = array ();
	private $available_properties = array (
			'trigger',
			'dtstamp',
			'dtend',
			'dtstart',
			'due',
			'completed',
			'repeat',
			'priority',
			'status',
			'summary' 
	);
	public function __construct($props) {
		$this->set ( $props );
	}
	public function set($key, $val = false, $num = 0) {
		if (is_array ( $key )) {
			foreach ( $key as $k => $v ) {
				$this->set ( $k, $v );
			}
		} elseif (is_array ( $val )) {
			foreach ( $val as $k => $v ) {
				$this->set ( $k, $v, $key );
			}
		} else {
			if (in_array ( $key, $this->available_properties )) {
				if (! isset ( $this->properties [$num] ) || ! is_array ( $this->properties [$num] )) {
					$this->properties [$num] = [ ];
				}
				$this->properties [$num] [$key] = $this->sanitize_val ( $val, $key );
			}
		}
	}
	public function to_string() {
		$rows = $this->build_props ();
		return implode ( "\r\n", $rows );
	}
	private function build_props() {
		// Build ICS properties - add header
		$ics_props = array (
				'BEGIN:VCALENDAR',
				'VERSION:2.0',
				'PRODID:-//Edison An//NONSGML v1.0//EN',
				'CALSCALE:GREGORIAN',
				'X-WR-CALNAME:Montage Tasks',
				'X-WR-TIMEZONE:Asia/Shanghai',
				'X-WR-CALDESC:Montage GTD Util By Edison An' 
		);
		
		foreach ( $this->properties as $key => $val ) {
			$ics_props [] = 'BEGIN:VTODO';
			foreach ( $val as $k => $v ) {
				$ics_props [] = strtoupper ( $k . ($k === 'url' ? ';VALUE=URI' : '') ) . ':' . $v;
			}
			$ics_props [] = 'DTSTAMP:' . $this->format_timestamp ( 'now' );
			$ics_props [] = 'UID:' . uniqid ();
			$ics_props [] = 'END:VTODO';
		}
		
		$ics_props [] = 'END:VCALENDAR';
		
		return $ics_props;
	}
	private function sanitize_val($val, $key = false) {
		switch ($key) {
			case 'dtend' :
			case 'dtstamp' :
			case 'dtstart' :
				$val = $this->format_timestamp ( $val );
				break;
			default :
				$val = $this->escape_string ( $val );
		}
		
		return $val;
	}
	private function format_timestamp($timestamp) {
		$dt = new \DateTime ( $timestamp );
		return $dt->format ( self::DT_FORMAT );
	}
	private function escape_string($str) {
		return preg_replace ( '/([\,;])/', '\\\$1', $str );
	}
}