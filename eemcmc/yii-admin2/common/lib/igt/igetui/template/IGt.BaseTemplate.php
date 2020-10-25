<?php

require_once(dirname(__FILE__) . '/' . '../utils/ApnsUtils.php');

class IGtBaseTemplate{
	var $appId;
	var $appkey;
	var $pushInfo;
	
	function get_transparent() {
		$transparent = new Transparent();
		$transparent->set_id('');
		$transparent->set_messageId('');
		$transparent->set_taskId('');
		$transparent->set_action('pushmessage');
		$transparent->set_pushInfo($this->get_pushInfo());
		$transparent->set_appId($this->appId);
		$transparent->set_appKey($this->appkey);

		$actionChainList = $this->getActionChain();
		
		foreach($actionChainList as $index=>$actionChain){
			$transparent->add_actionChain();
			$transparent->set_actionChain($index,$actionChain);
		}
		return $transparent->SerializeToString();
	}

	function  get_transmissionContent() {
		return null;
	}
	
	function  get_pushType() {
		return null;
	}

	function get_actionChain() {
		return null;
	}

	function get_pushInfo() {
		if ($this->pushInfo==null) {	
			$this->pushInfo = new PushInfo();
			$this->pushInfo->set_actionKey('');
			$this->pushInfo->set_badge('-1');
			$this->pushInfo->set_message('');
			$this->pushInfo->set_sound('');
		}

		return $this->pushInfo;
	}

	function set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage,$contentAvailable=0) {
		$this->pushInfo = new PushInfo();
		$this->pushInfo->set_actionLocKey($actionLocKey);
		$this->pushInfo->set_badge($badge);
		$this->pushInfo->set_contentAvailable($contentAvailable);
		$this->pushInfo->set_message($message);
		if ($sound!=null) {
			$this->pushInfo->set_sound($sound);
		}
		if ($payload!=null) {
			$this->pushInfo->set_payload($payload);
		}
		if ($locKey!=null) {
			$this->pushInfo->set_locKey($locKey);
		}
		if ($locArgs!=null) {
			$this->pushInfo->set_locArgs($locArgs);
		}
		if ($launchImage!=null) {
			$this->pushInfo->set_launchImage($launchImage);
		}
        $payloadLen = ApnsUtils::validatePayloadLength($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload,$contentAvailable);
		if ($payloadLen > 256) {
			$errorMsg = "PushInfo length over limit: " . (string)$payloadLen . ". Allowed: 256.";
			echo $errorMsg;
			throw new Exception($errorMsg);
		}
	}
	
	function  set_appId($appId) {
		$this->appId = $appId;
	}

	function  set_appkey($appkey) {
		$this->appkey = $appkey;
	}

	function abslength($str)
	{
		if(empty($str)){
			return 0;
		}
		if(function_exists('mb_strlen')){
			return mb_strlen($str,'utf-8');
		}
		else {
			preg_match_all("/./u", $str, $ar);
			return count($ar[0]);
		}
	}
	
	
}
