<?php
class Upload
{
	private  $uploadDir;
	private  $errorMsg;
	private	 $allowSize;
	public 	 $AllowExt;
	private  $saveFileName;
	public   $i=0;
	/**
	 * 构造函数
	 *
	 * @param int $allowFileSize
	 * @param string $upDir
	 * @return Upload
	 */
	function Upload($allowFileSize=1000,$upDir='/upload/',$sType='COMMON')
	{
		$this->allowSize=$allowFileSize;
		$this->selectFileType($sType);
		$this->uploadDir=ABSPATH.$upDir;
	}
	/**
	 * 保存上传的文件，并且以当前时间为目录保存，文件名是当前时间+随机数。
	 *
	 * @param string $fileField
	 * @return string
	 */
	function SaveFile($fileField,$isArry=false,$i=0)
	{
		if($isArry)
		{
			//检查上传文件
			if($_FILES[$fileField]['error'][$i] > 0)
			{
				switch((int)$_FILES[$fileField]['error'][$i]){
					case UPLOAD_ERR_NO_FILE:
						$this->errorMsg .="请选择有效的上传文件！";
						break;
					case UPLOAD_ERR_FORM_SIZE:
						$this->errorMsg .="您上传的文件总大小超出了最大限制:".$this->allowSize."KB\"')";
						break;
				}
				return NULL;
			}
			preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES[$fileField]['name'][$i],$exts);
			//检查上传文件的扩展名
			if($this->checkValidExt($exts[1]))
			{
				$this->errorMsg.="提示：\n\n请选择一个有效的文件，\n支持的格式有:".$this->AllowExt;
				return NULL;
			}
			$this->saveFileName = $this->getRndFileName(strtolower($exts[1]));
			$sFileName = $this->getDateDir().$this->saveFileName;
	
			if(!move_uploaded_file($_FILES[$fileField]['tmp_name'][$i],$this->uploadDir.$sFileName))
			{
				$this->errorMsg.='文件上传系统操作错误。';
				return NULL;
			}
			else
			{
				return $sFileName;
			}
		}
		else
		{
			//检查上传文件
			if($_FILES[$fileField]['error'] > 0)
			{
				switch((int)$_FILES[$fileField]['error']){
					case UPLOAD_ERR_NO_FILE:
						$this->errorMsg .="请选择有效的上传文件！";
						break;
					case UPLOAD_ERR_FORM_SIZE:
						$this->errorMsg .="您上传的文件总大小超出了最大限制:".$this->allowSize."KB\"')";
						break;
				}
				return NULL;
			}
			preg_match("/\.([a-zA-Z0-9]{2,4})$/",$_FILES[$fileField]['name'],$exts);
			//检查上传文件的扩展名
			if($this->checkValidExt($exts[1]))
			{
				$this->errorMsg.="提示：\n\n请选择一个有效的文件，\n支持的格式有:".$this->AllowExt;
				return NULL;
			}
			$this->saveFileName = $this->getRndFileName(strtolower($exts[1]));
			$sFileName = $this->getDateDir().$this->saveFileName;
	
			if(!move_uploaded_file($_FILES[$fileField]['tmp_name'],$this->uploadDir.$sFileName))
			{
				$this->errorMsg.='文件上传系统操作错误。';
				return NULL;
			}
			else
			{
				return $sFileName;
			}
		}
	}
	
	function showError()
	{
		return $this->errorMsg;
	}
	private function mkdir_p($target)
	{
		if (is_dir($target)||empty($target)) return true;
		if (file_exists($target) && !is_dir($target)) return false;
		if ($this->mkdir_p(substr($target,0,strrpos($target,'/')))){
		if(mkdir($target)) return false;
		}				
	}
	private function getRndFileName($sExt){
		return date("YmdHis").rand(1,999).".".$sExt;
	}
	/**
 	 * 返回以当前时间为文件夹名的字符串(例：200601)，如果没有这个文件夹就自动建立。
	 *
	 * @param int $dataStyle 日期样式：1=年，2=年月，3=年月日
	 * @return string
	 */
	private function getDateDir($dataStyle=2){
		switch ($dataStyle)
		{
			Case 1:
				$sCreateDir = date("Y");
				break;
				Case 2:
					$sCreateDir = date("Ym");
					break;
					Case 3:
						$sCreateDir = date("Ymd");
						break;
					default:
						return null;
		}
		$sCreateDir = $sCreateDir."/";
		$this->mkdir_p($this->uploadDir.$sCreateDir);
		return $sCreateDir;
	}
	//检测文件名是否正确
	private function checkValidExt($sExt)
	{
		$aExt = explode('|',$this->AllowExt);
		if(in_array(strtolower($sExt),$aExt))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * 选择一组媒体文件类型可选项有： 'REMOTE' 'FILE' 'MEDIA' 'FLASH' 'COMMON' 'PIC'
	 *
	 * @param string $sType
	 */
	function selectFileType($sType='COMMON')
	{
		switch (strtoupper($sType)){
			case "REMOTE":
				$this->AllowExt = 'gif|jpg|jpeg|bmp|png';
				break;
			case "FILE":
				//$this->AllowExt = 'rar|zip|exe|doc|xls|chm|hlp';
				$this->AllowExt = 'rar|zip|doc|xls|chm|hlp';
				break;
			case "MEDIA":
				$this->AllowExt = 'rm|mp3|wav|mid|midi|ra|avi|mpg|mpeg|asf|asx|wma|mov';
				break;
			case "FLASH":
				$this->AllowExt = 'swf|flv|fla';
				break;
			case "PIC":
				$this->AllowExt = 'gif|jpg|jpeg|bmp|png';
				break;
			default:
				$this->AllowExt = 'gif|jpg|jpeg|bmp|png|rar|zip|swf|wma|rm|mp3';
				break;
		}
	}
}