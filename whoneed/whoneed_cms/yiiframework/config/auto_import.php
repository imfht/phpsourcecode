<?php
/**
 * 自动导入程序包类	import system.extentions.*
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 * @package		system.extentions
 */
	$strCIFName	= WEB_ROOT.'/runtime/cache/auto_import_files.php';
	$strCTime	= 86400;
	$objCache = new cache($strCIFName, $strCTime);

	if($objCache->cache_is_active()){
		include($strCIFName);
	}else{
		$arrAutoLoadFiles = array();	// 遍历出将要预先导入的文件夹
		$arrImportFiles	  = array();	// 转换成符合规则的伪文件夹
		$arrAutoLoadFiles = mapTreeDirs($strYiiExtentions);
		
		if($arrAutoLoadFiles){
			$strTempFile = '';
			foreach($arrAutoLoadFiles as $v){
				$strTempFile = str_replace($strYiiExtentions, '', $v);
				$strTempFile = str_replace('/', '.', $strTempFile);

				$arrImportFiles[] = 'system.extentions'.$strTempFile.'.*';
			}
		}
		
		$str='<?php'."\n".'$arrImportFiles = '.var_export($arrImportFiles,true).';'."\n".'?>';
		$objCache->write_file($str);
	}

	return $arrImportFiles;

	// 遍历指定目前的所有文件
	function mapTreeFiles($ddir,$loop=true){
		global $arrTreeFiles;
		$handle = opendir($ddir);
		while ($file = readdir($handle)){
			$bdir=$ddir."/".$file;
			if($loop){
				if ($file<>'.' && $file<>'..' && filetype($bdir)=='dir'){  //是否还有下级目录
					mapTreeFiles($bdir,$loop);
				}elseif ($file<>'.' && $file<>'..' ) $arrTreeFiles[] = $bdir;
			}elseif ($file<>'.' && $file<>'..' && filetype($bdir)!='dir' ) $arrTreeFiles[] = $bdir;
		}
		closedir($handle);
		return $arrTreeFiles;
	}

	/**
	 * 遍历出指定目录下的所有目录
	 */
	function mapTreeDirs($ddir,$loop=true,$path=true){
		global $arrTreeDirs;
		$handle = opendir($ddir);

		while ($file = readdir($handle)){
			$bdir=$ddir."/".$file;
			if(filetype($bdir)=='dir' ) {
				if ($file<>'.' && $file<>'..') {
					if($path){
						$arrTreeDirs[] = $bdir;
					}else{
						$arrTreeDirs[] = $file;
					}
				}
			}
			if($loop){
				if ($file<>'.' && $file<>'..' && filetype($bdir)=='dir'){  //是否还有下级目录
					mapTreeDirs($bdir,$loop,$path);
				}
			}
		}
		closedir($handle);

		return $arrTreeDirs;
	}

class cache {
    var $cache_file;
    var $cache_time;

	function __construct($cache_file,$cache_time=3600) {
       $this->cache($cache_file,$cache_time);
   }

	function cache($cache_file,$cache_time=3600) {
        $this->cache_file = $cache_file;
        $this->cache_time = $cache_time;
    }
    /*
    * Start cache method without Return
    */
    function cache_start($update=false){
		if(!$update){
			if($this->cache_is_active()){
				include($this->cache_file);
				exit;
			}
		}
		ob_start();
    }

    /*
    * End of cache method without Return
    */
    function cache_end($output=true) {
        $this->make_cache();
		if($output) ob_end_flush();
		else ob_end_clean();
    }

    /*
    * Check if cache file is actived
    * Return true/false
    */
    function cache_is_active() {
        if ($this->cache_is_exist()) {
            if (time() - $this->lastModified() < $this->cache_time)
                Return true;
            else {
                Return false;
            }
        }
        else {
            Return false;
        }
    }

    /*
    * Create cache file
    * Return true/false
    */
    function make_cache() {
        $content = $this->get_cache_content();
		if(empty($content)) return false;
        if($this->write_file($content)) {
            return true;
        }
        else {
            return false;
        }
    }

    /*
    * Check if cache file is exists
    * Return true/false
    */
    function cache_is_exist() {
        if(file_exists($this->cache_file)) {
            Return true;
        }
        else {
            Return false;
        }
    }

    /*
    * Return last Modified time in bollin formart
    * Usage: $lastmodified = $this->lastModified();
    */
    function lastModified() {
        Return @filemtime($this->cache_file);
    }

    /*
    * Return Content of Page
    * Usage: $content = $this->get_cache_content();
    */
    function get_cache_content() {
        $contents = ob_get_contents();
//        Return '<!--'.date('Y-m-d H:i:s').'-->'.$contents;
        Return $contents;
    }

    /*Write content to $this->cache_file
    * Return true/false
    * Usage: $this->write_file($content);
    **/
    function write_file($content,$mode='w') {
        $this->mk_dir();
        if ($fp = fopen($this->cache_file,$mode)) {
            @fwrite($fp,$content);
            @fclose($fp);
            @umask($oldmask);
            return true;
        } else{
			$this->report_Error($this->cache_file." 目录或者文件属性无法写入.");
            return false;
        }
    }

    /*
    * Make given dir included in $this->cache_file
    * Without Return
    * Usage: $this->mk_dir();
    */
    function mk_dir()
    {    //$this->cache_file    = str_replace('','/');
        $dir    = @explode("/", $this->cache_file);
        $num    = @count($dir)-1;
        $tmp    = '';
        for($i=0; $i<$num; $i++){
			if($dir[$i] === '') {
				$tmp = '/';
				continue;
			}
            $tmp    .= $dir[$i];
            if(!@file_exists($tmp)){
                @mkdir($tmp);
                @chmod($tmp, 0777);
            }
            $tmp    .= '/';
        }

    }

    /*
    * Unlink an exists cache
    * Return true/false
    * Usage: $this->clear_cache();
    */
    function clear_cache() {
        if (!@unlink($this->cache_file)) {
            $this->report_Error('Unable to remove cache');
            Return false;
        }
        else {
            Return true;
        }
    }

    /*
    * Report Error Messages
    * Usage: $this->report_Error($message);
    */
    function report_Error($message=NULL) {
		if($message!=NULL) {
			trigger_error($message);
		}
	}
}
?>