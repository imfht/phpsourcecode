<?php

namespace App\Http\Utils\OAuth1;

/**
 *
 * @ignore
 *
 */
class OAuthUtil {
	public static $boundary = '';
	public static function urlencode_rfc3986($input) {
		if (is_array ( $input )) {
			return array_map ( array (
					'App\Http\Utils\OAuth1\OAuthUtil',
					'urlencode_rfc3986' 
			), $input );
		} else if (is_scalar ( $input )) {
			return str_replace ( '+', ' ', str_replace ( '%7E', '~', rawurlencode ( $input ) ) );
		} else {
			return '';
		}
	}
	
	// This decode function isn't taking into consideration the above
	// modifications to the encoding process. However, this method doesn't
	// seem to be used anywhere so leaving it as is.
	public static function urldecode_rfc3986($string) {
		return urldecode ( $string );
	}
	
	// Utility function for turning the Authorization: header into
	// parameters, has to do some unescaping
	// Can filter out any non-oauth parameters if needed (default behaviour)
	public static function split_header($header, $only_allow_oauth_parameters = true) {
		$pattern = '/(([-_a-z]*)=("([^"]*)"|([^,]*)),?)/';
		$offset = 0;
		$params = array ();
		while ( preg_match ( $pattern, $header, $matches, PREG_OFFSET_CAPTURE, $offset ) > 0 ) {
			$match = $matches [0];
			$header_name = $matches [2] [0];
			$header_content = (isset ( $matches [5] )) ? $matches [5] [0] : $matches [4] [0];
			if (preg_match ( '/^oauth_/', $header_name ) || ! $only_allow_oauth_parameters) {
				$params [$header_name] = OAuthUtil::urldecode_rfc3986 ( $header_content );
			}
			$offset = $match [1] + strlen ( $match [0] );
		}
		
		if (isset ( $params ['realm'] )) {
			unset ( $params ['realm'] );
		}
		
		return $params;
	}
	
	// helper to try to sort out headers for people who aren't running apache
	public static function get_headers() {
		if (function_exists ( 'apache_request_headers' )) {
			// we need this to get the actual Authorization: header
			// because apache tends to tell us it doesn't exist
			return apache_request_headers ();
		}
		// otherwise we don't have apache and are just going to have to hope
		// that $_SERVER actually contains what we need
		$out = array ();
		foreach ( $_SERVER as $key => $value ) {
			if (substr ( $key, 0, 5 ) == "HTTP_") {
				// this is chaos, basically it is just there to capitalize the first
				// letter of every word that is not an initial HTTP and strip HTTP
				// code from przemek
				$key = str_replace ( " ", "-", ucwords ( strtolower ( str_replace ( "_", " ", substr ( $key, 5 ) ) ) ) );
				$out [$key] = $value;
			}
		}
		return $out;
	}
	
	// This function takes a input like a=b&a=c&d=e and returns the parsed
	// parameters like this
	// array('a' => array('b','c'), 'd' => 'e')
	public static function parse_parameters($input) {
		if (! isset ( $input ) || ! $input)
			return array ();
		
		$pairs = explode ( '&', $input );
		
		$parsed_parameters = array ();
		foreach ( $pairs as $pair ) {
			$split = explode ( '=', $pair, 2 );
			$parameter = OAuthUtil::urldecode_rfc3986 ( $split [0] );
			$value = isset ( $split [1] ) ? OAuthUtil::urldecode_rfc3986 ( $split [1] ) : '';
			
			if (isset ( $parsed_parameters [$parameter] )) {
				// We have already recieved parameter(s) with this name, so add to the list
				// of parameters with this name
				
				if (is_scalar ( $parsed_parameters [$parameter] )) {
					// This is the first duplicate, so transform scalar (string) into an array
					// so we can add the duplicates
					$parsed_parameters [$parameter] = array (
							$parsed_parameters [$parameter] 
					);
				}
				
				$parsed_parameters [$parameter] [] = $value;
			} else {
				$parsed_parameters [$parameter] = $value;
			}
		}
		return $parsed_parameters;
	}
	public static function build_http_query_multi($params) {
		if (! $params)
			return '';
			
			// print_r( $params );
			// return null;
			
		// Urlencode both keys and values
		$keys = array_keys ( $params );
		$values = array_values ( $params );
		// $keys = OAuthUtil::urlencode_rfc3986(array_keys($params));
		// $values = OAuthUtil::urlencode_rfc3986(array_values($params));
		$params = array_combine ( $keys, $values );
		
		// Parameters are sorted by name, using lexicographical byte value ordering.
		// Ref: Spec: 9.1.1 (1)
		uksort ( $params, 'strcmp' );
		
		$pairs = array ();
		
		self::$boundary = $boundary = uniqid ( '------------------' );
		$MPboundary = '--' . $boundary;
		$endMPboundary = $MPboundary . '--';
		$multipartbody = '';
		
		foreach ( $params as $parameter => $value ) {
			
			// if( $parameter == 'pic' && $value{0} == '@' )
			if (in_array ( $parameter, array (
					"pic",
					"image" 
			) ) && $value {0} == '@') {
				$url = ltrim ( $value, '@' );
				$content = file_get_contents ( $url );
				$filename = reset ( explode ( '?', basename ( $url ) ) );
				$mime = self::get_image_mime ( $url );
				
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"' . "\r\n";
				$multipartbody .= 'Content-Type: ' . $mime . "\r\n\r\n";
				$multipartbody .= $content . "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value . "\r\n";
			}
		}
		
		$multipartbody .= $endMPboundary;
		// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		// Each name-value pair is separated by an '&' character (ASCII code 38)
		// echo $multipartbody;
		return $multipartbody;
	}
	public static function build_http_query($params) {
		if (! $params)
			return '';
			
			// Urlencode both keys and values
		$keys = OAuthUtil::urlencode_rfc3986 ( array_keys ( $params ) );
		$values = OAuthUtil::urlencode_rfc3986 ( array_values ( $params ) );
		$params = array_combine ( $keys, $values );
		
		// Parameters are sorted by name, using lexicographical byte value ordering.
		// Ref: Spec: 9.1.1 (1)
		uksort ( $params, 'strcmp' );
		
		$pairs = array ();
		foreach ( $params as $parameter => $value ) {
			if (is_array ( $value )) {
				// If two or more parameters share the same name, they are sorted by their value
				// Ref: Spec: 9.1.1 (1)
				natsort ( $value );
				foreach ( $value as $duplicate_value ) {
					$pairs [] = $parameter . '=' . $duplicate_value;
				}
			} else {
				$pairs [] = $parameter . '=' . $value;
			}
		}
		// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		// Each name-value pair is separated by an '&' character (ASCII code 38)
		return implode ( '&', $pairs );
	}
	public static function get_image_mime($file) {
		$ext = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );
		switch ($ext) {
			case 'jpg' :
			case 'jpeg' :
				$mime = 'image/jpg';
				break;
			
			case 'png' :
				$mime = 'image/png';
				break;
			
			case 'gif' :
			default :
				$mime = 'image/gif';
				break;
		}
		return $mime;
	}
}