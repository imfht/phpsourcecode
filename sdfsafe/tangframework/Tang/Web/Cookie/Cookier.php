<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Web\Cookie;
class Cookier implements ICookie
{
	protected $config = array();
	public function setConfig(array $config)
	{
		$this->config = array_replace_recursive(array('expire' => 86400,'path' => '/','domain'=>'','prefix'=>'tang-'), $config);
	}
	/**
	 * 设置一个cookie
	 * @param string $name cookie名
	 * @param string $value cookie值
	 * @param string $expire 时效
	 * @param string $path 路径
	 * @param string $domain 域
	 */
	public function set($name,$value,$expire=-1,$path='',$domain='')
	{
		if($expire == -1)
		{
			$expire = $this->config['expire'];
		}
		if(empty($path))
		{
			$path = $this->config['path'];
		}
		if(empty($domain))
		{
			$domain = $this->config['domain'];
		}
		$expire = !empty($expire) ? time()+$expire : 0;
		$value   =  base64_encode($value);
		$cookieName = $this->config['prefix'].$name;
		setcookie($cookieName,$value,$expire,$path,$domain);
		$_COOKIE[$cookieName] = $value;
	}
	/**
	 * 获得一个cookie
	 * @param string $name
     * @param null $default
	 * @return string
	 */
	public function get($name,$default=null)
	{
		$cookieName = $this->config['prefix'].$name;
		return isset($_COOKIE[$cookieName]) ? base64_decode($_COOKIE[$cookieName]) : $default;
	}
	/**
	 * 删除一个cookie
	 * @param string $name
	 * @return string
	 */
	public function delete($name)
	{
		$cookieName = $this->config['prefix'].$name;
		if(isset($_COOKIE[$cookieName]))
		{
			$this->set($name, '',-11111);
			$_COOKIE[$cookieName] = '';
		}
	}
	/**
	 * 清除cookie
	 */
	public function clean()
	{
		if($_COOKIE)
		{
			$prefix = $this->config['prefix'];
			foreach ($_COOKIE as $key => $value)
			{
				$this->delete(str_replace($prefix,'', $key));
			}
		}
	}
}