<?php

namespace app\libs;

class Question{
	
	public static $tags = [];
	
	public static function init() {
		self::$tags = self::combine(['+', '-', '*', '/'], 3, 1);
	}
	
	public static function create($max = 9) {
		$question = [];
		for($i = 0; $i < 4; $i++) {
			$question[] = rand(1, $max);
		}
		
		$numbers = self::combine($question, 4);
		$result = self::solve($numbers, self::$tags);
		
		return [$question, $result];
	}
	
	/**
	 * arr 元素数组，
	 * m 从arr 中选择的元素个数
	 * isRepeat arr中的元素是否可以重复
	 * b 中间变量
	 * M 等于第一次调用时的 m
	 * res 存放结果
	 */
	public static function combine($arr, $m, $isRepeat = 0, $b = [], $n = 0, $res = []) {
		!$n && $n = $m;
		
		if($m == 1) {
			foreach($arr as $item)
				$res[] = array_merge($b, [$item]);
		} else {
			foreach($arr as $key => $item) {
				$b[$n - $m] = $item;
				
				$tmp = $arr;
				if(!$isRepeat) unset($tmp[$key]);
				
				$res = self::combine($tmp, $m-1, $isRepeat, $b, $n, $res); 
			}
		}
		
		return $res;
	}

	// 5种形式的括号运算
	public static $tpls = [
		"(%s%s%s)%s(%s%s%s)",
		"((%s%s%s)%s%s)%s%s",
		"%s%s((%s%s%s)%s%s)",
		"(%s%s(%s%s%s))%s%s",
		"%s%s(%s%s(%s%s%s))"
	];
	
	// 运算测试
	public static function test($data) {
		foreach(self::$tpls as $tpl) {
			$str = vsprintf($tpl, $data);
			
			try{
				$val = Parser::parseStr($str);
			} catch(\Exception $e) {
				$val = false;
			}
			
			if($val == 24) return $str;
		}
		
		return null;
	}
	
	public static function solve($nums, $tags) {
		foreach($nums as $i => $num) {
			foreach($tags as $j => $tag) {
				
				$tmp = [];
				foreach($num as $k => $item) {
					$tmp[] = $item;
					isset($tag[$k]) && $tmp[] = $tag[$k];
				}
				
				$str = self::test($tmp);
				
				if($str) break 2;
			}
		}
		
		return $str;
	}
}

Question::init();
/*
include __DIR__.'/Parser.php';
$start = microtime(true);

#print_r(Question::create());
$tmp = '(1+(2)+(3)+(4)';

echo Parser::parseStr($tmp) . "\n";

echo microtime(true) - $start;
*/
