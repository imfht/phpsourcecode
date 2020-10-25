<?php
namespace Smail;

use Smail\Util\ComAuth;
use Smail\Util\MailConfig;
use Smail\Util\ComDate;
use Smail\Util\ComFunc;
use Smail\Util\ComDection;
use Smail\Mime\ComMime;

class MailBase
{

    var $imap_server = '';

    var $use_tls = '';

    var $imap_auth_mech = '';

    var $imap_port = '';

    var $smtp_server = '';

    var $smtp_port = '';

    var $mail_domain = '';

    var $smtp_auth_mech = '';

    var $imap_server_type = '';

    var $username = '';

    var $password = '';

    var $error = '';

    public function __construct()
    {}

    /**
     * 登录邮件服务器
     *
     * @param string $username 用户名            
     * @param string $password 密码            
     * @return object $imap_stream;
     */
    public function login($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $connection_pros = MailConfig::getConnectionPro($username);
        $this->imap_server = $connection_pros[2];
        $this->imap_port = $connection_pros[3];
        $this->imap_auth_mech = $connection_pros[0];
        $this->use_tls = $connection_pros[1];
        $this->mail_domain = $connection_pros[6];
        $this->imap_server_type = $connection_pros[8];
        if ($this->use_tls == true && extension_loaded('openssl')) {
            $this->imap_server = 'tls://' . $this->imap_server;
        }
        $imap_stream = @fsockopen($this->imap_server, $this->imap_port, $error_number, $error_string, 15);
        $server_info = fgets($imap_stream, 1024);
        if (($this->imap_auth_mech == 'cram-md5') or ($this->imap_auth_mech == 'digest-md5')) {
            $tag = $this->smimap_session_id(false);
            if ($this->imap_auth_mech == 'digest-md5') {
                $query = $tag . " AUTHENTICATE DIGEST-MD5\r\n";
            } elseif ($this->imap_auth_mech == 'cram-md5') {
                $query = $tag . " AUTHENTICATE CRAM-MD5\r\n";
            }
            fputs($imap_stream, $query);
            $answer = $this->smimap_fgets($imap_stream);
            // Trim the "+ " off the front
            $response = explode(" ", $answer, 3);
            if ($response[0] == '+') {
                // Got a challenge back
                $challenge = $response[1];
                if ($this->imap_auth_mech == 'digest-md5') {
                    $reply = ComAuth::digest_md5_response($username, $password, $challenge, 'imap', $imap_server);
                } elseif ($this->imap_auth_mech == 'cram-md5') {
                    $reply = ComAuth::cram_md5_response($username, $password, $challenge);
                }
                fputs($imap_stream, $reply);
                $read = $this->smimap_fgets($imap_stream);
                if ($this->imap_auth_mech == 'digest-md5') {
                    if (substr($read, 0, 1) == '+') {
                        fputs($imap_stream, "\r\n");
                        $read = $this->smimap_fgets($imap_stream);
                    }
                }
                $results = explode(" ", $read, 3);
                $response = $results[1];
                $message = $results[2];
            } else {
                $response = "BAD";
                $message = 'IMAP server does not appear to support the authentication method selected.';
                $message .= '  Please contact your system administrator.';
                // error_message($message);
            }
        } elseif ($this->imap_auth_mech == 'login') {
            if (stristr($server_info, 'LOGINDISABLED')) {
                $response = 'BAD';
                $message = "The IMAP server is reporting that plain text logins are disabled." . "Using CRAM-MD5 or DIGEST-MD5 authentication instead may work.";
                if (! $this->use_tls) {
                    $message .= "Also, the use of TLS may allow SquirrelMail to login.";
                }
                $message .= "Please contact your system administrator and report this error.";
            } else {
                if (ComMime::is8bit($username) || ComMime::is8bit($password)) {
                    $query['command'] = 'LOGIN';
                    $query['literal_args'][0] = $username;
                    $query['literal_args'][1] = $password;
                    $read = $this->smimap_run_literal_command($imap_stream, $query, false, $response, $message);
                } else {
                    $query = 'LOGIN "' . ComFunc::quoteimap($username) . '"' . ' "' . ComFunc::quoteimap($password) . '"';
                    $read = $this->smimap_run_command($imap_stream, $query, false, $response, $message);
                }
            }
        } elseif ($this->imap_auth_mech == 'plain') {
            $response = "BAD";
            $message = 'smail does not support SASL PLAIN yet. Rerun conf.pl and use login instead.';
            // error_message($message);
        } else {
            $response = "BAD";
            $message = "Internal smail error - unknown IMAP authentication method chosen.  Please contact the developers.";
            // error_message($message);
        }
        if ($response != 'OK') {
            if (! $hide) {
                if ($response != 'NO') {
                    /* "BAD" and anything else gets reported here. */
                    $message = htmlspecialchars($message);
                    if ($response == 'BAD') {
                        $string = sprintf("Bad request: %s<br/>", $message);
                    } else {
                        $string = sprintf("Unknown error: %s<br/>", $message);
                    }
                    if (isset($read) && is_array($read)) {
                        $string .= '<br />Read data:<br/>';
                        foreach ($read as $line) {
                            $string .= htmlspecialchars($line) . "<br/>";
                        }
                    }
                    // error_message($message);
                } else {
                    $this->smimap_logout($imap_stream);
                    // error_message("Unknown user or password incorrect.");
                }
            }
        }
        return $imap_stream;
    }

