<?php
/**
	WARNING: Do not embed plaintext credentials in your application code.
	Doing so is insecure and against best practices.
	Your API credentials must be handled securely. Please consider
	encrypting them for use in any production environment, and ensure
	that only authorized individuals may view or modify them.
*/

/**
 * This class provides static utility functions that will be used by the WPS samples
 */
class Utils
{
	/**
	 * Builds the URL for the input file using the HTTP request information
	 *
	 * @param	string	The name of the new file
	 * @return	string	The full URL for the input file
	 *
	 * @access	public
	 * @static
	 */
	function getURL($fileContextPath_)
	{
		$server_protocol = htmlspecialchars($_SERVER["SERVER_PROTOCOL"]);
		$server_name = htmlspecialchars($_SERVER["SERVER_NAME"]);
		$server_port = htmlspecialchars($_SERVER["SERVER_PORT"]);
		$url = strtolower(substr($server_protocol,0, strpos($server_protocol, '/')));	// http
		$url .= "://$server_name:$server_port/$fileContextPath_";

		return $url;
	} // getURL

	/**
	 * Send HTTP POST Request
	 *
	 * @param	string	The request URL
	 * @param	string	The POST Message fields in &name=value pair format
	 * @param	bool		determines whether to return a parsed array (true) or a raw array (false)
	 * @return	array		Contains a bool status, error_msg, error_no,
	 *				and the HTTP Response body(parsed=httpParsedResponseAr  or non-parsed=httpResponse) if successful
	 *
	 * @access	public
	 * @static
	 */
	function PPHttpPost($url_, $postFields_, $parsed_)
	{
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url_);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);

		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields_);

		//getting response from server
		$httpResponse = curl_exec($ch);

		if(!$httpResponse) {
			return array("status" => false, "error_msg" => curl_error($ch), "error_no" => curl_errno($ch));
		}

		if(!$parsed_) {
			return array("status" => true, "httpResponse" => $httpResponse);
		}

		$httpResponseAr = explode("\n", $httpResponse);

		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if(0 == sizeof($httpParsedResponseAr)) {
			$error = "Invalid HTTP Response for POST request($postFields_) to $url_.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}
		return array("status" => true, "httpParsedResponseAr" => $httpParsedResponseAr);

	} // PPHttpPost

	/**
	 * Redirect to Error Page
	 *
	 * @param	string	Error message
	 * @param	int		Error number
	 *
	 * @access	public
	 * @static
	 */
	function PPError($error_msg, $error_no) {
		// create a new curl resource
		$ch = curl_init();

		// set URL and other appropriate options
		$php_self = substr(htmlspecialchars($_SERVER["PHP_SELF"]), 1); // remove the leading /
		$redirectURL = Utils::getURL(substr_replace($php_self, "Error.php", strrpos($php_self, '/') + 1));
		curl_setopt($ch, CURLOPT_URL, $redirectURL);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		// set POST fields
		$postFields = "error_msg=".urlencode($error_msg)."&error_no=".urlencode($error_no);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields);

		// grab URL, and print
		curl_exec($ch);
		curl_close($ch);
	}
} // Utils

?>