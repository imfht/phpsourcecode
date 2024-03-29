<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Show all errors:
error_reporting(E_ALL & ~E_STRICT);

// Path to the chat directory:
define('AJAX_CHAT_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');

// Include custom libraries and initialization code:
require('lib/custom.php');

// Include Class libraries:
require('lib/classes.php');

// Initialize the chat:
$ajaxChat = new CustomAJAXChat();
?>