    /**
     * 注销登录
     *
     * @param mixed $imap_stream            
     */
    public function smimap_logout($imap_stream)
    {
        if (isset($imap_stream) && $imap_stream) {
            $this->smimap_run_command($imap_stream, 'LOGOUT', false, $response, $message);
        }
    }

    /**
     * 运行命令行
     *
     * @param mixed $imap_stream            
     * @param string $query            
     * @param string $handle_errors            
     * @param string $response            
     * @param string $message            
     * @param string $unique_id            
     * @return mixed $read
     */
    protected function smimap_run_command_list($imap_stream, $query, $handle_errors, &$response, &$message, $unique_id = false)
    {
        if ($imap_stream) {
            $sid = $this->smimap_session_id($unique_id);
            fputs($imap_stream, $sid . ' ' . $query . "\r\n");
            $read = $this->smimap_read_data_list($imap_stream, $sid, $handle_errors, $response, $message, $query);
            return $read;
        } else {
            return false;
        }
    }

    /**
     * get the section of the mail's id
     *
     * @param array $messages_array            
     * @return string 1:2:3
     */
    protected function smimap_message_list_squisher($messages_array)
    {
        if (! is_array($messages_array)) {
            return $messages_array;
        }
        sort($messages_array, SORT_NUMERIC);
        $msgs_str = '';
        while ($messages_array) {
            $start = array_shift($messages_array);
            $end = $start;
            while (isset($messages_array[0]) && $messages_array[0] == $end + 1) {
                $end = array_shift($messages_array);
            }
            if ($msgs_str != '') {
                $msgs_str .= ',';
            }
            $msgs_str .= $start;
            if ($start != $end) {
                $msgs_str .= ':' . $end;
            }
        }
        return $msgs_str;
    }

    /**
     * 获取ID组 1:1000,并放入session
     *
     * @param mixed $imap_stream            
     * @param array $mbxresponse            
     */
    private function smimap_get_php_sort_order($imap_stream, $mbxresponse)
    {
        $uid_support = true;
        
        if (isset($_SESSION['php_sort_array'])) {
            unset($_SESSION['php_sort_array']);
        }
        $php_sort_array = array();
        if ($uid_support) {
            if (isset($mbxresponse['UIDNEXT'])) {
                $uidnext = $mbxresponse['UIDNEXT'] - 1;
            } else {
                $uidnext = '*';
            }
            $query = "SEARCH UID 1:$uidnext";
            $uids = $this->smimap_run_command($imap_stream, $query, true, $response, $message, true);
            if (isset($uids[0])) {
                $php_sort_array = array();
                // EIMS workaround. EIMS returns the result as multiple untagged SEARCH responses
                foreach ($uids as $line) {
                    if (preg_match("/^\* SEARCH (.+)$/", $line, $regs)) {
                        $php_sort_array += preg_split("/ /", trim($regs[1]));
                    }
                }
            }
            if (! preg_match("/OK/", $response)) {
                $php_sort_array = 'no';
            }
        } else {
            $qty = $mbxresponse['EXISTS'];
            $php_sort_array = range(1, $qty);
        }
        $_SESSION['php_sort_array'] = $php_sort_array;
        return $php_sort_array;
    }

    /**
     * 创建会话ID
     *
     * @param
     *            $unique_id
     */
    public function smimap_session_id($unique_id = FALSE)
    {
        static $smimap_session_id = 1;
        if (! $unique_id) {
            return (sprintf("A%03d", $smimap_session_id ++));
        } else {
            return (sprintf("A%03d", $smimap_session_id ++) . ' UID');
        }
    }

