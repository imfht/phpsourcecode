<?php
/**
 * NOTICE OF LICENSE
 *
 * THIS SOURCE FILE IS PART OF EVEBIT'S PRIVATE PROJECT.
 *
 * DO NOT USE THIS FILE IN OTHER PLACE.
 *
 * @category    EveBit_Library
 * @package     Application
 * @author      Chen Qiao
 * @version     $$Id: Email.PHP 175 2011-03-26 09:52:16Z chen.qiao $$
 * @copyright   Copyright (c) 2011 Evebit Inc. China (http://www.evebit.com)
 */

/**
 * Email class
 *
 * get mail total count,get mail list,get mail content,get mail attach
 *
 * For a example, if you want to get some specified email list.
 *
 * $mail = new Evebit_Mail();
 * $mail->mailConnect($host,$port,$user,$pass,'INBOX',$ssl);
 * $mail->mail_list('5,9:20');
 *
 *
 * show the five,and nine to twenty mail.
 *
 * $mail->mail_list('5,9:20');
 *
 *
 * @docinfo
 *
 * @package     Application
 * @author      Chen Qiao
 * @version     $$Id: Email.PHP 175 2011-03-26 09:52:16Z chen.qiao $$
 */

class receiveMail {

	/**
	 * @var resource $_connect
	 */
	private $_connect;
	/**
	 * @var object $_mailInfo
	 */
	private $_mailInfo;
	/**
	 * @var int $_total_count
	 */
	private $_total_count;
	/**
	 * @var array $_total_count
	 */
	/**
	 * __construct of the class
	 */
	public function __construct() {

	}

	/**
	 * Open an IMAP stream to a mailbox
	 *
	 * @param string $host
	 * @param string $port
	 * @param string $user
	 * @param string $pass
	 * @param string $folder
	 * @param string $ssl
	 * @param string $pop
	 * @return resource|bool
	 */
	public function connect($host, $port, $user, $pass, $folder = "INBOX", $ssl, $pop = false) {
		if ($pop) {
			$ssl = $pop . '/' . $ssl . '/novalidate-cert/notls';
		}
		$this -> _connect = imap_open("{" . "$host:$port/$ssl" . "}$folder", $user, $pass);

		if (!$this -> _connect) {
			return false;
		}
		return $this -> _connect;
	}

	/**
	 * Get information about the current mailbox
	 *
	 * @return object|bool
	 */
	public function mailInfo() {
		$this -> _mailInfo = imap_mailboxmsginfo($this -> _connection);
		if (!$this -> _mailInfo) {
			echo "get mailInfo failed: " . imap_last_error();
			return false;
		}
		return $this -> _mailInfo;
	}

	/**
	 * Read an overview of the information in the headers of the given message
	 *
	 * @param string $msg_range
	 * @return array
	 */
	 
	public function mail_list($msg_range = '') {
		if ($msg_range) {
			$range = $msg_range;
		} else {
			$this -> mail_total_count();
			$range = "1:" . $this -> _total_count;
		}
		$overview = imap_fetch_overview($this -> _connect, $range);
		foreach ($overview as $val) {
			$mail_list[$val -> msgno] = $val -> message_id;
		}
		return $mail_list;
	}

	/**
	 * get the total count of the current mailbox
	 *
	 * @return int
	 */
	public function mail_total_count() {
		$check = imap_check($this -> _connect);
		$this -> _total_count = $check -> Nmsgs;
		return $this -> _total_count;		
	}
	
	public function get_unread_list(){
		$uids   = imap_search($this->_connect, 'UNSEEN', SE_UID);
		return $uids;		
	}

	/**
	 * Read the header of the message
	 *
	 * @param string $msg_count
	 * @return array
	 */

