<?php

class OAuth2_Exception extends Exception {

	public function __construct($message) {
		parent::__construct($message, 0);
	}
	
	public function __toString() {
		return $this->message;
	}
}