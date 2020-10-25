<?php

namespace App\Http\Utils\OAuth1;

/**
 *
 * @ignore
 *
 */
class OAuthConsumer {
	public $key;
	public $secret;
	function __construct($key, $secret) {
		$this->key = $key;
		$this->secret = $secret;
	}
	function __toString() {
		return "OAuthConsumer[key=$this->key,secret=$this->secret]";
	}
}