	public function mail_header($msg_count) {
		$mail_header = array();
		$filter = array("To", "From", "Cc", "Subject", "Date", "Reply-to", "Message-ID");
		$arr_header = explode("\n", imap_fetchheader($this -> _connect, $msg_count));
		foreach ($arr_header as $val) {
			$tmp = array_shift(explode(":", $val));
			if (in_array($tmp, $filter)) {
				if ($tmp == "Subject") {
					$val = substr($val, strlen($tmp) + 1);
					$mail_header['name'] = $this -> mail_decode($val);
				}
				if ($tmp == "From") {
					$val = substr($val, strlen($tmp) + 1);
					$mail_header['from'] = $this -> contact_conv($val);
				}
				if ($tmp == "To") {
					$val = substr($val, strlen($tmp) + 1);
					$mail_header['to'] = $this -> contact_conv($val);
				}
				if ($tmp == "Cc") {
					$val = substr($val, strlen($tmp) + 1);
					$mail_header['cc'] = $this -> contact_conv($val);
				}
				if ($tmp == "Date") {
					$val = substr($val, strlen($tmp) + 1);
					$mail_header['create_time'] = strtotime($val);
				}
				if ($tmp == "Message-ID") {
					$val = substr($val, strlen($tmp) + 1);
					$mail_header['mid'] = trim($val);
				}
			}
		}
		if (empty($mail_header['mid'])) {
			$mail_header['mid'] = $mail_header['from'] . $mail_header['create_time'];
		}
		$mail_header['content'] = $this -> get_body($msg_count);
		return $mail_header;
	}

