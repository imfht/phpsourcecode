<?php
class Common {
	function ajax_json_success($message,$callbackType="",$tabId="",$forwardUrl=""){
		//$callbackType=!empty($callbackType)?"forward":"";
		switch($callbackType){
			case 1:
				$callbackTypeStr="forward";
				break;
			case 2:
				$callbackTypeStr="closeCurrent";
				break;
			default :
				$callbackTypeStr="";
				break;
		}
		$forwardUrl  =!empty($forwardUrl)?ACT.$forwardUrl:"";
		$menu=array(
				  "statusCode"=>"200", 
				  "message"=>$message, 
				  "navTabId"=>$tabId, 
				  "reloadFlag"=>$tabId, 
				  "rel"=>$tabId, 
				  "callbackType"=>$callbackTypeStr,
				  "forwardUrl"=> $forwardUrl
		 );
		 echo json_encode($menu);		
	}
	
	/*错误提示*/
	function ajax_json_error($message){
		$menu=array(
				  "statusCode"=>"300", 
				  "message"=>$message
		 );
		 echo json_encode($menu);		
	}
	
	/*超时提示*/
	function ajax_json_timeout($message){
		$menu=array(
				  "statusCode"=>"301", 
				  "message"=>$message
		 );
		 echo json_encode($menu);		
	}		
	/*提示*/
	function ajax_alert($message,$type,$callbackType=""){
		echo "<script>";
		echo "alertMsg.$type('$message');";
		echo "navTab.closeCurrentTab();";
		echo "</script>";
		exit;
	}
}
?>
