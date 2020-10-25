<?php

namespace app\libs;

/**
 * 简单数学表达式解析器
 */
 
class Parser{
	// 括号 优先处理
	protected static $brackets = [
		['(', ')']
	];
	
	// 二元运算符
	protected static $twoOperators = [
		['*'=>1, '/'=>1],
		['+'=>1, '-'=>1]
	];
	
	/**
	 * 二元运算符计算
	 */
	public static function oper($m, $f, $n) {
		switch($f) {
			case '*':
				return $m * $n;
			case '/':
				if($n == 0) throw new \Exception('Division by zero!');
				return $m / $n;
			case '+':
				return $m + $n;
			case '-':
				return $m - $n;
		}
		
		return false;
	}
	
	// 分割字符串
	public static function parseStr($str) {
		$arr = preg_split("##", $str, -1, PREG_SPLIT_NO_EMPTY);
		
		return self::parseArr($arr);
	}
	
	// 解析
	public static function parseArr($arr) {
		$arr = self::parseBracket($arr);
		
		$val = $arr ? self::parseTwoOperator($arr) : false;
		
		return $val;
	}
	
	/**
	 * 处理 括号等 高优先级的情况，简化为基础表达式
	 */
	public static function parseBracket($arr) {
		if(false === $arr) return false;
		
		foreach(self::$brackets as $bracket) {
			$tagCount = 0;
			
			// 初始化一个新数组，用来存放 去掉括号后的数据
			$newArr = [];
			$i = 0;
			
			foreach($arr as $item) {
				if($item == $bracket[0]) {
					
					// 在遇到 正括号时，
					// 若是第一个括号，则初始化一个 临时数组 tmp，用来存放 这个括号里面的 表达式
					// 否则，将括号也放入数组内
					$tagCount == 0 ? $tmp = [] : $tmp[] = $item;
					$tagCount++;
				} else if($item == $bracket[1]) {
					
					// 若遇到 反括号时，
					// 括号数量为1，则配对成功，进行 递归解析
					// 否则，将反括号 放入 临时数组 tmp
					if($tagCount == 1) {
						!empty($tmp) && $newArr[$i++] = self::parseArr($tmp);
					} else {
						$tmp[] = $item;
					}
					
					// 遇到反括号，则数量减 1
					$tagCount--;
 				} else {
 					
					// 判断 tagCount
					// 等于 0 表示 不在括号内，将原始直接放入新数组
					// 大于 0 则表示 在括号内，则放入临时数组
					$tagCount == 0 ? $newArr[$i++] = $item : $tmp[] = $item;
				}
			}
			
			// 用新数组 替换 旧数组，进行 下一个符合运算
			$arr = $newArr;
		}
		
		// $tagCount 不为0 表示括号不配对
		return $tagCount === 0 ? $arr : false;
	}
	
	/**
	 * 处理二元运算符
	 */
	public static function parseTwoOperator($arr) {
		foreach(self::$twoOperators as $operator) {
			$newArr = [];
			$i = 0;
			$len = count($arr);
			
			for($j = 0; $j < $len; $j++) {
				
				// 若 符合操作符，则进行运算(将新数组的最后一个数，和 旧数字的下一个数 进行运算)
				// 不符合，则放入新数组
				isset($operator[$arr[$j]]) ? 
					$newArr[$i-1] = self::oper($newArr[$i-1], $arr[$j], $arr[++$j]) : 
					$newArr[$i++] = $arr[$j];
			}
			
			$arr = $newArr;
		}
		
		return $arr[0]*1;
	}
}