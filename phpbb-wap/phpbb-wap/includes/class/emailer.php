<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/**
* emailer 类支持添加附件，虽然在当前版本中没有实现该功能
* 但有可能我们在以后的版本中实现
**/

class emailer
{
	// 声明变量
	var $msg;
	var $subject;
	var $extra_headers;
	var $addresses;
	var $reply_to;
	var $from;
	var $use_smtp;
	var $tpl_msg = array();
	var $vars;
	var $debug = flase;
	var $debug_msg = '';

	static $encode = 'utf-8';

	/**
	* 初始化
	* 参数：字符串 $use_smtp SMTP地址
	**/
	function __construct()
	{
		$this->reset();
		$this->reply_to = $this->from = '';
		$this->addresses['cc'][] = '';
		$this->addresses['bcc'][] = '';
	}

	/**
	* 重置地址、头部、信息等
	**/
	function reset()
	{
		$this->addresses = array();
		$this->vars = $this->msg = $this->extra_headers = '';
	}

	/**
	* 收人人
	* 参数：字符串 $address 收件人邮箱
	**/
	function email_address($address)
	{
		$this->addresses['to'] = trim($address);
	}

	/**
	* 抄送
	* 参数：字符串 $address 抄送邮箱
	**/
	function cc($address)
	{
		$this->addresses['cc'][] = trim($address);
	}

	/**
	* 密件抄送
	* 参数：字符串 $address 密件抄送邮箱
	**/
	function bcc($address)
	{
		$this->addresses['bcc'][] = trim($address);
	}

	/**
	* 回复
	* 参数：字符串 $address 回复的邮箱
	**/
	function replyto($address)
	{
		$this->reply_to = trim($address);
	}

	function from($address)
	{
		$this->from = trim($address);
	}

	// 设置邮件的标题
	function set_subject($subject = '')
	{
		$this->subject = trim(preg_replace('#[\n\r]+#s', '', $subject));
	}

	// 附上Email的 header
	function extra_headers($headers)
	{
		$this->extra_headers .= trim($headers) . "\n";
	}

	// 使用邮件模版
	function use_template($template_file)
	{
		global $board_config;

		if (trim($template_file) == '')
		{
			trigger_error('邮件模版不能为空', E_USER_WARNING);
		}

		if (empty($this->tpl_msg[$template_file]))
		{
			$tpl_file = ROOT_PATH . 'includes/email_templates/' . $template_file . '.tpl';

			if (!@file_exists(@phpbb_realpath($tpl_file)))
			{
				trigger_error("邮件模版 $template_file 不存在", E_USER_WARNING);
			}


			if (!($fd = @fopen($tpl_file, 'r')))
			{
				trigger_error("邮件模版 $tpl_file 无法打开");
			}

			$this->tpl_msg[$template_file] = fread($fd, filesize($tpl_file));
			
			fclose($fd);
		}

		$this->msg = $this->tpl_msg[$template_file];

		return true;
	}

	// 分配变量
	function assign_vars($vars)
	{
		$this->vars = (empty($this->vars)) ? $vars : $this->vars . $vars;
	}

	// 发送邮件的收件人以前在 var $this->address
	function send()
	{
		global $board_config, $db, $cache;

		// 避开引号, 否则会 eval 失败
		$this->msg = str_replace ("'", "\'", $this->msg);
		$this->msg = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->msg);

		// 重置变量 。 reset() 函数把数组的内部指针指向第一个元素，并返回这个元素的值
		reset ($this->vars);
		// each() 函数生成一个由数组当前内部指针所指向的元素的键名和键值组成的数组，并把内部指针向前移动
		foreach($this->vars as $key => $val)
		{
			$$key = $val;
		}

		// eval() 函数把字符串按照 PHP 代码来计算
		eval("\$this->msg = '$this->msg';");

		reset ($this->vars);
		foreach ($this->vars as $key => $val)
		{
			unset($$key);// unset() 函数用于销毁指定的变量
		}

		$drop_header = '';
		$match = array();
		if (preg_match('#^(Subject:(.*?))$#m', $this->msg, $match))
		{
			$this->subject = (trim($match[2]) != '') ? trim($match[2]) : (($this->subject != '') ? $this->subject : '无标题');
			$drop_header .= '[\r\n]*?' . preg_quote($match[1], '#');
		}
		else
		{
			$this->subject = (($this->subject != '') ? $this->subject : '无标题');
		}

		if (preg_match('#^(Charset:(.*?))$#m', $this->msg, $match))
		{
			$this->encoding = (trim($match[2]) != '') ? trim($match[2]) : trim($this->encode);
			$drop_header .= '[\r\n]*?' . preg_quote($match[1], '#');
		}
		else
		{
			$this->encoding = trim($this->encode);
		}

		if ($drop_header != '')
		{
			$this->msg = trim(preg_replace('#' . $drop_header . '#s', '', $this->msg));
		}

		$to 	= (isset($this->addresses['to'])) ? $this->addresses['to'] : '';
		$cc 	= (isset($this->addresses['cc'])) ? ((count($this->addresses['cc'])) ? implode(', ', $this->addresses['cc']) : '') : '';
		$bcc 	= (isset($this->addresses['bcc'])) ? ((count($this->addresses['bcc'])) ? implode(', ', $this->addresses['bcc']) : '') : '';

		// 创建 header
		$this->extra_headers = (($this->reply_to != '') ? "Reply-to: $this->reply_to\n" : '') . (($this->from != '') ? "From: $this->from\n" : "From: " . $board_config['board_email'] . "\n") . "Return-Path: " . $board_config['board_email'] . "\nMessage-ID: <" . md5(uniqid(time())) . "@" . $board_config['server_name'] . ">\nMIME-Version: 1.0\nContent-type: text/plain; charset=" . $this->encoding . "\nContent-transfer-encoding: 8bit\nDate: " . date('r', time()) . "\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\nX-MimeOLE: Produced By phpBB-WAP\n" . $this->extra_headers . (($cc != '') ? "Cc: $cc\n" : '')  . (($bcc != '') ? "Bcc: $bcc\n" : ''); 

		// 发送消息 ... 删除 $this->encode() 当时的标题
		// 使用SMTP发送邮件

		if ( !defined('SMTP_INCLUDED') ) 
		{
			require(ROOT_PATH . 'includes/functions/smtp.php');
		}

		$result = smtpmail($to, $this->subject, $this->msg, $this->extra_headers);// 内建函数

		
		if (!$result)
		{
			$this->debug = true;
			$this->debug_msg = '邮件发送失败，原因 :: ' . $result;
		}
	}

	function debug()
	{
		return array('debug' => $this->debug, 'debug_msg' => $this->debug_msg);
	}

	// 编码
	function encode($str)
	{
		if ($this->encoding == '')
		{
			return $str;
		}

		$end 	= "?=";
		$start 	= "=?$this->encoding?B?";
		$spacer = "$end\r\n $start";
		$length = 75 - strlen($start) - strlen($end);
		$length = floor($length / 2) * 2;
		$str = chunk_split(base64_encode($str), $length, $spacer);
		$str = preg_replace('#' . preg_quote($spacer, '#') . '$#', '', $str);

		return $start . $str . $end;
	}

} 

?>