<?php
require_once(dirname (__FILE__) . './PApiAutorun.php');
require_once(dirname(__FILE__) . '/../libs/simpletest/browser.php');
require_once(dirname(__FILE__) . '/../libs/simpletest/test_case.php');

class PApi{
	/**
	 * 
	 * @param unknown $label 显示名称
	 * @param unknown $divId 
	 * @param unknown $jsonStr
	 */
	public static function showJson($label, $jsonStr){
		if(is_array($jsonStr) == true){
			$jsonStr = json_encode($jsonStr);
		}
		// 过滤js json特殊符
		$jsonStr = str_replace(array("\\n","\\t","\\r", "\"", "'"), array("\\\u000a", "", "\\\u000d", "\\\"", "\\'"), $jsonStr);
		if (SimpleReporter::inCli()) {
			echo $label."\n";
			echo $jsonStr . "\n\n";
		}
		else{
			$divId = "id".uniqid();
			echo '<br/><div class="Canvas">'.$label.'</div>';
			echo '<div id="'.$divId.'" class="Canvas">$nbsp;</div>';
			echo '<script type="text/javascript">ProcessJson(\''.$divId.'\', \''.$jsonStr.'\')</script>';
		}
	}
}

?>