	/**
	 * decode the subject of chinese
	 *
	 * @param string $subject
	 * @return sting
	 */
	function mail_decode($str) {
		if (stripos($str, 'GBK?B')) {
			$arr_temp = explode(" ", $str);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?GBK?B?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp2 = $tmp2 . $this -> auto_charset(base64_decode($tmp), 'gb2312', 'utf-8');
			}
			return $tmp2;
		}
		if (stripos($str, 'GBK?Q')) {
			$arr_temp = explode(" ", $str);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?GBK?B?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp2 = $tmp2 . $this -> auto_charset(base64_decode($tmp), 'gb2312', 'utf-8');
			}
			return $tmp2;
		}
		if (stripos($str, 'utf-8?B')) {
			$arr_temp = explode(" ", $str);
			// dump($arr_temp[0]);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?utf-8?B?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp2 = $tmp2 . base64_decode($tmp);
			}
			return $tmp2;
		}
		if (stripos($str, 'utf-8?Q')) {
			$arr_temp = explode(" ", $str);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?utf-8?Q?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp = str_ireplace('?', '', $tmp);
				$tmp2 = $tmp2 . quoted_printable_decode($tmp);
			}
			return $tmp2;
		}
		if (stripos($str, 'gb2312?B')) {
			$arr_temp = explode(" ", $str);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?gb2312?B?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp2 = $tmp2 . $this -> auto_charset(base64_decode($tmp), 'gb2312', 'utf-8');
			}
			return $tmp2;
		}
		if (stripos($str, 'gb2312?Q')) {
			$arr_temp = explode(" ", $str);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?gb2312?Q?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp = str_ireplace('?', '', $tmp);
				$tmp2 = $tmp2 . $this -> auto_charset(quoted_printable_decode($tmp), 'gb2312', 'utf-8');
			}
			return $tmp2;
		}
		if (stripos($str, 'gb18030?B')) {
			$arr_temp = explode(" ", $str);
			// dump($arr_temp[0]);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?gb18030?B?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp2 = $tmp2 . auto_charset(base64_decode($tmp), 'gb2312', 'utf-8');
			}
			return $tmp2;
		}
		if (stripos($str, 'gb18030?Q')) {
			$arr_temp = explode(" ", $str);
			for ($i = 0; $i <= count($arr_temp); $i++) {
				$tmp = str_ireplace('=?gb18030?Q?', '', $arr_temp[$i]);
				$tmp = str_ireplace('=?', '', $tmp);
				$tmp = str_ireplace('?', '', $tmp);
				$tmp2 = $tmp2 . $this -> auto_charset(quoted_printable_decode($tmp), 'gb2312', 'utf-8');
			}
			return $tmp2;
		}
		return $str;
	}

	function auto_charset($fContents, $from, $to) {
		$gbk = array('GBK', 'GB2312', 'GB18030');
		$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
		$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
		if (in_array(strtoupper($from), $gbk)) {
			$from = "gb2312";
		}
		if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
			//如果编码相同或者非字符串标量则不转换
			return $fContents;
		}
		if (is_string($fContents)) {
			if (function_exists('mb_convert_encoding')) {
				return mb_convert_encoding($fContents, $to, $from);
			} elseif (function_exists('iconv')) {
				return iconv($from, $to, $fContents);
			} else {
				return $fContents;
			}
		} elseif (is_array($fContents)) {
			foreach ($fContents as $key => $val) {
				$_key = $this -> auto_charset($key, $from, $to);
				$fContents[$_key] = $this -> auto_charset($val, $from, $to);
				if ($key != $_key)
					unset($fContents[$key]);
			}
			return $fContents;
		} else {
			return $fContents;
		}
	}

	/**
	 * Mark a message for deletion from current mailbox
	 *
	 * @param string $msg_count
	 */
	public function delete($msg_count) {
		imap_delete($this -> _connect, $msg_count);
	}

	/**
	 * get attach of the message
	 *
	 * @param string $msg_count
	 * @param string $path
	 * @return array
	 */
	public function get_attach($msg_count) {
		if (!$this -> _connect)
			return false;
		$struckture = imap_fetchstructure($this -> _connect, $msg_count);
		$return = "";
		if ($struckture -> parts) {
			foreach ($struckture->parts as $key => $value) {
				$enc = $struckture -> parts[$key] -> encoding;
				$subtype = $struckture -> parts[$key] -> subtype;
				$text_type = array("PLAIN", "HTML", "ALTERNATIVE");
				if (in_array($subtype, $text_type)) {
					continue;
				}

				if ($struckture -> parts[$key] -> ifdparameters) {
					$name = $this -> mail_decode($struckture -> parts[$key] -> dparameters[0] -> value);
					$cid = $struckture -> parts[$key] -> id;
					$cid = substr($cid, 1, strlen($cid) - 2);
					$disposition = $struckture -> parts[$key] -> disposition;
					if (empty($disposition)) {
						$disposition = "INLINE";
					}

					$message = imap_fetchbody($this -> _connect, $msg_count, $key + 1);
					if ($enc == 0)
						$message = imap_8bit($message);
					if ($enc == 1)
						$message = imap_8bit($message);
					if ($enc == 2)
						$message = imap_binary($message);
					if ($enc == 3)
						$message = imap_base64($message);
					if ($enc == 4)
						$message = quoted_printable_decode($message);
					if ($enc == 5)
						$message = $message;

					$tmpfile = tempnam(sys_get_temp_dir(), 'helloxiaowei');
					$str_file = $cid . "|" . $disposition . "|" . $name . "|" . $tmpfile;
					$fp = fopen($tmpfile, "w");

					fwrite($fp, $message);
					fclose($fp);
					$return = $return . $str_file . "?";
				}

				if (($struckture -> parts[$key] -> ifparameters) && ($struckture -> parts[$key] -> ifdparameters == 0)) {
					if ($struckture -> parts[$key] -> parameters[0] -> attribute == "NAME") {
						$name = $this -> mail_decode($struckture -> parts[$key] -> parameters[0] -> value);
					}
					if ($struckture -> parts[$key] -> parameters[1] -> attribute == "NAME") {
						$name = $this -> mail_decode($struckture -> parts[$key] -> parameters[1] -> value);
					}

					$cid = $struckture -> parts[$key] -> id;
					$cid = substr($cid, 1, strlen($cid) - 2);
					$disposition = $struckture -> parts[$key] -> disposition;
					if (empty($disposition)) {
						$disposition = "INLINE";
					}

					$message = imap_fetchbody($this -> _connect, $msg_count, $key + 1);
					if ($enc == 0)
						$message = imap_8bit($message);
					if ($enc == 1)
						$message = imap_8bit($message);
					if ($enc == 2)
						$message = imap_binary($message);
					if ($enc == 3)
						$message = imap_base64($message);
					if ($enc == 4)
						$message = quoted_printable_decode($message);
					if ($enc == 5)
						$message = $message;

					$tmpfile = tempnam(sys_get_temp_dir(), 'helloxiaowei');
					$str_file = $cid . "|" . $disposition . "|" . $name . "|" . $tmpfile;
					$fp = fopen($tmpfile, "w");

					fwrite($fp, $message);
					fclose($fp);
					$return = $return . $str_file . "?";
				}
				if ($struckture -> parts[$key] -> parts) {
					foreach ($struckture->parts[$key]->parts as $keyb => $valueb) {
						$enc = $struckture -> parts[$key] -> parts[$keyb] -> encoding;
						if ($struckture -> parts[$key] -> parts[$keyb] -> ifdparameters) {
							$name = $this -> mail_decode($struckture -> parts[$key] -> parts[$keyb] -> dparameters[0] -> value);

							$id = $struckture -> parts[$key] -> parts[$keyb] -> id;

							$disposition = $struckture -> parts[$key] -> parts[$keyb] -> disposition;

							$str_file = $id . "|" . $disposition . "|" . $name;

							$partnro = ($key + 1) . "." . ($keyb + 1);

							$message = imap_fetchbody($this -> _connect, $msg_count, $partnro);

							if ($enc == 0)
								$message = imap_8bit($message);
							if ($enc == 1)
								$message = imap_8bit($message);
							if ($enc == 2)
								$message = imap_binary($message);
							if ($enc == 3)
								$message = imap_base64($message);
							if ($enc == 4)
								$message = quoted_printable_decode($message);
							if ($enc == 5)
								$message = $message;

							$tmpfile = tempnam(sys_get_temp_dir(), 'helloxiaowei');
							$str_file = $cid . "|" . $disposition . "|" . $name . "|" . $tmpfile;
							$fp = fopen($tmpfile, "w");

							fwrite($fp, $message);
							fclose($fp);
							$return = $return . $str_file . "?";
						}
					}
				}
			}
		}
		$return = substr($return, 0, (strlen($return) - 1));
		return $return;
	}

	/**
	 * get the body of the message
	 *
	 * @param string $msg_count
	 * @return string
	 */
	public function get_body($msg_count) {
		$body = $this -> get_part($msg_count, "TEXT/HTML");
		if ($body == '') {
			$body = $this -> get_part($msg_count, "TEXT/PLAIN");
		}
		if ($body == '') {
			return '';
		}
		return $this -> mail_decode($body);
	}

	/**
	 * Read the structure of a particular message and fetch a particular
	 * section of the body of the message
	 *
	 * @param string $msg_count
	 * @param string $mime_type
	 * @param object $structure
	 * @param string $part_no
	 * @return string|bool
	 */
	private function get_part($msg_count, $mime_type, $structure = false, $part_no = false) {
		if (!$structure) {
			$structure = imap_fetchstructure($this -> _connect, $msg_count);
		}
		if ($structure) {
			if ($mime_type == $this -> get_mime_type($structure)) {
				if (!$part_no) {
					$part_no = "1";
				}
				$from_encoding = $structure -> parameters[0] -> value;
				$text = imap_fetchbody($this -> _connect, $msg_count, $part_no);
				if ($structure -> encoding == 3) {
					$text = imap_base64($text);
				} else if ($structure -> encoding == 4) {
					$text = imap_qprint($text);
				}

				//$text = mb_convert_encoding($text,'utf-8',$from_encoding);
				$text = $this -> auto_charset($text, $from_encoding, 'utf-8');
				return $text;
			}
			if ($structure -> type == 1) {
				while (list($index, $sub_structure) = each($structure -> parts)) {
					if ($part_no) {
						$prefix = $part_no . '.';
					}
					$data = $this -> get_part($msg_count, $mime_type, $sub_structure, $prefix . ($index + 1));
					if ($data) {
						return $data;
					}
				}
			}
		}
		return false;
	}

	/**
	 * get the subtype and type of the message structure
	 *
	 * @param object $structure
	 */
	private function get_mime_type($structure) {
		$mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
		if ($structure -> subtype) {
			return $mime_type[(int)$structure -> type] . '/' . $structure -> subtype;
		}
		return "TEXT/PLAIN";
	}

	/**
	 * put the message from unread to read
	 *
	 * @param string $msg_count
	 * @return bool
	 */
	 
	public function mail_readed($msg_count) {
		$uid=imap_uid($this -> _connect,$msg_count);
		$status = imap_setflag_full($this -> _connect,$msg_count, '\Seen',ST_UID);
		return $status;
	}

	/**
	 * Close an IMAP stream
	 */
	public function close_mail() {
		imap_close($this -> _connect, CL_EXPUNGE);
	}

	function contact_conv($contact) {
		$arr_tmp = explode(",", $contact);
		$new = '';
		foreach ($arr_tmp as $vo) {
			$tmp = array_filter(explode(" ", $vo));
			$filter = array("<", ">");
			$new .= $this -> mail_decode($tmp[1]) . "|" . str_replace($filter, '', $tmp[2]) . ";";
		}
		return $new;
	}
}
?>