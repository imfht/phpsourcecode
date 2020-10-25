<?php
/**
 * Copyright 2007 PayPal, Inc. All Rights Reserved.
 */

require_once "PPCrypto.php";

/**
 * API for doing PayPal encryption services.
 */
class EWPServices
{
	/**
	 * Creates a new encrypted button HTML block
	 *
	 * @param	array	The button parameters as key/value pairs
	 * @param	string	The file path to the EWP(merchant) certificate
	 * @param	string	The file path to the EWP(merchant) private key
	 * @param	string	The EWP(merchant) private key password
	 * @param	string	The file path to the PayPal Certificate
	 * @param	string	The URL where button will be posted
	 * @param	string	The URL of the button image
	 * @return	array	Contains a bool status, error_msg, error_no, and an encrypted string: encryptedButton if successfull
	 *
	 * @access public
	 * @static
	 */
	function encryptButton(	$buttonParams_,
							$ewpCertPath_,
							$ewpPrivateKeyPath_,
							$ewpPrivateKeyPwd_,
							$paypalCertPath_,
							$destinationUrl_)
    {
		/**
		 * serialize the button parameters' array to a string.
		 */
		$contentBytes = array();
		foreach ($buttonParams_ as $name => $value) {
			$contentBytes[] = "$name=$value";
		}
        $contentBytes = implode("\n", $contentBytes);

		/**
         * sign and encrypt the button parameters
         */
		$encryptedDataReturn = PPCrypto::signAndEncrypt($contentBytes, $ewpCertPath_, $ewpPrivateKeyPath_, $ewpPrivateKeyPwd_, $paypalCertPath_);
		if(!$encryptedDataReturn["status"]) {
			return array("status" => false, "error_msg" => $encryptedDataReturn["error_msg"], "error_no" => $encryptedDataReturn["error_no"]);
		}

		/**
		 * Build and return HTML string
		 */
		$encryptedData = "-----BEGIN PKCS7-----".$encryptedDataReturn["encryptedData"]."-----END PKCS7-----";
		$encryptedButton = <<<PPHTML
<FORM ACTION="${destinationUrl_}/cgi-bin/webscr" METHOD="post" id="paysubmit">
<INPUT TYPE="hidden" NAME="cmd" VALUE="_s-xclick">
<INPUT TYPE="hidden" NAME="encrypted" VALUE="$encryptedData">
</FORM>
PPHTML;
		return array("status" => true, "encryptedButton" => $encryptedButton);
	} // encryptButton
} // EWPServices
?>