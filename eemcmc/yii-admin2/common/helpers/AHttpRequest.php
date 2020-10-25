<?php

namespace common\helpers;

/**
 * 基于 curl 封装的 Http 请求类 
 */
class AHttpRequest
{	
	
	const DELETE = "DELETE";
	const GET = "GET";
	const POST = "POST";
	const PUT = "PUT";
	const PATCH = "PATCH";
	
	/**
	 * Send a GET request to a URL
	 * @param  string $url     URL to send the GET request to
	 * @param  array  $headers Additional headers to send
	 * @return \common\helpers\AHttpResponse
	 */
	public static function get($url, $headers = array())
	{
		return self::request(self::GET, $url, NULL, $headers);
	}

	/**
	 * Send POST request to a URL
	 * @param  string $url     URL to send the POST request to
	 * @param  array  $headers Additional headers to send
	 * @param  mixed  $body    POST body data
	 * @return \common\helpers\AHttpResponse
	 */
	public static function post($url, $headers = array(), $body = NULL)
	{
		return self::request(self::POST, $url, $body, $headers);
	}
	
	/**
	 * Send DELETE request to a URL
	 * @param  string $url     URL to send the DELETE request to
	 * @param  array  $headers Additional headers to send
	 * @return \common\helpers\AHttpResponse
	 */
	public static function delete($url, $headers = array())
	{
		return self::request(self::DELETE, $url, NULL, $headers);
	}

	/**
	 * Send PUT request to a URL
	 * @param  string $url     URL to send the PUT request to
	 * @param  array  $headers Additional headers to send
	 * @param  mixed  $body    PUT body data
	 * @return \common\helpers\AHttpResponse
	 */
	public static function put($url, $headers = array(), $body = NULL)
	{
		return self::request(self::PUT, $url, $body, $headers);
	}
	
	/**
	 * Send PATCH request to a URL
	 * @param  string $url     URL to send the PATCH request to
	 * @param  array  $headers Additional headers to send
	 * @param  mixed $body     PATCH body data
	 * @return \common\helpers\AHttpResponse
	 */
	public static function patch($url, $headers = array(), $body = NULL)
	{
		return self::request(self::PATCH, $url, $body, $headers);
	}

	/**
	 * Send a cURL request
	 * @param  string $httpMethod 		HTTP Method to use
	 * @param  string $url        		URL to send the request to
	 * @param  mixed  $body       		Request body
	 * @param  array  $headers    		Additional headers to send
	 * @throws Exception 				If a cURL error occurs
	 * @return \common\helpers\AHttpResponse 			AHttpResponse object
	 */
	private static function request($httpMethod, $url, $body = NULL, $headers = array())
	{
		$lowercaseHeaders = array();
		foreach ($headers as $key => $val) {
			$key = trim(strtolower($key));
			if ($key == "user-agent" || $key == "expect") continue;
			$lowercaseHeaders[] = $key . ": " . $val;
		}
		$lowercaseHeaders[] = "user-agent: ahttp/1.0";
		$lowercaseHeaders[] = "expect:";
				
		$ch = curl_init();
		if ($httpMethod != self::GET) {
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
			if (is_array($body)) $body = http_build_query($body);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $body);
		}
		
		curl_setopt ($ch, CURLOPT_URL , self::encodeUrl($url));
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $lowercaseHeaders);
		curl_setopt ($ch, CURLOPT_HEADER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($ch);
		$error = curl_error($ch);
		if ($error) {
			throw new \Exception($error);
		}
		
		// Split the full response in its headers and body
		$curl_info = curl_getinfo($ch);
		$header_size = $curl_info["header_size"];
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		$httpCode = $curl_info["http_code"];
		
		return new AHttpResponse($httpCode, $body, $header);
	}
	
	/**
	 * Ensure that a URL is encoded and safe to use with cURL
	 * @param  string $url URL to encode
	 * @return string      Encoded URL
	 */
	private static function encodeUrl($url)
	{
		// Parse URL into pieces
		$url_parsed = parse_url($url);

		// Build the basics bypassing notices
		$scheme = $url_parsed['scheme'] . '://';
		$host = $url_parsed['host'];
		$port = (isset($url_parsed['port']) ? $url_parsed['port'] : null );
		$path = (isset($url_parsed['path']) ? $url_parsed['path'] : null );
		$query = (isset($url_parsed['query']) ? $url_parsed['query'] : null );

		// Do we need to encode anything?
		if ($query != null) {
			// Break up the query into an array
			parse_str($url_parsed['query'], $query_parsed);
			// Encode and build query based on RFC 1738
			$query = '?'.http_build_query($query_parsed);
		}

		// Return the completed URL
		$result = $scheme . $host . $port . $path . $query;
		return $result;
	}
	
}

class AHttpResponse
{	
	private $code;
	private $raw_body;
	private $headers;

	/**
	 * AHttpResponse constructor
	 * @param int $code     	Response code of the cURL request
	 * @param string $raw_body  The raw body of the cURL response
	 * @param string $headers   Raw header string from cURL response
	 */
	function __construct($code, $raw_body, $headers)
	{
		$this->code = $code;
		$this->headers = $this->get_headers_from_curl_response($headers);
		$this->raw_body = $raw_body;
	}
	
	/**
	 * Return a property of the response if it exists
	 * Possibilities include:
	 * - code
	 * - raw_body
	 * - headers
	 * @param  string $property
	 * @return string
	 */
	public function __get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	/**
	 * Set the properties of this object
	 * @param string $property The property name
	 * @param mixed $value    The property value
	 */
	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}
		return $this;
	}
	
	/**
	 * Retrieve the cURL response headers from the
	 * header string and convert it into an array
	 * @param  string $headers header string from cURL response
	 * @return array           headers in array form
	 */
	private function get_headers_from_curl_response($headers)
	{
		$headers = explode("\r\n", $headers);
        array_shift($headers);
        
        foreach ($headers as $line) {
            if (strstr($line, ': ')) {
                list($key, $value) = explode(': ', $line);
                $result[$key] = $value;
            }
        }
        
        return $result;
	}
}

if (!function_exists('http_chunked_decode')) {
    /** 
     * dechunk an http 'transfer-encoding: chunked' message 
     * 
     * @param string $chunk the encoded message 
     * @return string the decoded message.  If $chunk wasn't encoded properly it will be returned unmodified. 
     */ 
    function http_chunked_decode($chunk) {
        $pos = 0; 
        $len = strlen($chunk); 
        $dechunk = null; 

        while(($pos < $len) 
            && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos))
        ) {
            if (!is_hex($chunkLenHex)) { 
                trigger_error('Value is not properly chunk encoded', E_USER_WARNING); 
                return $chunk; 
            } 

            $pos = $newlineAt + 1; 
            $chunkLen = hexdec(rtrim($chunkLenHex,"\r\n")); 
            $dechunk .= substr($chunk, $pos, $chunkLen); 
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1; 
        } 
        return $dechunk; 
    }

	/** 
	 * determine if a string can represent a number in hexadecimal 
	 * 
	 * @param string $hex 
	 * @return boolean true if the string is a hex, otherwise false 
	 */ 
	function is_hex($hex) {
	    // regex is for weenies 
	    $hex = strtolower(trim(ltrim($hex,"0"))); 
	    if (empty($hex)) { $hex = 0; }; 
	    $dec = hexdec($hex); 
	    return ($hex == dechex($dec)); 
	}
	
}
