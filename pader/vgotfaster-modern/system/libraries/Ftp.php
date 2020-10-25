<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Library;

/**
 * VgotFaster FTP Client
 *
 * @package VgotFaster
 * @subpackage Library
 * @author pader
 */
class Ftp {

	var $host = '';
	var $port = 21;
	var $username = '';
	var $password = '';
	var $conn    = NULL;
	var $passive = TRUE;
	var $debug   = FALSE;

	public function __construct($config=array())
	{
		if ($config) {
			$this->initialize($config);
		}
	}

	/**
	 * SYS_Ftp::initialize()
	 *
	 * @param array $config
	 * @return void
	 */
	public function initialize($config)
	{
		if ($config) {
			foreach ($config as $key => $val) {
				if (isset($this->$key)) {
					$this->$key = $val;
				}
			}
		}
	}

	/**
	 * Connect To FTP Server
	 *
	 * @param mixed $host
	 * @param mixed $username
	 * @param string $password
	 * @param mixed $passive
	 * @param mixed $port
	 * @return
	 */
	public function connect($host=NULL,$username='',$password='',$passive=NULL,$port=NULL)
	{
		//Initlize Params
		if (is_array($host) and $host) {
			$this->initialize($host);
		} elseif ($host) {
			$config = array(
				'host' => $host,
				'username' => $username,
				'password' => $password
			);
			is_null($passive) || $config['passive'] = $passive;
			is_null($port) || $config['port'] = $port;
			$this->initialize($config);
		} elseif (!$this->host) {
			showError('FTP connect has no config, Connect been stopped.',FALSE);
			return FALSE;
		}

		//Connect
		$this->conn = @ftp_connect($this->host,$this->port);

		if (!$this->conn) {
			showError("Unable to connect to FTP server '{$this->host}'",FALSE);
			return FALSE;
		}

		//Login
		$login = @ftp_login($this->conn,$this->username,$this->password);
		if (!$login) {
			showError("Unable to login to FTP server");
			return FALSE;
		}

		if ($this->passive) {
			ftp_pasv($this->conn,TRUE);
		}

		return TRUE;
	}

	/**
	 * 切换到目录
	 *
	 * @param string $path
	 * @return array
	 */
	public function chdir($path)
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		$result = @ftp_chdir($this->conn, $path);

		if ($result === FALSE) {
			if ($this->debug == TRUE) {
				showError('Unable to change FTP dir');
			}
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * 读取目录
	 *
	 * @param string $path
	 * @return array
	 */
	public function dir($path='.')
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		if (substr($path,-1) != '/') {
			$path .= '/';
		}

		$dir = ftp_rawlist($this->conn,$path);
		$data = array();

		if ($dir === FALSE) return FALSE;

		foreach ($dir as $row) {
			$ls = preg_split('/\s+/',$row,9);
			if (count($ls) < 8) return FALSE;
			if ($ls[8] == '.' or $ls[8] == '..') continue;
			$data[] = array(
				'permission' => $ls[0],
				'subdir'     => $ls[1],
				'user'       => $ls[2],
				'group'      => $ls[3],
				'filesize'   => $ls[4],
				'filemtime'  => strtotime($ls[5].' '.$ls[6].' '.$ls[7]),
				'filename'   => $ls[8],
				'isdir'      => substr($ls[0],0,1) == 'd' ? 1 : 0
			);
		}

		return $data;
	}

	/**
	 * 读取名称列表
	 *
	 * @param string $path
	 * @return array
	 */
	public function nlist($path='.')
	{
		if (!$this->isConn()) {
			return FALSE;
		}
		return ftp_nlist($this->conn,$path);
	}

	/**
	 * 上传文件到 FTP
	 *
	 * @param string $local
	 * @param string $remote
	 * @param string $mode
	 * @param int $permission
	 * @return bool
	 */
	public function upload($local,$remote,$mode='auto',$permission=NULL)
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		if (!file_exists($local)) {
			return FALSE;
		}

		if ($mode == 'auto') {
			$mode = $this->getTransferMode($local);
		}

		$mode = $mode == 'ascii' ? FTP_ASCII : FTP_BINARY;

		$result = ftp_put($this->conn,$remote,$local,$mode);

		if (!is_null($permission)) {
			$this->chmod($remote,(int)$permission);
		}

