<?php
namespace Jykj\Donation\Weixin;

class  SDKRuntimeException extends \Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>