    /**
     * 获取会话信息
     *
     * @param mixed $imap_stream            
     */
    private function smimap_fgets($imap_stream)
    {
        $read = '';
        $buffer = 4096;
        $results = '';
        $offset = 0;
        while (strpos($results, "\r\n", $offset) === false) {
            if (! ($read = fgets($imap_stream, $buffer))) {
                /* this happens in case of an error */
                /* reset $results because it's useless */
                $results = false;
                break;
            }
            if ($results != '') {
                $offset = strlen($results) - 1;
            }
            $results .= $read;
        }
        return $results;
    }

    /**
     * 运行命令
     *
     * @param
     *            $imap_stream
     * @param
     *            $query
     * @param
     *            $handle_errors
     * @param
     *            $response
     * @param
     *            $message
     * @param
     *            $unique_id
     */
    private function smimap_run_literal_command($imap_stream, $query, $handle_errors, &$response, &$message, $unique_id = false)
    {
        if ($imap_stream) {
            $sid = $this->smimap_session_id($unique_id);
            $command = sprintf("%s {%d}\r\n", $query['command'], strlen($query['literal_args'][0]));
            fputs($imap_stream, $sid . ' ' . $command);
            
            // TODO: Put in error handling here //
            $read = $this->smimap_read_data($imap_stream, $sid, $handle_errors, $response, $message, $query['command']);
            
            $i = 0;
            $cnt = count($query['literal_args']);
            while ($i < $cnt) {
                if (($cnt > 1) && ($i < ($cnt - 1))) {
                    $command = sprintf("%s {%d}\r\n", $query['literal_args'][$i], strlen($query['literal_args'][$i + 1]));
                } else {
                    $command = sprintf("%s\r\n", $query['literal_args'][$i]);
                }
                fputs($imap_stream, $command);
                $read = $this->smimap_read_data($imap_stream, $sid, $handle_errors, $response, $message, $query['command']);
                $i ++;
            }
            return $read;
        } else {
            $string = "会话流没有创建";
            // error_message($string);
            return false;
        }
    }

    /**
     * 读取会话返回数据
     *
     * @param
     *            $imap_stream
     * @param
     *            $tag_uid
     * @param
     *            $handle_errors
     * @param
     *            $response
     * @param
     *            $message
     * @param
     *            $query
     * @param
     *            $filter
     * @param
     *            $outputstream
     * @param
     *            $no_return
     */
    protected function smimap_read_data($imap_stream, $tag_uid, $handle_errors, &$response, &$message, $query = '')
    {
        $res = $this->smimap_read_data_list($imap_stream, $tag_uid, $handle_errors, $response, $message, $query);
        return $res[0];
    }

    /**
     * 运行命令
     *
     * @param
     *            $imap_stream
     * @param
     *            $query
     * @param
     *            $handle_errors
     * @param
     *            $response
     * @param
     *            $message
     * @param
     *            $unique_id
     * @param
     *            $filter
     * @param
     *            $outputstream
     * @param
     *            $no_return
     */
    public function smimap_run_command($imap_stream, $query, $handle_errors, &$response, &$message, $unique_id = false)
    {
        if ($imap_stream) {
            $sid = $this->smimap_session_id($unique_id);
            fputs($imap_stream, $sid . ' ' . $query . "\r\n");
            $read = $this->smimap_read_data($imap_stream, $sid, $handle_errors, $response, $message, $query);
            return $read;
        } else {
            $string = "会话数据流为空";
            return false;
        }
    }

