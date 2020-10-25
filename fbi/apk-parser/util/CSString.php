<?php
/**
 * Helper class, used in Cast.toCharSequence.
 * AndroidManifest.xml解析相关
 * ---------------------------------------------
 * @Author: 80056038
 * @Date: 2012-5-22
 * @package
 * @name :CSString.php
 * @version v1.0
 *
 */
namespace APKParser;
class CSString{

	private $m_string;
	
	public function CSString($string) {
		if ($string==null) {
			$string="";
		}
		$this->m_string=$string;
	}

	public function length() {
		return strlen($this->m_string);
	}

	public function charAt($index) {
		return $this->m_string[$index];
	}

	public function subSequence($start,$end) {
		return new CSString(substr($this->m_string, $start ,$end));
	}

	public function toString() {
		return $this->m_string;
	}
}

?>