<?php
include_once('pinyin_table.php');

class pinyin
{
	private static $instance = null;
	private static $pinyin_table;

	static public function getInstance(){
		if(!self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}


	public function get_pinyin_array($string)
	{
		if(!self::$pinyin_table)
		{
			self::$pinyin_table = get_pinyin_table();
		}
		
		$flow = array();
		for ($i=0;$i<strlen($string);$i++)
		{
			if (ord($string[$i]) >= 0x81 and ord($string[$i]) <= 0xfe)
			{
				$h = ord($string[$i]);
				if (isset($string[$i+1]))
				{
					$i++;
					$l = ord($string[$i]);
					if (isset(self::$pinyin_table[$h][$l]))
					{
						array_push($flow,self::$pinyin_table[$h][$l]);
					}
					else
					{
						array_push($flow,$h);
						array_push($flow,$l);
					}
				}
				else
				{
					array_push($flow,ord($string[$i]));
				}
			}
			else
			{
				array_push($flow,ord($string[$i]));
			}
		}

		//print_r($flow);
		return $flow;
	}

	public function get_pinyin($str)
	{
		$flow = $this -> get_pinyin_array($str);

		$pinyin = null;
		for ($i = 0; $i < sizeof($flow); $i++)
		{
			//如果是取到了这个字的拼音
			if (is_array($flow[$i]))
			{
				$pinyin .= $flow[$i][0];
			}
			else
			{
				$pinyin .= chr($flow[$i]);
			}
		}
		return $pinyin;
	}


	/**
	 * 得到一个字符串的所有的字的声母
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	public function get_initial($str)
	{
		$flow = $this -> get_pinyin_array($str);

		$pinyin = null;
		for ($i = 0; $i < sizeof($flow); $i++)
		{
			//如果是取到了这个字的拼音
			if (is_array($flow[$i]))
			{
				$pinyin .= substr($flow[$i][0],0,1);
			}
			else
			{
				$pinyin .= chr($flow[$i]);
			}
		}
		return $pinyin;
	}


	/**
	 *  得到字符串的第一个字的第一个字母
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	public function get_first_pinyin($str)
	{
		$flow = $this -> get_pinyin_array($str);

		$pinyin = null;
		if (is_array($flow[0]))
		{
			return substr($flow[0][0],0,1);
		}
		else
		{
			return chr($flow[0]);
		}
	}
}

/*
$text = <<< EOT
看看多音字的情况，比如：还、乐。
EOT;

$py = new pinyin();

echo $py -> get_pinyin($text."\n");
echo $py -> get_first_pinyin($text);
*/