		return $result;
	}

	/**
	 * 下载文件
	 *
	 * @param string $remote
	 * @param string $local
	 * @param string $mode
	 * @return bool
	 */
	public function download($remote,$local,$mode='auto')
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		if ($mode == 'auto') {
			$mode = $this->getTransferMode($local);
		}

		$mode = $mode == 'ascii' ? FTP_ASCII : FTP_BINARY;

		return ftp_get($this->conn,$local,$remote,$mode);
	}

	/**
	 * 重命名文件或目录
	 *
	 * @param string $origName
	 * @param string $newName
	 * @return bool
	 */
	public function rename($origName,$newName)
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		return ftp_rename($this->conn,$origName,$newName);
	}

	/**
	 * 删除远程文件
	 *
	 * @param string $path
	 * @return bool
	 */
	public function delete($path)
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		$result = ftp_delete($this->conn,$path);

		return $result;
	}

	/**
	 * 移动文件
	 *
	 * @param mixed $from
	 * @param mixed $to
	 * @return bool
	 */
	public function move($from,$to)
	{
		return $this->rename($from,$to);
	}

	/**
	 * 创建一个目录
	 *
	 * @param string $dirname
	 * @param int $permission
	 * @return bool
	 */
	public function mkdir($dirname,$permission=NULL)
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		$result = ftp_mkdir($this->conn,$dirname);

		if (!is_null($permission)) {
			$this->chmod($dirname,(int)$permission);
		}

		return $result === FALSE ? FALSE : TRUE;
	}

	/**
	 * 创建多层目录
	 *
	 * @param string $path
	 * @param int $permission
	 * @return bool
	 */
	public function mkdirs($path,$permission=NULL)
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		$cur = ftp_pwd($this->conn);
		$rpath = '';

		foreach (preg_split('/\\\\|\//',$path) as $dirname) {
			$rpath .= $rpath ? '/'.$dirname : $dirname;

			$chdir = @ftp_chdir($this->conn,$rpath);
			if ($chdir) {
				$rpath = '';
				continue;
			}

			$result = ftp_mkdir($this->conn,$rpath);
			if ($result === FALSE) {
				return FALSE;
			} elseif (!is_null($permission)) {
				@ftp_chmod($this->conn,(int)$permission,$rpath);
			}
		}

		$this->chdir($cur);

		return TRUE;
	}

	/**
	 * 删除目录
	 *
	 * 注意此方法会删除目录下所有文件及目录
	 *
	 * @param string $path
	 * @return bool
	 */
	public function rmdir($dir)
	{
		if (!$this->isConn()) {
			return FALSE;
		}
		
		$ls = $this->nlist($dir);
		
		if ($ls AND is_array($ls)) {
			foreach ($ls as $file) {
				//删除失败意味着这可能是个目录，调用删除目录
				if (!@ftp_delete($this->conn,$file)) {
					$this->rmdir($file);
				}
			}
		}
		
		$result = ftp_rmdir($this->conn,$dir);
		
		return $result;
	}

	/**
	 * 修改远程文件权限
	 *
	 * @param string $path
	 * @param int $permission
	 * @return bool
	 */
	public function chmod($path,$permission)
	{
		if (!$this->isConn()) {
			return FALSE;
		}

		$result = @ftp_chmod($this->conn,$permission,$path);

		return $result === FALSE ? FALSE : TRUE;
	}

	/**
	 * 返回当前目录
	 *
	 * @return string
	 */
	public function current()
	{
		if (!$this->isConn()) {
			return FALSE;
		}
		return ftp_pwd($this->conn);
	}

	/**
	 * 关闭 FTP 连接
	 *
	 * @return void
	 */
	public function close()
	{
		ftp_close($this->conn);
	}

	/**
	 * 获取默认的传输模式
	 *
	 * @param string $path
	 * @return string
	 */
	private function getTransferMode($path)
	{
		$VF =& getInstance();
		$VF->load->helper('file');

		$ext = pathinfoCompat($path,'extension');
		if ($ext == '' OR preg_match('/^txt|text|php|phps|js|css|htm|html|phtml|shtml|log|xml$/i',$ext)) {
			return 'ascii';
		}

		return 'binary';
	}

	/**
	 * 判断 FTP 当前是否为已连接状态
	 *
	 * @return bool
	 */
	private function isConn()
	{
		if (!is_resource($this->conn)) {
			if ($this->debug == TRUE) {
				showError('Ftp no connection.');
			}
			return FALSE;
		}
		return TRUE;
	}

}
