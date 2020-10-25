<?php
	class padl {
		var $key;
		var $USE_MCRYPT 	= true;
		var $ALGORITHM 		= 'blowfish';
		var $_SERVER_VARS 	= array();
		var $ALLOW_LOCAL 	= true;
		var $REQUIRED_URIS 	= 2;
		
		function init($use_mcrypt = true){
			$this->_check_secure();
			$this->USE_MCRYPT			= ($use_mcrypt && function_exists('mcrypt_generic'));
			$this->_SERVER_VARS         = $_SERVER;
			$this->_LINEBREAK = $this->_get_os_linebreak();
			if(!$this->ALLOW_LOCAL && (strrpos($this->_SERVER_VARS['SERVER_ADDR'], '127.0.0.1') !== false)){
				trigger_error("FORBID_LOCAL", E_USER_ERROR);
				exit;
			}
		}
		/** 获取操作系统换行符**/
		function _get_os_linebreak($true_val=false){
			$os = strtolower(PHP_OS);
			switch($os){
				case 'freebsd' : 
				case 'netbsd' : 
				case 'solaris' : 
				case 'sunos' : 
				case 'linux' : 
					$nl = "\n";
					break;
				case 'darwin' : 
					if($true_val) $nl = "\r";
					else $nl = "\n";
					break;
				default :
					$nl = "\r\n";
			}
			return $nl;
		}
		/** 解码 **/
		private function _decrypt($key,$str){
			$this->_check_secure();
			$str = base64_decode(base64_decode($str));
			if($this->USE_MCRYPT){
				$td 	= mcrypt_module_open($this->ALGORITHM, '', 'ecb', '');
				$iv 	= mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
				$key 	= substr($key, 0, mcrypt_enc_get_key_size($td));
				mcrypt_generic_init($td, $key, $iv);
				$decrypt = mdecrypt_generic($td, $str);
				mcrypt_generic_deinit($td);
				mcrypt_module_close($td);
			}else{
				$decrypt 	= '';
				for($i=1; $i<=strlen($str); $i++){
					$char 		= substr($str, $i-1, 1);
					$keychar 	= substr($key, ($i % strlen($key))-1, 1);
					$char 		= chr(ord($char)-ord($keychar));
					$decrypt   .= $char;
				}
			}
			return @unserialize($decrypt);
		}
		
		/** 打开秘钥 **/
		/** function _unwrap_license($key,$enc_str){
			$str 	= trim(str_replace(array("\r", " " ,"\n", "\t"), '', $enc_str));
			$key 	= trim(str_replace(array("-"), '', $key));
			return $this->_decrypt($key,$str);
		}**/
		function _unwrap_license($enc_str){
			$str 	= trim(str_replace(array("\r", " " ,"\n", "\t"), '', $enc_str));
			$key 	= trim(substr($str, 0, 16));
			return $this->_decrypt($key,substr($str, 16));
		}
		
		function _get_config(){
			if(ini_get('safe_mode')){
				return 'SAFE_MODE';
			}
			$os = strtolower(PHP_OS);
			if(substr($os, 0, 3)=='win'){
				@exec('ipconfig/all', $lines);
				if(count($lines) == 0) return 'ERROR_OPEN';
				$conf = implode($this->_LINEBREAK, $lines);
			}else{
				$os_file = $this->_get_os_var('conf', $os);
				$fp = @popen($os_file, "rb");
				if (!$fp) return 'ERROR_OPEN';
				$conf = @fread($fp, 4096);
				@pclose($fp);
			}
			return $conf;
		}
		function _get_mac_address(){
			$conf = $this->_get_config();
			$os = strtolower(PHP_OS);
			if(substr($os, 0, 3)=='win'){
				$lines = explode($this->_LINEBREAK, $conf);
				foreach ($lines as $key=>$line){
					if(preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $line)) {
						$trimmed_line = trim($line);
						return trim(substr($trimmed_line, strrpos($trimmed_line, " ")));
					}
				}
			}else{
				$mac_delim = $this->_get_os_var('mac', $os);
				$pos = strpos($conf, $mac_delim);
				if($pos){
					$str1 = trim(substr($conf, ($pos+strlen($mac_delim))));
					return trim(substr($str1, 0, strpos($str1, "\n")));
				}
			}
			return 'MAC_404'; 
		}
		/** 删除所有的类值，以防止关键的重写 **/
		function make_secure($report=false){
			if($report) define('_PADL_REPORT_ABUSE_', true);
			foreach(array_keys(get_object_vars($this)) as $value){
				unset($this->$value);
			}
			define('_PADL_SECURE_', 1);
		}
		
		/** 安全检查 **/
		function _check_secure(){
			if(defined('_PADL_SECURE_')){	
				trigger_error("<br /><br /><span style='color: #F00;font-weight: bold;'>The PHP Application Distribution License System (PADL) has been made secure.<br />You have attempted to use functions that have been protected and this has terminated your script.<br /><br /></span>", E_USER_ERROR);
				exit;
			}
		}
	}	
?>