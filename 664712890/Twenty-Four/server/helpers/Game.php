<?php

namespace app\helpers;
use app\libs\Parser;
use app\libs\Question;

class Game{
	
	/**
	 * 检测用户答题结果
	 */
	public static function check($str) {
		Logger::add("Post str:".$str);
		
		$room = \app\manager\Room::instance()->getCurrent();
		if(!$room) return false;
		
		list($numbers, $solution) = $room->getQuestion();
		
		$str = trim($str);
		
		if(!$solution) return (int)$str === -1;
		
		// 校验表达式
		if( !preg_match("#^(?:\d+[\+\-\*\/]){3}\d+$#", $str) ) {
			Logger::add("Error expression.");
			return false;
		}
		
		// 输入数字校验
		$arr = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
		$inputNumbers = array_diff($arr, ['+', '-', '*', '/']);
		$diff = array_diff($numbers, $inputNumbers);
		
		if( !empty($diff) ) {
			Logger::add("Invalid number exists!");
			return false;
		}
		
		return null !== Question::test($arr);
	}
	
}