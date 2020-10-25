<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

if ( !defined('IN_PHPBB') )
{
	exit;
}

class cache
{
	var $cache_dir = '';
	
	//初始化缓存路径
	function __construct()
	{
		$this->cache_dir = ROOT_PATH . 'cache/';
	}
	
	/**
	* 从数据库中读取到 config 表的内容
	*/
	function obtain_config()
	{
		global $db;

		$sql = 'SELECT config_name, config_value
			FROM ' . CONFIG_TABLE;
			
		$result = $db->sql_query($sql);
		
		$config = array();
		
		while ($row = $db->sql_fetchrow($result))
		{
			$config[$row['config_name']] = $row['config_value'];
		}
		
		$db->sql_freeresult($result);

		return $config;
	}
	
	/**
	* 从文件系统中读取缓存内容
	* 
	* @参数 字符串 $filename 文件名
	* @返回 文件不存在或读取出错时返回 false 否则 返回 $new_data
	**/
	function read($filename)
	{
		
		$file = $this->cache_dir . $filename . '.php';
		//暂时不需要
		//$type = substr($filename, 0, strpos($filename, '_'));//返回数据的缓存类型，例如：data

		//检测文件是否存在
		if (!file_exists($file))
		{
			return false;
		}

		//返回缓存文件中的内容
		if (!($handle = @fopen($file, 'rb')))
		{
			return false;
		}

		// 跳过 ＜？ｐｈｐ　ｅｘｉｔ；　？＞ 这一行代码
		fgets($handle);
		
		$new_data = @unserialize(fgets($handle));
		//while (($data = fgets($handle)) && !feof($handle))
		//{
			//值不正确证明此文件为修改过的文件，移除
			//if (!is_numeric($bytes) || ($bytes = (int) $bytes) == 0)
			//{
			//	fclose($handle);
				
			//	$this->remove_file($file);

			//	break;
			//}
			//$new_data = @unserialize($data);
			
		//}

		return $new_data;
	}
		

	/**
	* 	将缓存中的数据写入到指定的文件
	*
	* 	文件的格式为：
	* 	<code>
	* 	<?php exit; ?>
	* 	(序列化的数据大小)
	* 	(序列化的数据)
	* 	</code>
	*
	* 	@访问 私有
	* 	@参数 字符串 $filename 写入的文件名
	* 	@参数 所有 $data 数据储存
	* 	@返回 布尔值 文件成功创建返回真，否则为假
	*/
	function write($filename, $data = null)
	{

		//指定文件
		$file = $this->cache_dir . $filename . '.php';

		//打开指定文件，如果文件不存在则创建
		if ($handle = @fopen($file, 'wb'))
		{
			//锁定
			@flock($handle, LOCK_EX);

			//在开头写入 ＜？ｐｈｐ　ｅｘｉｔ；　？＞
			//防止直接访问该文件，预防数据暴露
			fwrite($handle, '<' . '?php exit; ?' . '>');
			
			fwrite($handle, "\n");

			//缓存数据
			$data = serialize($data);

			//把数据数据的长度计算出来，写入
			//fwrite($handle, strlen($data) . "\n");
			
			//把缓存数据写入
			fwrite($handle, $data);

			//释放锁定
			@flock($handle, LOCK_UN);
			
			fclose($handle);

			if (!function_exists('phpbb_chmod'))
			{
				require(ROOT_PATH . 'includes/functions/common.php');
			}

			//设置权限
			phpbb_chmod($file, CHMOD_READ | CHMOD_WRITE);

			return true;
		}

		return false;
	}
	
	/**
	* 	最终输出
	* 	@参数 字符串 $filename 文件名 
	* 	@返回 $data
	*/
	function export($filename)
	{
		if( !($data = $this->read($filename)) )
		{
			$this->write($filename, $this->obtain_config());
			$data = $this->obtain_config();
		}
		
		return $data;
	}
	
	/**
	*	用于清除缓存
	*/
	function clear($filename, $check = false)
	{
		if (!function_exists('phpbb_is_writable'))
		{
			require(ROOT_PATH . 'includes/common.php');
		}

		if ($check && !phpbb_is_writable($this->cache_dir))
		{
			trigger_error('Unable to remove files within ' . $this->cache_dir . '. Please check directory permissions.', E_USER_WARNING);
		}
		
		$unlink_file = $this->cache_dir . $filename . '.php';
		
		return unlink($unlink_file);
	}

}
?>