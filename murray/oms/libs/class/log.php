<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 日志
*/

defined('INPOP') or exit('Access Denied');

class log{

	public $objFile;
	public $strIp;
	public $strLogMode;
	public $strLogTable;
	public $intFileSizeMax;
	public $booArch;
	public $db;
	
	//初始化
	public function __construct($strToFile = DEFAULT_LOG, $strToLog = '', $strTable = 'logs', $booArch = true){
		$this->db = DB::getInstance();
		if(!empty($_SERVER['REMOTE_ADDR'])){
			$this->strIp = $_SERVER['REMOTE_ADDR'];
		}else{
			$this->strIp = 'NO IP';
		}	
		$this->booArch = $booArch;
		$this->strLogMode = LOG_MODE;
		$this->strToFile = $strToFile;
		$this->strLogTable = TABLEPRE.$strTable;
		$this->intFileSizeMax = 1 * 1024 * 1024;
		if($strToLog != ''){
			//On log
			$this->logThis($strToLog);
		}
	}
	
	//打开日志
	public function openLogFile(){		
		clearstatcache();
		if(file_exists($this->strToFile) ){
			if($this->booArch === true){
				$intFileSize = filesize($this->strToFile);
				if( $intFileSize >= $this->intFileSizeMax){
					$strFileContent = file_get_contents($this->strToFile);
					$strToFileGz = $this->strToFile . '.' . date('Ymd_His') . '.gz';
					$strFileContentGz = gzencode($strFileContent,9);
					$objFileGz = $this->fopenLogFile($strToFileGz, 'w+');	
					fwrite($objFileGz,$strFileContentGz);
					fclose($objFileGz);
					unlink($this->strToFile);
					$this->objFile = $this->fopenLogFile($this->strToFile, 'w+');					
				}else{
					$this->objFile = $this->fopenLogFile($this->strToFile, 'a+');	
				}
			}else{
				$this->objFile = $this->fopenLogFile($this->strToFile, 'a+');	
			}
		}else{		
			$this->objFile = $this->fopenLogFile($this->strToFile, 'w+');				
		}	
	}	
	
	//打开日志文件
	public function fopenLogFile($strFile,$strMode){				
		$objFile = fopen($strFile, $strMode);	
		if($objFile==FALSE){						
			$strError = 'Can not open ' . $strFile . ' in mode ' . $strMode . '!';
			$strError.= "\n";				
			exit($strError);
		}
		return $objFile;
	}	
	
	//写日志
	public function logThis($strToLog){
		switch($this->strLogMode){
			case 'mysql':
				$this->logThisInTable($strToLog);
			break;
			case 'file':
				$strToLog = str_replace(array("\n","\r"),array(' ',' '),$strToLog);
				$this->openLogFile();
				$this->logThisInFile($strToLog);
				$this->closeLogFile();
			break;
		}
	}
	
	//日志写入文件
	public function logThisInFile($strToLog){		
		fwrite(
			$this->objFile,
			date('Y-m-d') . "\t" . date('H:i:s') . "\t" . $this->strIp . "\t" . $strToLog . "\n"
		);
	}
	
	//日志写入数据库表
	public function logThisInTable($strToLog){	
		$reqLog = 'INSERT INTO `' . mysql_real_escape_string($this->strLogTable) . '` ' ;	
		$reqLog.= '(`logDate`,`logTime`,`logIp`,`logString`) ';
		$reqLog.= 'VALUES(';
			$reqLog.= '\'' . date('Y-m-d') . '\',';
			$reqLog.= '\'' . date('H:i:s') . '\',';
			$reqLog.= '\'' . mysql_real_escape_string($this->strIp) . '\',';
			$reqLog.= '\'' . mysql_real_escape_string($strToLog) . '\'';
		$reqLog.= '); ';
		$objResult = $this->db->query($reqLog);
	}	
	
	//关闭日志文件
	public function closeLogFile(){
		fclose($this->objFile);
	}
	
