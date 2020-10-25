<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: auth_imap.php,v 1.2 2010/07/27 09:24:17 alex Exp $
 *
 */

/* quoteIMAP($str)
 *
 * quote char's into valid IMAP string
 *
 * $str - String to be quoted
 *
 * Returns:
 *   quoted string
 */
function quoteIMAP($str)
{
	return preg_replace ('/(["\\\])/', '\\\\${1}', $str);
}

/* authValidateUser($user, $pass)
 * 
 * Checks if the specified username/password pair are valid
 * 
 * $user  - The user name
 * $pass  - The password
 * 
 * Returns:
 *   0        - The pair are invalid or do not exist
 *   non-zero - The pair are valid
 */
function authValidateUser($server, $port, $user, $pass)
{	
	if (!isset($server) || !isset($port)) {
		return 0;
	}
	// Check if we do not have a username/password
	if(!isset($user) || !isset($pass) || strlen($pass)==0) {
		return 0;
	}
	
	$error_number = "";
	$error_string = "";
	
	// Connect to IMAP-server
	$stream = fsockopen( $server, $port, $error_number, $error_string, 15 );
	$response = fgets( $stream, 1024 );
	if( $stream ) {
		$logon_str = "a001 LOGIN \"" . quoteIMAP( $user ) . "\" \"" . quoteIMAP( $pass ) . "\"\r\n";
		fputs( $stream, $logon_str );
		$response = fgets( $stream, 1024 );
		if( substr( $response, 5, 2 ) == 'OK' ) {
			fputs( $stream, "a001 LOGOUT\r\n" );
			$response = fgets( $stream, 1024 );
			fputs( $stream, "a001 LOGOUT\r\n" );
			return 1;
		}
		fputs( $stream, "a001 LOGOUT\r\n" );
	}
	
	// return failure
	return 0;
}

?>
