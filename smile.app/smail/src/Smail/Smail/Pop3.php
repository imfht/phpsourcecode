<?php
namespace Smail;

use Smail\MailBase;
/**
 * pop3 util class 
 * 
 * @author fuyou
 *
 */
class Pop3 {
	/**
	 * Error string.
	 *
	 * @var string
	 */
	private  $ERROR      = '';
	/**
	 * Default timeout before giving up on a network operation.
	 *
	 * @var int
	 */
	private $TIMEOUT    = 60;
	/**
	 * Mailbox msg count
	 *
	 * @var unknown_type
	 */
	private $COUNT      = -1;
	/**
	 * Socket buffer for socket fgets() calls.
	 * Per RFC 1939 the returned line a POP3
	 * server can send is 512 bytes.
	 * @var int
	 */
	private $BUFFER     = 512;
	/**
	 * The connection to the server's file descriptor
	 *
	 * @var resource
	 */
	private $FP         = '';
	/**
	 *  Set this to hard code the server name
	 *
	 * @var string
	 */
	private $MAILSERVER = '';
	/**
	 * set to true to echo pop3,commands and responses to error_log,this WILL log passwords!
	 *
	 * @var boolean
	 */
	private $DEBUG      = FALSE;
	/**
	 * Holds the banner returned by the pop server - used for apop()
	 *
	 * @var string
	 */
	private $BANNER     = '';
	/**
	 * Allow or disallow apop() This must be set to true manually
	 *
	 * @var boolean
	 */
	private $ALLOWAPOP  = FALSE;

