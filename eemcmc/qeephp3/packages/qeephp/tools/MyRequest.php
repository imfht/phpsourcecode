<?php namespace qeephp\tools;

/**
 * MyRquest 请求类
 *
 * 实现一些客户端数据的获取, 与 \qeeephp\mvc\Request 形成互补
 */
class MyRequest
{
	/**
	 * IP 地址
	 *
	 * @var string
	 */
	static protected $ip_address	= FALSE;

	/**
	 * 浏览器的user agent信息
	 *
	 * @var string
	 */
	static protected $user_agent	= FALSE;

	/**
	 * 获取 IP 地址
	 *
	 * @return	string
	 */
	static function ip_address()
	{
		if (self::$ip_address !== FALSE)
		{
			return self::$ip_address;
		}

		if (server('REMOTE_ADDR') AND server('HTTP_CLIENT_IP'))
		{
			self::$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (server('REMOTE_ADDR'))
		{
			self::$ip_address = $_SERVER['REMOTE_ADDR'];
		}
		elseif (server('HTTP_CLIENT_IP'))
		{
			self::$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (server('HTTP_X_FORWARDED_FOR'))
		{
			self::$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if (self::$ip_address === FALSE)
		{
			self::$ip_address = '0.0.0.0';
			return self::$ip_address;
		}

		if (strpos(self::$ip_address, ',') !== FALSE)
		{
			$x = explode(',', self::$ip_address);
			self::$ip_address = trim(end($x));
		}

		if ( ! self::valid_ip(self::$ip_address))
		{
			self::$ip_address = '0.0.0.0';
		}

		return self::$ip_address;
	}

	/**
	 * 验证 IP 地址
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	static function valid_ip($ip)
	{
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) != 4)
		{
			return FALSE;
		}
		// IP can not start with 0
		if ($ip_segments[0][0] == '0')
		{
			return FALSE;
		}
		// Check each segment
		foreach ($ip_segments as $segment)
		{
			// IP segments must be digits and can not be
			// longer than 3 digits or greater then 255
			if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 *  浏览器的 User Agent 信息
	 *
	 * @access	public
	 * @return	string
	 */
	static function user_agent()
	{
		if (self::$user_agent !== FALSE)
		{
			return self::$user_agent;
		}

		return self::$user_agent = server('HTTP_USER_AGENT');
	}
}