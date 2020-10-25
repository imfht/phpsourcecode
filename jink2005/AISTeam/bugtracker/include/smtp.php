<?php
class smtp
{
	/* Public Variables */
	var $smtp_port;
	var $time_out;
	var $host_name;
	var $log_file;
	var $relay_host;
	var $debug;
	var $auth;
	var $user;
	var $pass;

	/* Private Variables */ 
	var $sock;

	/* Constractor */
	function smtp($relay_host = "", $smtp_port = 25, $auth = false, $user, $pass)
	{
		$this->debug = FALSE;
		$this->smtp_port = $smtp_port;
		$this->relay_host = $relay_host;
		$this->time_out = 30; //is used in fsockopen() 
		
		$this->auth = $auth;//auth
		$this->user = $user;
		$this->pass = $pass;

		/* Use localhost in the host_name would reject by gmail. */
		$this->host_name = $_SERVER['SERVER_NAME'];	//is used in HELO command 
		if ($this->host_name == "") {
			$this->host_name = $_SERVER['HOST']?$_SERVER['HOST']:"localhost.com";
		}
		$this->log_file = "";

		$this->sock = FALSE;
	}

	/* Main Function */
	function sendmail($to, $from, $subject = "", $body = "", $mailtype, $cc = "", $bcc = "", $additional_headers = "")
	{
		if (strstr($from, "<")) {
			preg_match("/(.*)<(.*)>/", $from, $matches);
			$name = trim($matches[1]);
			$email = trim($matches[2]);
			if ($email == "") {
				$email = $from;
			}
			if ($name == "") {
				$name = $from;
			}
		} else {
			$email = $from;
			$name = $from;
		}

		$body = str_replace("\r\n.", "\r\n..", $body);
		$header = "MIME-Version:1.0\r\n";

		if($mailtype == "HTML"){
			$header .= "Content-Type:text/html; charset=utf-8\r\n";
		}

		$header .= "To: ".$to."\r\n";

		if ($cc != "") {
			$header .= "Cc: ".$cc."\r\n";
		}

		$header .= "From: $name<".$email.">\r\n";
		$header .= "Subject: ".$subject."\r\n";
		$header .= $additional_headers;
		$header .= "Date: ".date("r")."\r\n";
		$header .= "X-Mailer:By Bug Tracker (PHP/".phpversion().")\r\n";

		list($msec, $sec) = explode(" ", microtime());

		$header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$email.">\r\n";

		$TO = explode(",", $to);

		if ($cc != "") {
			$TO = array_merge($TO, explode(",", $cc));
		}

		if ($bcc != "") {
			$TO = array_merge($TO, explode(",", $bcc));
		}

		if (!$this->smtp_sockopen()) {
			$this->log_write("Error: Failed to open socket.\n");
			return FALSE;
		}

		if (!$this->smtp_send($this->host_name, $email, $TO, $header, $body)) {
			$this->log_write("Error: Cannot send email to <".$rcpt_to.">\n");
			fclose($this->sock);
			return FALSE;
		}

		fclose($this->sock);

		$this->log_write("Disconnected from remote host\n");

		return TRUE;
	}

	/* Private Functions */
	function smtp_send($helo, $from, $to_array, $header, $body = "")
	{
		if ($this->auth) {
			if (!$this->smtp_putcmd("EHLO", $helo)) {
				return $this->smtp_error("sending HELO command");
			}
		} else {
			if (!$this->smtp_putcmd("HELO", $helo)) {
				return $this->smtp_error("sending HELO command");
			}
		}
		

		#auth
		if($this->auth){
			if (!$this->smtp_putcmd("AUTH LOGIN", "")) {
				return $this->smtp_error("sending AUTH LOGIN ");
			}
			if (!$this->smtp_putcmd("", base64_encode($this->user))) {
				return $this->smtp_error("sending AUTH user command");
			}

			if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
				return $this->smtp_error("sending ATUH  password command");
			}
		}

		if (!$this->smtp_putcmd("MAIL", "From: <".$from.">")) {
			return $this->smtp_error("sending MAIL FROM command");
		}

		foreach ($to_array as $rcpt_to) {
			$rcpt_to = $this->get_address($rcpt_to);
			if ($rcpt_to == "") {
				continue;
			}
			if (!$this->smtp_putcmd("RCPT", "To: <".$rcpt_to.">")) {
				return $this->smtp_error("sending RCPT TO command");
			}
		}

