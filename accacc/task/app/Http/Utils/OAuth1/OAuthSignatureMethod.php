<?php

namespace App\Http\Utils\OAuth1;

/**
 *
 * @ignore
 *
 */
class OAuthSignatureMethod {
	public function check_signature(&$request, $consumer, $token, $signature) {
		$built = $this->build_signature ( $request, $consumer, $token );
		return $built == $signature;
	}
}