	//清除日志
	public function purgeLog($strDateTime='0000-00-00 00:00:00'){
		if($strDateTime=='0000-00-00 00:00:00'){
			$strDateTime = date('Y-m-d H:i:s');
		}
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $strDateTime)){
			switch($this->strLogMode){
				case 'mysql':
					$this->purgeLogTable($strDateTime);
				break;
				case 'file':					
					$this->purgeLogFiles($strDateTime);
					$this->purgeLogCurentFile($strDateTime);
				break;
			}
		}else{
			exit($strDateTime . ' N\'est pas au bon format');
		}
	}
	
	//清除日志表
	public function purgeLogTable($strDateTime){
		$strDate = substr($strDateTime,0,10);
		$strTime = substr($strDateTime,11,8);
		$reqPurge = 'DELETE FROM `' . mysql_real_escape_string($this->strLogTable) . '` ';
		$reqPurge.= 'WHERE `logDate` < \'' . $strDate . '\' ';
		$reqPurge.= 'OR(`logDate` = \'' . $strDate . '\' AND `logTime` <= \'' . $strTime . '\') ;';
		$objResult = $this->db->query($reqPurge);
	}
	
	//时间转换为时间戳
	public function dateToTimestamp($strDateTime){
		return strtotime($strDateTime);
	}
	
	//清除当前日志文件
	public function purgeLogCurentFile($strDateTime){		
		$intTimeStampeDate = $this->dateToTimestamp($strDateTime);
		$strContent = '';
		$arrLineContent = array();
		$this->objFile = $this->fopenLogFile($this->strToFile, 'r+');		
		while(!feof($this->objFile)){			
			$strLineContent = fgets($this->objFile);
			$arrLineContent = explode("\t",$strLineContent);
			if( sizeof($arrLineContent) > 1 ){
				$strDateLine = $arrLineContent[0];
				$strTimeLine = $arrLineContent[1];
				$strDateTimeLine = $strDateLine . ' ' . $strTimeLine;
				$intTimeStampLine = $this->dateToTimestamp($strDateTimeLine);
				if($intTimeStampLine > $intTimeStampeDate){
					$strContent.=$strLineContent;
				}
			}
		}
		$this->closeLogFile();			
		unlink($this->strToFile);
		$this->objFile = $this->fopenLogFile($this->strToFile, 'w+');	
		fwrite($this->objFile, $strContent);
		$this->closeLogFile();				
	}
	
	//清除日志文件
	public function purgeLogFiles($strDateTime){		
		$intTimeStampeDate = $this->dateToTimestamp($strDateTime);
		$arrArchFiles = glob($this->strToFile . '.*.gz');
		foreach($arrArchFiles as $strPathToArch){
			$strDateArch = str_replace(array($this->strToFile.'.','.gz'),array(''),$strPathToArch);
			$strDateTimeArch = substr($strDateArch,0,4) . '-';
			$strDateTimeArch.= substr($strDateArch,4,2) . '-';
			$strDateTimeArch.= substr($strDateArch,6,2) . ' ';
			$strDateTimeArch.= substr($strDateArch,9,2) . ':';
			$strDateTimeArch.= substr($strDateArch,11,2) . ':';
			$strDateTimeArch.= substr($strDateArch,13,2);
			$intTimeStampArch = $this->dateToTimestamp($strDateTimeArch);
			if($intTimeStampArch <= $intTimeStampeDate){
				unlink($strPathToArch);
			}
		}		
	}
	
	//格式化日志
	public function visuLog( $strDateTimeIni='0000-00-00 00:00:00',$strDateTimeEnd='0000-00-00 00:00:00',
		$strContentToSearch='',$booDetail=true,$strIp='' ){	
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $strDateTimeIni)){
		}else{
			exit('Date Ini : ' .$strDateTimeIni . ' N\'est pas au bon format');
		}
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $strDateTimeEnd)){
		}else{
			exit('Date End : ' .$strDateTimeEnd . ' N\'est pas au bon format');
		}
		if('0000-00-00 00:00:00' == $strDateTimeEnd){
			$strDateTimeEnd = date('Y-m-d H:i:s');
		}
		$strContent = $this->returnLog($strDateTimeIni,$strDateTimeEnd,$strContentToSearch,$booDetail,$strIp);
		echo '<pre>' . $strContent . '</pre>';
	}
	
	//返回日志
	public function returnLog( $strDateTimeIni='0000-00-00 00:00:00',$strDateTimeEnd='0000-00-00 00:00:00',
		$strContentToSearch='',$booDetail=true,$strIp='' ){	
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $strDateTimeIni)){
		}else{
			exit('Date Ini : ' .$strDateTimeIni . ' N\'est pas au bon format');
		}
		if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $strDateTimeEnd)){
		}else{
			exit('Date End : ' .$strDateTimeEnd . ' N\'est pas au bon format');
		}
		if('0000-00-00 00:00:00' == $strDateTimeEnd){
			$strDateTimeEnd = date('Y-m-d H:i:s');
		}
		$strContent = '';
		switch($this->strLogMode){
			case 'mysql':
				$strContent = $this->returnLogMysqlContent(
					$strDateTimeIni,$strDateTimeEnd,$strContentToSearch,$booDetail,$strIp
				);
			break;
			case 'file':					
				$strContent.= $this->returnLogArchFilesContent(
					$strDateTimeIni,$strDateTimeEnd,$strContentToSearch,$booDetail,$strIp
				);
				$strContent.= $this->returnLogFileContent(
					$strDateTimeIni,$strDateTimeEnd,$strContentToSearch,$booDetail,$strIp
				);				
			break;
		}
		return $strContent;
	}	
	
	//从压缩文件返回日志
	public function returnLogArchFilesContent( $strDateTimeIni,$strDateTimeEnd,$strContentToSearch,$booDetail,$strIp ){	
		$strContentRequired = '';
		$intTimeStampDateIni = $this->dateToTimestamp($strDateTimeIni);
		$intTimeStampDateEnd = $this->dateToTimestamp($strDateTimeEnd);
		$arrArchFiles = glob($this->strToFile . '.*.gz');
		$arrFilesToParse = array();
		foreach($arrArchFiles as $strPathToArch){
			$strDateArch = str_replace(array($this->strToFile.'.','.gz'),array(''),$strPathToArch);
			$strDateTimeArch = substr($strDateArch,0,4) . '-';
			$strDateTimeArch.= substr($strDateArch,4,2) . '-';
			$strDateTimeArch.= substr($strDateArch,6,2) . ' ';
			$strDateTimeArch.= substr($strDateArch,9,2) . ':';
			$strDateTimeArch.= substr($strDateArch,11,2) . ':';
			$strDateTimeArch.= substr($strDateArch,13,2);
			$intTimeStampArch = $this->dateToTimestamp($strDateTimeArch);
			if($intTimeStampArch >= $intTimeStampDateIni){
				$arrFilesToParse[]=$strPathToArch;
			}
		}	
		foreach($arrFilesToParse as $strPathToArch ){
			$objFileArch = gzopen($strPathToArch,'r');
			$strArchContent = '';
			while(!feof($objFileArch)){
			  $strArchContent .= gzread($objFileArch,10000);			 			  
			}
			gzclose($objFileArch);
			$arrArchLineContent = array();
			$arrArchLineContent = explode("\n",$strArchContent);
			foreach($arrArchLineContent as $strLineArch){
				$arrArchColContent = array();
				$arrArchColContent = explode("\t",$strLineArch);
				if( sizeof($arrArchColContent) > 1 ){
					$intTimeStampLine = $this->dateToTimestamp(
						$arrArchColContent[0] . ' ' . $arrArchColContent[1], 'MYSQL'
					);
					if($intTimeStampLine>=$intTimeStampDateIni && $intTimeStampLine<=$intTimeStampDateEnd){
						$booLineCheck = true;
						if($strContentToSearch!=''){
							$strLineContentToCheck = str_replace(
								$arrArchColContent[0] . "\t"
								. $arrArchColContent[1] . "\t" 
								. $arrArchColContent[2] . "\t",
								'',
								$strLineArch
							) . "\n";
							if(!strstr($strLineContentToCheck,$strContentToSearch)){
								$booLineCheck = false;
							}
						}
						if($strIp!='' && !strstr($arrArchColContent[2],$strIp) ){
							$booLineCheck = false;
						}
						if($booLineCheck === true){
							if($booDetail === true){
								$strContentRequired.= $strLineArch . "\n";
							}else{
								$strContentRequired.= str_replace(
									$arrArchColContent[0] . "\t"
									. $arrArchColContent[1] . "\t" 
									. $arrArchColContent[2] . "\t",
									'',
									$strLineArch
								) . "\n";
							}
						}
					} elseif($intTimeStampLine > $intTimeStampDateEnd){
						return $strContentRequired;
					}
				}
			}
		}
		return $strContentRequired;
	}
	
	//从文件返回日志
	public function returnLogFileContent($strDateTimeIni,$strDateTimeEnd,$strContentToSearch,$booDetail,$strIp){	
		$intTimeStampDateIni = $this->dateToTimestamp($strDateTimeIni);
		$intTimeStampDateEnd = $this->dateToTimestamp($strDateTimeEnd);
		$strContentRequired = '';
		$arrLineContent = array();
		$this->objFile = $this->fopenLogFile($this->strToFile, 'r');		
		while(!feof($this->objFile)){			
			$strLineContent = fgets($this->objFile);
			$arrLineContent = explode("\t",$strLineContent);			
			if( sizeof($arrLineContent) > 1 ){
				$strDateLine = $arrLineContent[0];
				$strTimeLine = $arrLineContent[1];
				$strDateTimeLine = $strDateLine . ' ' . $strTimeLine;
				$intTimeStampLine = $this->dateToTimestamp($strDateTimeLine);
				if($intTimeStampLine >= $intTimeStampDateIni && $intTimeStampLine <= $intTimeStampDateEnd){
					$booLineCheck = true;
					if($strContentToSearch!=''){
						$strLineContentToCheck = str_replace(
							$arrLineContent[0] . "\t" 
							. $arrLineContent[1] . "\t" 
							. $arrLineContent[2] . "\t",
							'',
							$strLineContent
						);
						if(!strstr($strLineContentToCheck,$strContentToSearch)){
							$booLineCheck = false;
						}
					}
					if($strIp!='' && !strstr($arrLineContent[2],$strIp) ){
						$booLineCheck = false;
					}
					if($booLineCheck === true){
						if($booDetail === true){
							$strContentRequired.= $strLineContent;
						}else{
							$strContentRequired.= str_replace(
								$arrLineContent[0] . "\t" 
								. $arrLineContent[1] . "\t" 
								. $arrLineContent[2] . "\t",
								'',
								$strLineContent
							);
						}
					}
				} elseif($intTimeStampLine > $intTimeStampDateEnd){
					$this->closeLogFile();		
					return $strContentRequired;
				}
			}
		}
		$this->closeLogFile();			
		return $strContentRequired;
	}
	
	//从数据库返回日志
	public function returnLogMysqlContent($strDateTimeIni,$strDateTimeEnd,$strContentToSearch,$booDetail,$strIp){	
		$strContentRequired = '';
		$strDateIni = substr($strDateTimeIni,0,10);
		$strTimeIni = substr($strDateTimeIni,11,8);
		$strDateEnd = substr($strDateTimeEnd,0,10);
		$strTimeEnd = substr($strDateTimeEnd,11,8);
		$reqLogContent = 'SELECT * FROM `' . mysql_real_escape_string($this->strLogTable) . '` ';
		$reqLogContent.= 'WHERE( ';
			$reqLogContent.= '`logDate` < \'' . $strDateEnd . '\' ';
			$reqLogContent.= 'OR( ';
				$reqLogContent.= '`logDate` = \'' . $strDateEnd . '\' ';
				$reqLogContent.= 'AND `logTime` <= \'' . $strTimeEnd . '\' ';
			$reqLogContent.= ') ';
		$reqLogContent.= ') ';
		$reqLogContent.= 'AND( ';
			$reqLogContent.= '`logDate` > \'' . $strDateIni . '\' ';
			$reqLogContent.= 'OR(';
				$reqLogContent.= '	`logDate` = \'' . $strDateIni . '\' ';
				$reqLogContent.= 'AND `logTime` >= \'' . $strTimeIni . '\'';
			$reqLogContent.= ') ';
		$reqLogContent.= ') ';
		if($strIp!=''){
			$reqLogContent.= 'AND `logIp` LIKE \'%' . mysql_real_escape_string($strIp) . '%\' ';
		}
		if($strContentToSearch!=''){
			$reqLogContent.= 'AND `logString` LIKE ';
			$reqLogContent.= '\'%' . mysql_real_escape_string($strContentToSearch) . '%\' ';
		}
		$reqLogContent .= " order by logid desc";
		$recLogContent = $this->db->query($reqLogContent);
		if( mysql_num_rows($recLogContent) ){
			while($ojbLogContent = mysql_fetch_object($recLogContent)){
				if($booDetail === true){
					$strContentRequired.= $ojbLogContent->logDate . "\t" 
					. $ojbLogContent->logTime . "\t" 
					. $ojbLogContent->logIp . "\t" 
					. $ojbLogContent->logString . "\n";
				}else{
					$strContentRequired.= $ojbLogContent->logString . "\n";
				}
			}
		}
		return $strContentRequired;
	}
	
	//执行输出
	public function _toString(){
		return '<pre>' . print_r($this,true) . '</pre>';
	}
	
}
?>