    /**
     * 读取数据列表
     *
     * @param
     *            $imap_stream
     * @param
     *            $tag_uid
     * @param
     *            $handle_errors
     * @param
     *            $response
     * @param
     *            $message
     * @param
     *            $query
     * @param
     *            $filter
     * @param
     *            $outputstream
     * @param
     *            $no_return
     */
    private function smimap_read_data_list($imap_stream, $tag_uid, $handle_errors, &$response, &$message, $query = '')
    {
        $read = '';
        $tag_uid_a = explode(' ', trim($tag_uid));
        $tag = $tag_uid_a[0];
        $resultlist = array();
        $data = array();
        $read = $this->smimap_fgets($imap_stream);
        $i = 0;
        while ($read) {
            $char = $read{0};
            switch ($char) {
                case '+':
                    {
                        $response = 'OK';
                        break 2;
                    }
                default:
                    $read = $this->smimap_fgets($imap_stream);
                    break;
                case $tag{0}:
                    {
                        $arg = '';
                        $i = strlen($tag) + 1;
                        $s = substr($read, $i);
                        if (($j = strpos($s, ' ')) || ($j = strpos($s, "\n"))) {
                            $arg = substr($s, 0, $j);
                        }
                        $found_tag = substr($read, 0, $i - 1);
                        if ($arg && $found_tag == $tag) {
                            switch ($arg) {
                                case 'OK':
                                case 'BAD':
                                case 'NO':
                                case 'BYE':
                                case 'PREAUTH':
                                    $response = $arg;
                                    $message = trim(substr($read, $i + strlen($arg)));
                                    break 3; /* switch switch while */
                                default:
									/* this shouldn't happen */
									$response = $arg;
                                    $message = trim(substr($read, $i + strlen($arg)));
                                    break 3; /* switch switch while */
                            }
                        } elseif ($found_tag !== $tag) {
                            /* reset data array because we do not need this reponse */
                            $data = array();
                            $read = $this->smimap_fgets($imap_stream);
                            break;
                        }
                    } // end case $tag{0}
                
                case '*':
                    {
                        if (preg_match('/^\*\s\d+\sFETCH/', $read)) {
                            /* check for literal */
                            $s = substr($read, - 3);
                            $fetch_data = array();
                            do { /*
                                  * outer loop, continue until next untagged fetch
                                  * or tagged reponse
                                  */
                                do { /*
                                      * innerloop for fetching literals. with this loop
                                      * we prohibid that literal responses appear in the
                                      * outer loop so we can trust the untagged and
                                      * tagged info provided by $read
                                      */
                                    $read_literal = false;
                                    if ($s === "}\r\n") {
                                        $j = strrpos($read, '{');
                                        $iLit = substr($read, $j + 1, - 3);
                                        $fetch_data[] = $read;
                                        $sLiteral = $this->smimap_fread($imap_stream, $iLit);
                                        if ($sLiteral === false) { /* error */
                                            break 4; /* while while switch while */
                                        }
                                        /* backwards compattibility */
                                        $aLiteral = explode("\n", $sLiteral);
                                        /* release not neaded data */
                                        unset($sLiteral);
                                        foreach ($aLiteral as $line) {
                                            $fetch_data[] = $line . "\n";
                                        }
                                        /* release not neaded data */
                                        unset($aLiteral);
                                        /*
                                         * next fgets belongs to this fetch because
                                         * we just got the exact literalsize and there
                                         * must follow data to complete the response
                                         */
                                        $read = $this->smimap_fgets($imap_stream);
                                        if ($read === false) { /* error */
                                            break 4; /* while while switch while */
                                        }
                                        $s = substr($read, - 3);
                                        $read_literal = true;
                                        continue;
                                    } else {
                                        $fetch_data[] = $read;
                                    }
                                    /*
                                     * retrieve next line and check in the while
                                     * statements if it belongs to this fetch response
                                     */
                                    $read = $this->smimap_fgets($imap_stream);
                                    if ($read === false) { /* error */
                                        break 4; /* while while switch while */
                                    }
                                    /* check for next untagged reponse and break */
                                    if ($read{0} == '*')
                                        break 2;
                                    $s = substr($read, - 3);
                                } while ($s === "}\r\n" || $read_literal);
                                $s = substr($read, - 3);
                            } while ($read{0} !== '*' && substr($read, 0, strlen($tag)) !== $tag);
                            $resultlist[] = $fetch_data;
                            /* release not neaded data */
                            unset($fetch_data);
                        } else {
                            $s = substr($read, - 3);
                            do {
                                if ($s === "}\r\n") {
                                    $j = strrpos($read, '{');
                                    $iLit = substr($read, $j + 1, - 3);
                                    // check for numeric value to avoid that untagged responses like:
                                    // * OK [PARSE] Unexpected characters at end of address: {SET:debug=51}
                                    // will trigger literal fetching ({SET:debug=51} !== int )
                                    if (is_numeric($iLit)) {
                                        $data[] = $read;
                                        $sLiteral = fread($imap_stream, $iLit);
                                        if ($sLiteral === false) { /* error */
                                            $read = false;
                                            break 3; /* while switch while */
                                        }
                                        $data[] = $sLiteral;
                                        $data[] = $this->smimap_fgets($imap_stream);
                                    } else {
                                        $data[] = $read;
                                    }
                                } else {
                                    $data[] = $read;
                                }
                                $read = $this->smimap_fgets($imap_stream);
                                if ($read === false) {
                                    break 3; /* while switch while */
                                } else 
                                    if ($read{0} == '*') {
                                        break;
                                    }
                                $s = substr($read, - 3);
                            } while ($s === "}\r\n");
                            break 1;
                        }
                        break;
                    } // end case '*'
            } // end switch
        } // end while
        
        /* error processing in case $read is false */
        if ($read === false) {
            unset($data);
            $string = "<b>" . "ERROR: Connection dropped by IMAP server." . "</b><br />";
            $cmd = explode(' ', $query);
            $cmd = strtolower($cmd[0]);
            if ($query != '' && $cmd != 'login') {
                $string .= "Query:" . ' ' . htmlspecialchars($query) . '<br />' . "<br />";
            }
            echo $string;
        }
        
        /* Set $resultlist array */
        if (! empty($data)) {
            $resultlist[] = $data;
        } elseif (empty($resultlist)) {
            $resultlist[] = array();
        }
        /* Return result or handle errors */
        if ($handle_errors == false) {
            return $resultlist;
        }
        $close_connection = false;
        switch ($response) {
            case 'OK':
                return $resultlist;
                break;
            case 'NO':
				/* ignore this error from M$ exchange, it is not fatal (aka bug) */
				if (strstr($message, 'command resulted in') === false) {
                    echo '<span>command line:' . htmlspecialchars($query) . '</span></br>' . '<span>response:' . htmlspecialchars($message) . '</span>';
                    $close_connection = true;
                }
                break;
            case 'BAD':
                $string = '<b>' . 'ERROR: Bad or malformed request' . "</b><br />" . "Query:" . ' ' . htmlspecialchars($query) . '<br />' . "Server responded:" . ' ' . htmlspecialchars($message) . "<br />";
                echo $string;
                $close_connection = true;
                break;
            case 'BYE':
                $string = "<b>" . "ERROR: IMAP server closed the connection." . "</b><br />" . "Query:" . ' ' . htmlspecialchars($query) . '<br />' . "Server responded:" . ' ' . htmlspecialchars($message) . "<br />";
                echo $string;
                $close_connection = true;
                break;
            default:
                $string = '<b>' . 'ERROR: Unknown IMAP response' . '</b><br />' . 'Query:' . htmlspecialchars($query) . '<br />' . 'Server responded:' . ' ' . htmlspecialchars($message) . "<br />";
                echo $string;
                /*
                 * the error is displayed but because we don't know the reponse we
                 * return the result anyway
                 */
                $close_connection = true;
                break;
        }
        if ($close_connection) {
            $this->smimap_logout($imap_stream);
        }
    }

