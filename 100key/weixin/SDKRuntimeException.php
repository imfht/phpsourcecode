<?php
namespace Weixin\Controller;

class  SDKRuntimeException extends Exception {
    public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>