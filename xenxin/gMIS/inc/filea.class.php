<?php
/* Administrator of Files and Directories reading and writing, handling IO transactions
 * v0.1
 * wadelau@ufqi.com
 * Sat Jul 23 09:50:58 UTC 2011
 * updates filea, filedriver, by Xenxin@Pbtt, Thu, 27 Oct 2016 08:36:48 +0800
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");
require_once(__ROOT__."/inc/filesystem.class.php");

class FileA {
	
    var $conf = null;
    var $filehdl = array();
    var $isclosed = true;
    var $uplddir = '';
    
 	//- construct
	function __construct($fileConf=null){
		//- open as first access
		$fileConf = ($fileConf==null ? 'File_System' : $fileConf);
		$this->conf = new $fileConf;
		$fileDriver = GConf::get('filedriver');
		$this->filehdl = new $fileDriver($this->conf);
	}

	//-
	function __destruct(){
	    $this->close();
	}
	
	//- read
	//- return string
	function read($file, $args=null){
		$cont = '';
		$cont = $this->filehdl->read($file, $args); # $fp reusable by $args['reuse']=true
		return $cont;
	}
	
	//- write
	//- return true | false
	function write($file, $content, $args=null){
		$rtn = false;
		$rtn = $this->filehdl->write($file, $content, $args); # $fp reusable by $args['reuse']=true
		return $rtn;
	}
	
	//- close
	private function close(){
	    //- @todo
	    # need sub class override.
	    $this->filehdl->close();
	}
	
	
	# Todo: to be implemented in second stage for uniting the storage engine.
 	# need todo ....
	# first try @2016-11-05, Saturday
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
