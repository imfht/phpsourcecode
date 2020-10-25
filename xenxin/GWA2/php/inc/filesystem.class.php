<?php
/* Files and Directories reading and writing, handling IO transactions
 * v0.1
 * wadelau@ufqi.com
 * Sat Jul 23 09:50:58 UTC 2011
 * updates filea, filedriver, by Xenxin@Pbtt, Thu, 27 Oct 2016 08:36:48 +0800
 * default set as *unix file system
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");

class FileSystem {
	
    var $file = null;
    var $fp = null;
    var $handlelist = array();
    const Access_Mode = 'r';
    const Write_Locking = true;
    const Write_Mode = 'w';
    var $uplddir = '';
    var $reuse = false;
    
 	//- construct
	function __construct($config=null){
		//- init at first access
		$this->uplddir = $config->uplddir;
		$this->reuse = $config->reuse;
	}
	
	//- destruct
	function __destruct(){
	    $this->close();
	}

	//- open
	private function _open($file, $args=null){
		$rtn = true;
		$args = $args==null ? array() : $args;
		$this->fp = $this->handlelist[$file]['filepointer'];
		if(!$this->fp){
			# new open
			if(!is_file($file)){
				$dir = dirname($file);
				# test dir
				if(!is_dir($dir)){
					$mdrtn = mkdir($dir, 0777, true);
					if(!$mdrtn){
						debug(__FILE__.": open: mkdir failed. 1611051117.");
						$rtn = false;
						return $rtn;
					}
				}
			}
			$accessmode = self::Access_Mode;
			if($args['mode'] != ''){
				$accessmode = $args['mode'];
			}
			$this->fp = fopen($file, $accessmode, true);
			if(!$this->fp){
				debug(__FILE__.": _open: failed to open [$file]. 1611051128.");
				$rtn = false;
				return $rtn;
			}
			else{
				# make $fp reusable from settings
				if($args['reuse'] || $this->reuse){
					$this->handlelist[$file] = array('filepointer'=>$this->fp, 
							'accessmode'=>$accessmode, 'reuse'=>true);
				}
			}
		}
		return $rtn;
	}
	
	//- read
	//- return string
	function read($file, $args=null){
		$cont = ''; $rtncode = true;
		if($this->_open($file, $args)){
			$length = 0; 
			if($args['length'] != ''){
				$length = $args['length'];
			}
			else{
				$length = filesize($file);
			}
			$cont = fread($this->fp, $length);
			if(!$this->handlelist[$file]['reuse']){
				fclose($this->fp);
			}
		}
		else{
			# read failed.
			$rtncode = false;
		}
		return $cont;
	}
	
	//- write
	//- return true | false;
	function write($file, $content, $args=null){
		$rtn = false;
		if($args != null){
			if($args['isappend']){
				$args['mode'] = 'a';
			}
			else{
				$args['mode'] = self::Write_Mode;
			}
		}
		else{
			$args['mode'] = self::Write_Mode;
		}
		if($this->_open($file, $args)){
			$accessmode = self::Access_Mode;
			if($args['mode'] != ''){
				$accessmode = $args['mode'];
			}
			if(inList($accessmode, 'a,a+,ab,a+b,w,w+,wb,w+b')){
				fseek($this->fp, 0, SEEK_END);
			}
			$needlock = self::Write_Locking;
			if($args['islock'] != ''){
				$needlock = $args['islock'];
			}
			if($needlock){
				# write with lock
				if(flock($this->fp, LOCK_EX)){
					$wrtn = fwrite($this->fp, $content);
					if(!$wrtn){
						debug(__FILE__.": write: failed. 1611051442.");
					}
					else{
						# write succ.
						$rtn = true;
					}
					flock($this->fp, LOCK_UN);
				}
				else{
					debug(__FILE__.": write: lock failed. 1611051444.");
					return $rtn;
				}
			}
			else{
				$wrtn = fwrite($this->fp, $content);
				if(!$wrtn){
					debug(__FILE__.": write: failed. 1611051442.");
				}
				else{
					# write succ.
					$rtn = true;
				}
			}
			if(!$this->handlelist[$file]['reuse']){
				fclose($this->fp);
			}
		}
		else{
			# write failed.
		}
		return $rtn;
	}
	
	//- close
	function close($myfp=null){
	    //- @todo
		foreach ($this->handlelist as $k=>$v){
			if(is_object($v['filepointer'])){
				fclose($v['filepointer']);
			}
		}
		return true;
	}
	
	/*
	 * WebApp.class has been added two extended methods as
		$webapp->setBy('url:', $args);
		$webapp->setBy('file:', $args);
		$webapp->setBy('cache:', $args);
		$webapp->getBy('url:', $args);
		$webapp->getBy('file:', $args);
		$webapp->getBy('cache:', $args);
	* 2016-05-10
	*/
	
 }
?>