		if (!$this->smtp_putcmd("DATA")) {
			return $this->smtp_error("sending DATA command");
		}


		if (!$this->smtp_message($header, $body)) {
			return $this->smtp_error("sending message");
		}

		if (!$this->smtp_eom()) {
			return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
		}

		if (!$this->smtp_putcmd("QUIT")) {
			return $this->smtp_error("sending QUIT command");
		}
		return TRUE;
	}

	function smtp_sockopen()
	{
		$this->log_write("Trying to ".$this->relay_host.":".$this->smtp_port."\n");
		$this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
		if (!($this->sock && $this->smtp_ok())) {
			$this->log_write("Error: Cannot connenct to relay host ".$this->relay_host."\n");
			$this->log_write("Error: ".$errstr." (".$errno.")\n");
			return FALSE;
		}
		$this->log_write("Connected to relay host ".$this->relay_host."\n");
		return TRUE;
	}
/*
	function smtp_sockopen_mx($address)
	{
		$domain = ereg_replace("^.+@([^@]+)$", "\1", $address);
		if (!@getmxrr($domain, $MXHOSTS)) {
			$this->log_write("Error: Cannot resolve MX \"".$domain."\"\n");
			return FALSE;
		}

		foreach ($MXHOSTS as $host) {
			$this->log_write("Trying to ".$host.":".$this->smtp_port."\n");
			$this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
			if (!($this->sock && $this->smtp_ok())) {
				$this->log_write("Warning: Cannot connect to mx host ".$host."\n");
				$this->log_write("Error: ".$errstr." (".$errno.")\n");
				continue;
			}
			$this->log_write("Connected to mx host ".$host."\n");

			return TRUE;
		}

		$this->log_write("Error: Cannot connect to any mx hosts (".implode(", ", $MXHOSTS).")\n");

		return FALSE;
	}
*/
	function smtp_message($header, $body)
	{
		fputs($this->sock, $header."\r\n".$body);
		$this->smtp_debug("> ".str_replace("\r\n", "\n"."> ", $header."\n> ".$body."\n> "));

		return TRUE;
	}

	function smtp_eom()
	{
		fputs($this->sock, "\r\n.\r\n");
		$this->smtp_debug(". [EOM]\n");

		return $this->smtp_ok();
	}

	function smtp_ok()
	{
		$sock = array($this->sock);
		// create a copy, so $sock doesn't get modified by stream_select()
		$read_sock = $sock;
		$w = NULL;
		$e = NULL;
		
		while (0 < stream_select($read_sock, $w, $e, 20)) {
			$response = str_replace("\r\n", "", fgets($this->sock, 512));
			$this->smtp_debug($response."\n");

			// Skip the multiple 250-XXXX lines introduced by EHLO
			if (strncmp($response, "250-", 4) == 0) continue;

			if (!preg_match("/^[23]/", $response)) {
				fputs($this->sock, "QUIT\r\n");
				fgets($this->sock, 512);
				$this->log_write("Error: Remote host returned \"".$response."\"\n");
				return FALSE;
			}
			return TRUE;
		}
		// Never reached
		return FALSE;
	}

	function smtp_putcmd($cmd, $arg = "")
	{
		if ($arg != "") {
			if($cmd=="") $cmd = $arg;
			else $cmd = $cmd." ".$arg;
		}
		
		fputs($this->sock, $cmd."\r\n");

		$this->smtp_debug("> ".$cmd."\n");

		return $this->smtp_ok();
	}

	function smtp_error($string)
	{
		$this->log_write("Error: Error occurred while ".$string.".\n");

		return FALSE;
	}

	function log_write($message)
	{
		$this->smtp_debug($message);
		if ($this->log_file == "") {
			return TRUE;
		}

		$message = date("M d H:i:s ").get_current_user()."[".getmypid()."]: ".$message;

		if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) {
			$this->smtp_debug("Warning: Cannot open log file \"".$this->log_file."\"\n");

			return FALSE;;
		}
		flock($fp, LOCK_EX);

		fputs($fp, $message);

		fclose($fp);

		return TRUE;
	}

	function get_address($address)
	{
		if (strstr($address, "<")) {
			preg_match("/<(.*)>/", $address, $matches);
			$address = $matches[1];
		}

		return $address;
	}

	function smtp_debug($message)
	{
		if ($this->debug) {

			echo htmlspecialchars($message)."<br>";

		}
	}
}

?>