	public function __construct(){
	}
	/**
	 * refresh the timeout limited
	 *
	 */
	private function update_timer(){
		if (!ini_get('safe_mode')){
			set_time_limit($this->TIMEOUT);
		}
		return true;
	}
	/**
	 * Strips \r\n from server responses
	 *
	 * @param $text
	 */
	private function strip_clf ($text = "") {
		if(empty($text)){
			return $text;
		}else {
			$stripped = str_replace(array("\r","\n"),'',$text);
			return $stripped;
		}
	}
	/**
	 * Return true or false on +OK or -ERR
	 *
	 * @param string $cmd
	 */
	private function is_ok ($cmd = "") {
		if(empty($cmd)){
			return false;
		}else{
			return(stripos($cmd,'+OK')!==false);
		}
	}
	/**
	 * Get the banner returned by the pop server - used for apop()
	 *
	 * @param string $server_text
	 */
	private function parse_banner ($server_text) {
		$outside = true;
		$banner = "";
		$length = strlen($server_text);
		for($count =0; $count < $length; $count++){
			$digit = substr($server_text,$count,1);
			if(!empty($digit)){
				if(!$outside && $digit != '<' && $digit != '>'){
					$banner .= $digit;
				}
				if ($digit == '<'){
					$outside = false;
				}
				if($digit == '>'){
					$outside = true;
				}
			}
		}
		$banner = $this->strip_clf($banner);    // Just in case
		return "<$banner>";
	}
	/**
	 * Opens a socket to the specified server. Unless overridden,
	 * port defaults to 110. Returns true on success, false on fail
	 * If MAILSERVER is set, override $server with it's value
	 * @param string $server
	 * @param int $port
	 */
	public function connect($server,$port = 110){
		if(empty($server)){
			$this->ERROR = "POP3 connect: No server specified";
			unset($this->FP);
			return false;
		}
		$fp = @fsockopen("$server", $port, $errno, $errstr);
		if(!$fp) {
			$this->ERROR = "POP3 connect: Can not connect the pop3 server" ;
			unset($this->FP);
			return false;
		}
		socket_set_blocking($fp,-1);
		$this->update_timer();
		$reply = fgets($fp,$this->BUFFER);
		$reply = $this->strip_clf($reply);
		if($this->DEBUG){
			error_log("POP3 SEND [connect: $server] GOT [$reply]",0);
		}
		if(!$this->is_ok($reply)) {
			$this->ERROR = "POP3 connect: " ."Error " . "[$reply]";
			unset($this->FP);
			return false;
		}
		$this->FP = $fp;
		$this->BANNER = $this->parse_banner($reply);
		return true;
	}
	/**
	 * auth with the pop3 server
	 *
	 * @param string $user
	 * @param string $pass
	 */
	public function login($user,$pass){
		if(!isset($this->FP)) {
			$this->ERROR = "POP3 user:" ."connection not established";
			return false;
		}
		if(empty($user)) {
			$this->ERROR = "POP3 user:" ."no login ID submitted";
			return false;
		}else {
			$reply = $this->send_cmd("USER $user");
			if(!$this->is_ok($reply)) {
				$this->ERROR = "POP3 user: " . _("Error ") . "[$reply]";
				return false;
			}
		}
		if(empty($pass)){
			$this->ERROR = "POP3 pass: " . "No password submitted";
			return false;
		}else{
			$reply = $this->send_cmd("PASS $pass");
			if(!$this->is_ok($reply)) {
				$this->ERROR =  "Authentication failed" . "Please check your account infomation";
				$this->quit();
				return false;
			} else {
				$count = $this->stat();
				$this->COUNT = $count['count'];
				return $count;
			}
		}
	}
	/**
	 * Attempts an APOP login. If this fails, it'll
	 * try a standard login. YOUR SERVER MUST SUPPORT
	 * THE USE OF THE APOP COMMAND!
	 * (apop is optional per rfc1939)
	 *
	 *@param$
	 */
	function apop($user,$pass){
		if(!isset($this->FP)) {
			$this->ERROR = "POP3 apop: " . "No connection to server";
			return false;
		} elseif(!$this->ALLOWAPOP) {
			$retVal = $this->login($user,$pass);
			return $retVal;
		} elseif(empty($user)) {
			$this->ERROR = "POP3 apop: " . "No login ID submitted";
			return false;
		} elseif(empty($pass)) {
			$this->ERROR = "POP3 apop: " . "No password submitted";
			return false;
		} else {
			if(!$this->BANNER || empty($this->BANNER)){
				$this->ERROR = "POP3 apop: " . "No server banner".' - '."abort";
				$retVal = $this->login($user,$pass);
				return $retVal;
			} else {
				$AuthString = $this->BANNER;
				$AuthString .= $pass;
				$APOPString = md5($AuthString);
				$cmd = "APOP $user $APOPString";
				$reply = $this->send_cmd($cmd);
				if(!$this->is_ok($reply)) {
					$this->ERROR = "POP3 apop: " . "apop authentication failed" . ' - ' . "abort";
					$retVal = $this->login($user,$pass);
					return $retVal;
				} else {
					$count = $this->stat();
					$this->COUNT = $count['count'];
					return $count;
				}
			}
		}
	}
	/**
	 * Gets the header and first $numLines of the msg body
	 * returns data in an array with each returned line being
	 * an array element. If $numLines is empty, returns
	 * only the header information, and none of the body.
	 *
	 * @param int $msgNum
	 * @param int $numLines
	 */
	public function top($msgNum,$numLines = "0"){
		if(!isset($this->FP)) {
			$this->ERROR = "POP3 top: " ."No connection to server";
			return false;
		}
		$this->update_timer();
		$cmd = "TOP $msgNum $numLines";
		fwrite($this->FP, "TOP $msgNum $numLines\r\n");
		$reply = fgets($this->FP, $this->BUFFER);
		$reply = $this->strip_clf($reply);
		if($this->DEBUG){
			@error_log("POP3 SEND [$cmd] GOT [$reply]",0);
		}
		if(!$this->is_ok($reply)){
			$this->ERROR = "POP3 top: " . "Error " . "[$reply]";
			return false;
		}
		$count = 0;
		$MsgArray = array();
		$line = fgets($this->FP,$this->BUFFER);
		while (!preg_match('/^\.\r\n/',$line)){
			$MsgArray[$count] = $line;
			$count++;
			$line = fgets($this->FP,$this->BUFFER);
			if(empty($line)){
				break;
			}
		}

		return $MsgArray;
	}
	/**
	 *  Flags a specified msg as deleted. The msg will not
	 *  be deleted until a quit() method is called.
	 */
	public function delete ($msgNum = "") {
		if(!isset($this->FP)){
			$this->ERROR = "POP3 delete: " ."No connection to server";
			return false;
		}
		if(empty($msgNum)){
			$this->ERROR = "POP3 delete: " . "No msg number submitted";
			return false;
		}
		$reply = $this->send_cmd("DELE $msgNum");
		if(!$this->is_ok($reply)){
			$this->ERROR = "POP3 delete: " . "Command failed " . "[$reply]";
			return false;
		}
		return true;
	}
	/**
	 * Returns the UIDL of the msg specified. If called with
	 * no arguments, returns an associative array where each
	 * undeleted msg num is a key, and the msg's uidl is the element
	 * Array element 0 will contain the total number of msgs
	 *
	 * @param int $msgNum
	 */
	function uidl($msgNum = ""){
		if(!isset($this->FP)) {
			$this->ERROR = "POP3 uidl: " . "No connection to server";
			return false;
		}
		if(!empty($msgNum)){
			$cmd = "UIDL $msgNum";
			$reply = $this->send_cmd($cmd);
			if(!$this->is_ok($reply)){
				$this->ERROR = "POP3 uidl: " . "Error " . "[$reply]";
				return false;
			}
			list ($ok,$num,$myUidl) = preg_split('/\s+/',$reply);
			return $myUidl;
		}else{
			$this->update_timer();
			$UIDLArray = array();
			$UIDLArray[0] = $this->COUNT;
			if ($this->COUNT < 1){
				return $UIDLArray;
			}
			$cmd = "UIDL";
			fwrite($this->FP, "UIDL\r\n");
			$reply = fgets($this->FP, $this->BUFFER);
			$reply = $this->strip_clf($reply);
			if($this->DEBUG) {
				@error_log("POP3 SEND [$cmd] GOT [$reply]",0);
			}
			if(!$this->is_ok($reply)){
				$this->ERROR = "POP3 uidl: " . "Error " . "[$reply]";
				return false;
			}
			$line = "";
			$count = 1;
			$line = fgets($this->FP,$this->BUFFER);
			while (!preg_match('/^\.\r\n/',$line)){
				list ($msg,$msgUidl) = preg_split('/\s+/',$line);
				$msgUidl = $this->strip_clf($msgUidl);
				if($count == $msg) {
					$UIDLArray[$msg] = $msgUidl;
				}else{
					$UIDLArray[$count] = 'deleted';
				}
				$count++;
				$line = fgets($this->FP,$this->BUFFER);
			}
		}
		return $UIDLArray;
	}
	/**
	 *  Retrieve the specified msg number. Returns an array
	 *  where each line of the msg is an array element.
	 *  @param int $msgNum
	 */
	function get ($msgNum) {
		if(!isset($this->FP)){
			$this->ERROR = "POP3 Get: " . _("No connection to server");
			return false;
		}
		$this->update_timer();
		$cmd = "RETR $msgNum";
		$reply = $this->send_cmd($cmd);
		if(!$this->is_ok($reply)){
			$this->ERROR = "POP3 Get:" . "Error " . "[$reply]";
			return false;
		}
		$count = 0;
		$MsgArray = array();
		$line = fgets($this->FP,$this->BUFFER);
		while (!preg_match('/^\.\r\n/',$line)){
			if($line{0} == '.'){
				$line = substr($line,1);
			}
			$MsgArray[$count] = $line;
			$count++;
			$line = fgets($this->FP,$this->BUFFER);
			if(empty($line)){
				break;
			}
		}
		return $MsgArray;
	}
	/**
	 *  Resets the status of the remote server. This includes
	 *  resetting the status of ALL msgs to not be deleted.
	 *  This method automatically closes the connection to the server.
	 *
	 */
	function reset () {
		if(!isset($this->FP)){
			$this->ERROR = "POP3 reset: " . "No connection to server";
			return false;
		}
		$reply = $this->send_cmd("RSET");
		if(!$this->is_ok($reply)){
			//  The POP3 RSET command -never- gives a -ERR
			//  response - if it ever does, something truely
			//  wild is going on.
			$this->ERROR = "POP3 reset: " . "Error " . "[$reply]";
			if($this->DEBUG){
				@error_log("POP3 reset: ERROR [$reply]",0);
			}
		}
		$this->quit();
		return true;
	}
	/**
	 * If called with an argument, returns that msgs' size in octets
	 * No argument returns an associative array of undeleted
	 * msg numbers and their sizes in octets
	 *
	 * @param int $msgNum
	 */
	function pop_list($msgNum = ""){
		if(!isset($this->FP)){
			$this->ERROR = "POP3 pop_list: " . "No connection to server";
			return false;
		}
		if(!$this->COUNT || $this->COUNT == -1){
			return false;
		}
		if($this->COUNT == 0){
			return array('count'=>0,'size'=>0);
		}
		$this->update_timer();
		if(!empty($msgNum)){
			$cmd = "LIST $msgNum";
			fwrite($this->FP,"$cmd\r\n");
			$reply = fgets($this->FP,$this->BUFFER);
			$reply = $this->strip_clf($reply);
			if($this->DEBUG){
				@error_log("POP3 SEND [$cmd] GOT [$reply]",0);
			}
			if(!$this->is_ok($reply)){
				$this->ERROR = "POP3 pop_list: " . "Error " . "[$reply]";
				return false;
			}
			list($junk,$num,$size) = preg_split('/\s+/',$reply);
			return $size;
		}
		$cmd = "LIST";
		$reply = $this->send_cmd($cmd);
		if(!$this->is_ok($reply)){
			$reply = $this->strip_clf($reply);
			$this->ERROR = "POP3 pop_list: " . "Error " .  "[$reply]";
			return false;
		}
		$MsgArray = array();
		$MsgArray[0] = $this->COUNT;
		for($msgC=1;$msgC <= $this->COUNT; $msgC++){
			if($msgC > $this->COUNT) {
				break;
			}
			$line = fgets($this->FP,$this->BUFFER);
			$line = $this->strip_clf($line);
			if(strpos($line, '.') === 0){
				$this->ERROR = "POP3 pop_list: " . _("Premature end of list");
				return false;
			}
			list($thisMsg,$msgSize) = preg_split('/\s+/',$line);
			settype($thisMsg,"integer");
			if($thisMsg != $msgC){
				$MsgArray[$msgC] = "deleted";
			}else{
				$MsgArray[$msgC] = $msgSize;
			}
		}
		return $MsgArray;
	}
	/**
	 * Returns the highest msg number in the mailbox.
	 * returns -1 on error, 0+ on success, if type != count
	 * results in a popstat() call (2 element array returned)
	 *
	 */
	public function stat(){
		$last = -1;
		if(!isset($this->FP)){
			$this->ERROR = "POP3 last: " ."No connection to server";
			return $last;
		}
		$reply = $this->send_cmd("STAT");
		if(!$this->is_ok($reply)){
			$this->ERROR = "POP3 last: " . "Error " . "[$reply]";
			return $last;
		}
		$Vars = preg_split('/\s+/',$reply);
		$count = $Vars[1];
		$size = $Vars[2];
		return array('acount'=>$count,'size'=>$size);
	}
	/**
	 * Closes the connection to the POP3 server, deleting
	 * any msgs marked as deleted.
	 *
	 */
	public function quit() {
		if(!isset($this->FP)){
			$this->ERROR = "POP3 quit: " . "connection does not exist";
			return false;
		}
		$cmd = "QUIT";
		fwrite($this->FP,"$cmd\r\n");
		$reply = fgets($this->FP,$this->BUFFER);
		$reply = $this->strip_clf($reply);
		if($this->DEBUG) {
			@error_log("POP3 SEND [$cmd] GOT [$reply]",0);
		}
		fclose($this->FP);
		unset($this->FP);
		return true;
	}
	/**
	 * Sends a user defined command string to the
	 * POP server and returns the results. Useful for
	 * non-compliant or custom POP servers.
	 * Do NOT includ the \r\n as part of your command
	 * string - it will be appended automatically.
	 * The return value is a standard fgets() call, which
	 * will read up to $this->BUFFER bytes of data, until it
	 * encounters a new line, or EOF, whichever happens first.
	 * This method works best if $cmd responds with only
	 * one line of data.
	 *
	 * @param unknown_type $cmd
	 */
	public function send_cmd($cmd = ""){
		if(!isset($this->FP)){
			$this->ERROR = "POP3 connect" . "No connection to server";
			return false;
		}
		if(empty($cmd)){
			$this->ERROR = "POP3 send_cmd: " ."Empty command string";
			return false;
		}
		$this->update_timer();
		fwrite($this->FP,"$cmd\r\n");
		$reply = fgets($this->FP,$this->BUFFER);
		$reply = $this->strip_clf($reply);
		if($this->DEBUG) {
			@error_log("POP3 SEND [$cmd] GOT [$reply]",0);
		}
		return $reply;
	}
	/**
	 * get the error
	 *
	 */
	public function get_error(){
		return $this->ERROR;
	}
}