    /**
     * 从会话流中读取信息
     *
     * @param unknown_type $imap_stream            
     * @param unknown_type $iSize            
     * @param unknown_type $filter            
     * @param unknown_type $outputstream            
     * @param unknown_type $no_return            
     */
    protected function smimap_fread($imap_stream, $iSize)
    {
        $iBufferSize = $iSize;
        // see php bug 24033. They changed fread behaviour %$^&$%
        // $iBufferSize = 7800; // multiple of 78 in case of base64 decoding.
        if ($iSize < $iBufferSize) {
            $iBufferSize = $iSize;
        }
        
        $iRetrieved = 0;
        $results = '';
        $sRead = $sReadRem = '';
        // NB: fread can also stop at end of a packet on sockets.
        while ($iRetrieved < $iSize) {
            $sRead = fread($imap_stream, $iBufferSize);
            $iLength = strlen($sRead);
            $iRetrieved += $iLength;
            $iRemaining = $iSize - $iRetrieved;
            if ($iRemaining < $iBufferSize) {
                $iBufferSize = $iRemaining;
            }
            if ($sRead == '') {
                $results = false;
                break;
            }
            if ($sReadRem != '') {
                $sRead = $sReadRem . $sRead;
                $sReadRem = '';
            }
            
            if ($filter && $sRead != '') {
                $sReadRem = $filter($sRead);
            }
            $results .= $sRead;
        }
        return $results;
    }

    /**
     * Retreive the CAPABILITY string from the IMAP server.
     * If capability is set, returns only that specific capability,
     * else returns array of all capabilities.
     *
     * @param
     *            $imap_stream
     * @param
     *            $capability
     * @return array $smimap_capabilities
     *        
     */
    public function smimap_capability($imap_stream, $capability = '')
    {
        $read = $this->smimap_run_command($imap_stream, 'CAPABILITY', true, $a, $b);
        $c = explode(' ', $read[0]);
        for ($i = 2; $i < count($c); $i ++) {
            $cap_list = explode('=', $c[$i]);
            if (isset($cap_list[1])) {
                // FIX ME. capabilities can occure multiple times.
                // THREAD=REFERENCES THREAD=ORDEREDSUBJECT
                $smimap_capabilities[$cap_list[0]] = $cap_list[1];
            } else {
                $smimap_capabilities[$cap_list[0]] = TRUE;
            }
        }
        if ($capability) {
            if (isset($smimap_capabilities[$capability])) {
                return $smimap_capabilities[$capability];
            } else {
                return false;
            }
        }
        return $smimap_capabilities;
    }
}