<?php 
class docEncryption
{
	var $enstr = null;
	function docEncryption($str)
	{
		$this->enstr = $str;
	}
	function get_shal()
	{
		return sha1($this->enstr);
	}
	function get_md5()
	{
		return md5($this->enstr);
	}
	function get_jxqy3()
	{
		$tmpMS = $this->get_shal().$this->get_md5();
		$tmpNewStr = substr($tmpMS,0,9).'s'.substr($tmpMS,10,9).'h'.substr($tmpMS,20,9).'l'.substr($tmpMS,30,9).'s'.substr($tmpMS,40,9).'u'.substr($tmpMS,50,9).'n'.substr
($tmpMS,60,9).'y'.substr($tmpMS,70,2);
		$tmpNewStr = substr($tmpNewStr,-36).substr($tmpNewStr,0,36);
		$tmpNewStr = substr($tmpNewStr,0,70);
		$tmpNewStr = substr($tmpNewStr,0,14).'j'.substr($tmpNewStr,14,14).'x'.substr($tmpNewStr,28,14).'q'.substr($tmpNewStr,32,14).'y'.substr($tmpNewStr,56,14).'3';
		return $tmpNewStr;
	}
	function to_string()
	{
		$tmpstr = $this->get_jxqy3();
		$tmpstr = substr($tmpstr,-35).substr($tmpstr,0,40);
		return $tmpstr;
	}
}