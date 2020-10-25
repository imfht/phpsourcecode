<?php

/**
 * 附件模块
 * 
 * @author ShuangYa
 * @package Blog
 * @category Model
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\model;

use \Sy;
use \sy\lib\db\Mysql;
use \sy\base\Router;
use \blog\libs\Common;

class Attachment {
	protected static $remote = ['ftp' => 'FTP', 'qiniu' => '七牛', 'upyun' => '又拍云', 'alioss' => '阿里云OSS', 'aliwantu' => '阿里顽兔'];
	/**
	 * 检查是否合法
	 * @access public
	 * @param array $f 文件
	 * @param array option 选项
	 * @return boolean
	 */
	public static function check($f) {
		$tmp_dir = pathinfo(Common::getTempName(), PATHINFO_DIRNAME);
		//如果是临时目录下的文件，直接放行
		if (substr($f['tmp_name'], 0, strlen($tmp_dir)) === $tmp_dir) {
			return TRUE;
		}
		if ($f['error'] !== UPLOAD_ERR_OK) {
			return FALSE;
		}
		if (!is_uploaded_file($f['tmp_name'])) {
			return FALSE;
		}
		$ext = pathinfo($f['name'], PATHINFO_EXTENSION);
		if (in_array(strtolower($ext), Sy::$app->get('notupload'), TRUE)) {
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 获取文件类型
	 * @access public
	 * @param string $name
	 * @param boolean $is_ext 是否为扩展名
	 * @return int
	 */
	public static function getFileType($name, $is_ext = FALSE) {
		$img = ['bmp', 'tif', 'psd', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
		$doc = ['doc', 'txt', 'docx', 'xls', 'xlsx', 'pdf', 'ppt', 'pptx', 'et', 'wps'];
		$pack = ['zip', 'rar', '7z', 'tar', 'gz', 'cab', 'lzma', 'bz2', 'bzip2', 'gzip'];
		$code = ['css', 'js', 'php', 'php3', 'php4', 'php5', 'asp', 'aspx', 'jsp', 'vbs', 'inc', 'c', 'h', 'cpp', 'hpp', 'sql', 'py', 'pl', 'lua', 'java'];
		$media = ['wma', 'wmv', 'wm', 'rm', 'rmvb', 'vob', 'mov', '3gp', 'amr', 'avi', 'mkv', 'mp4', 'mpeg', 'mpg', 'webm', 'ape', 'm4a', 'mid', 'midi', 'mp3', 'ogm', 'ogg', 'flv'];
		if (!$is_ext) {
			$info = pathinfo($name);
			$ext = strtolower($info['extension']);
		} else {
			$ext = strtolower($name);
		}
		if (in_array($ext, $img, TRUE)) {
			return 1; //图片
		} elseif (in_array($ext, $doc, TRUE)) {
			return 2; //文档
		} elseif (in_array($ext, $pack, TRUE)) {
			return 3; //打包文件
		} elseif (in_array($ext, $code, TRUE)) {
			return 4; //代码
		} elseif (in_array($ext, $media, TRUE)) {
			return 5; //媒体文件
		} else {
			return 0; //未知
		}
	}
	/**
	 * 获取支持的远程附件
	 * @access public
	 * @return array
	 */
	public static function getRemoteSupport() {
		return self::$remote;
	}
	/**
	 * 上传附件
	 * @access public
	 * @param mixed $from 原文件路径或$_FILES
	 * @param string $to 存放路径
	 * @return mixed
	 */
	public static function add($from, $to) {
		if (is_array($from)) {
			$_local = $from['tmp_name'];
			$_info = pathinfo($from['name']);
			$_ext = $_info['extension'];
			$_size = $from['size'];
			$_name = addslashes($from['name']);
		} else {
			$_local = $from;
			$_info = pathinfo($to);
			$_ext = $_info['extension'];
			$_size = filesize($from);
			$_name = 'unknow';
		}
		$attachmentType = Common::option('attachmentType');
		if ($attachmentType === 'local') {
			$r = self::local($_local, $to);
		} else {
			$r = self::remoteDo($attachmentType, 'upload', [$_local, $to]);
		}
		if ($r !== FALSE) {
			//上传成功，加入数据库
			if ($attachmentType === 'local') {
				$url = Router::createUrl() . str_replace('@root/', '', $to);
			} else {
				$url = Common::option('attachmentUrl') . $to;
			}
			$url_en = addslashes($url);
			$type = self::getFileType($_ext, TRUE);
			$time = time();
			Mysql::i()->query("INSERT INTO `#@__attachment`(`name`,`type`,`size`,`url`,`time`) VALUES ('$_name','$type','$_size','$url_en','$time')");
			$id = Mysql::i()->getLastId();
			return [$id, $type, $url];
		} else {
			return FALSE;
		}
	}
	/**
	 * 删除
	 * @access public
	 * @param int $id
	 * @return boolean?
	 */
	public static function del($id) {
		$attachmentType = Common::option('attachmentType');
		$info = Mysql::i()->getOne('SELECT url FROM `#@__attachment` WHERE id = ?', [$id]);
		if (empty($info['url'])) {
			return TRUE;
		}
		$sql = 'DELETE FROM `#@__attachment` WHERE id = ?';
		$path = parse_url($info['url'], PHP_URL_PATH);
		if ($attachmentType === 'local') {
			$fullpath = Sy::$webrootDir . $path;
			if (!is_file($fullpath) || @unlink($fullpath)) {
				Mysql::i()->query($sql, [$id]);
				return TRUE;
			}
		} else {
			if (self::remoteDo($attachmentType, 'delete', [$path])) {
				Mysql::i()->query($sql, [$id]);
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
	/**
	 * 设置附件名称
	 * @access public
	 * @param int $id
	 * @param string $name
	 * @return int
	 */
	public static function setName($id, $name) {
		$type = self::getFileType($name);
		$name = addslashes($name);
		Mysql::i()->query("UPDATE `#@__attachment` SET name = '$name', type = '$type' WHERE id = ?", [$id]);
		return $type;
	}
	/**
	 * 本地附件处理
	 * @access public
	 * @param string $from 原文件
	 * @param string $to 存放到
	 * @return boolean
	 */
	public static function local($from, $to) {
		$to = str_replace('@root/', rtrim(str_replace('\\', '/', realpath(Sy::$appDir . '../')), '/') . '/', $to);
		$to_info = pathinfo($to);
		if (!is_dir($to_info['dirname'])) {
			Common::mkdirs($to_info['dirname']);
		}
		if (move_uploaded_file($from, $to)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/**
	 * 远程实际操作
	 * @access public
	 * @param string $type
	 * @param string $action
	 * @param array $param
	 */
	public static function remoteDo($type, $action, $param = []) {
		if (!isset(self::$remote[$type])) {
			return FALSE;
		}
		$classname = ucfirst($type) . '_Attachment';
		if (!class_exists($classname, FALSE)) {
			if (is_file(Sy::$appDir . 'libs/attachment/' .$type . '/action.php')) {
				require(Sy::$appDir . 'libs/attachment/' .$type . '/action.php');
			} else {
				return FALSE;
			}
		}
		if ($action === 'getForm' || $action === 'config') {
			$att = new $classname(FALSE);
		} else {
			$att = new $classname();
		}
		if (method_exists($att, $action)) {
			$result = call_user_func_array([$att, $action], $param);
			return $result;
		}
	}
	/**
	 * 生成附件名称
	 * @access public
	 * @param string $format 格式
	 * @param string $ext 扩展名
	 * @param string $rand 是否随机
	 * @return string
	 */
	public static function getName($format, $ext, $rand = TRUE) {
		//时间戳
		$save = str_replace('{{time}}', $_SERVER['REQUEST_TIME'], $format);
		//IP
		$save = str_replace('{{ip}}', $_SERVER['REMOTE_ADDR'], $save);
		//时间
		$save = preg_replace_callback('#\{\{date:(.*?)\}\}#', function ($matches) {
			return date($matches[1], $_SERVER['REQUEST_TIME']);
		}, $save);
		if ($rand) {
			//随机数字
			$save = preg_replace_callback('#\{\{rand:(\d+)\}\}#', function ($matches) {
				$length = intval($matches[1]);
				$start = intval('1' . str_repeat('0', $length - 1));
				$stop = intval(str_repeat('9', $length));
				return mt_rand($start, $stop);
			}, $save);
			//随机字符串
			$save = preg_replace_callback('#\{\{randstr:(\d+)\}\}#', function ($matches) {
				$length = intval($matches[1]);
				return Common::getRandStr($length);
			}, $save);
		}
		//扩展名
		$save = str_replace('{{ext}}', $ext, $save);
		return $save;
	}
}
