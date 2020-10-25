<?php
define('SMTP_STATUS_NOT_CONNECTED', 1, TRUE);
define('SMTP_STATUS_CONNECTED', 2, TRUE);
/**
* @desc   SMTP 邮件服务器
* @param  服务器参数和邮件信息
* @author 张长伟
* @date   2010-1-10 22:17:14
* @contact QQ:462178176
*/
class p8_smtp
{
    var $connection;
    var $recipients;
    var $headers;
    var $timeout;
    var $errors;
    var $status;
    var $body;
    var $from;
    var $host;
    var $port;
    var $helo;
    var $auth;
    var $user;
    var $pass;
    
    /**
     *  参数为一个数组
     *  host        SMTP 服务器的主机       默认：localhost
     *  port        SMTP 服务器的端口       默认：25
     *  helo        发送HELO命令的名称      默认：localhost
     *  user        SMTP 服务器的用户名     默认：空值
     *  pass        SMTP 服务器的登陆密码   默认：空值
     *  timeout     连接超时的时间          默认：5
     *  @return  bool
     */
    
    function p8_smtp($params = array())
    {
        if(!defined('CRLF')) define('CRLF', "\r\n", TRUE);
        
        $this->timeout  = 5;
        $this->status   = SMTP_STATUS_NOT_CONNECTED;
        $this->host     = 'localhost';
        $this->port     = 25;
        $this->auth     = FALSE;
        $this->user     = '';
        $this->pass     = '';
        $this->errors   = array();
        foreach($params as $key => $value)
        {
            $this->$key = $value;
        }
        
        $this->helo     = $this->host;
        //  如果没有设置用户名则不验证        
        $this->auth = ('' == $this->user) ? FALSE : TRUE;
    }
    function connect($params = array())
    {
        if(!isset($this->status))
        {
            $obj = new p8_smtp($params);
            
            if($obj->connect())
            {
                $obj->status = SMTP_STATUS_CONNECTED;
            }
            return $obj;
        }
        else
        {
            
            $this->connection = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
            socket_set_timeout($this->connection, 0, 250000);
            $greeting = $this->get_data();
            
            if(is_resource($this->connection))
            {
                $this->status = 2;
                return $this->auth ? $this->ehlo() : $this->helo();
            }
            else
            {
                $this->errors[] = 'Failed to connect to server: '.$errstr;
                return FALSE;
            }
        }
    }
    
    /**
     * 参数为数组
     * recipients      接收人的数组
     * from            发件人的地址，也将作为回复地址
     * headers         头部信息的数组
     * body            邮件的主体
     */
    
    function send($params = array())
    {
        foreach($params as $key => $value)
        {
            $this->set($key, $value);
        }
        if($this->is_connected())
        {
            //  服务器是否需要验证     
            if($this->auth)
            {
                if(!$this->auth()) return FALSE;
            }
            $this->mail($this->from);
            if(is_array($this->recipients))
            {
                foreach($this->recipients as $value)
                {
                    $this->rcpt($value);
                }
            }
            else
            {
                $this->rcpt($this->recipients);
            }
            if(!$this->data()) return FALSE;
            $headers = str_replace(CRLF.'.', CRLF.'..', trim(implode(CRLF, $this->headers)));
            $body    = str_replace(CRLF.'.', CRLF.'..', $this->body);
            $body    = $body[0] == '.' ? '.'.$body : $body;
            $this->send_data($headers);
            $this->send_data('');
            $this->send_data($body);
            $this->send_data('.');
            return (substr(trim($this->get_data()), 0, 3) === '250');
        }
        else
        {
            $this->errors[] = 'Not connected!';
            return FALSE;
        }
    }
    
    function helo()
    {
        if(is_resource($this->connection)
                AND $this->send_data('HELO '.$this->helo)
                AND substr(trim($error = $this->get_data()), 0, 3) === '250' )
        {
            return TRUE;
        }
        else
        {
            $this->errors[] = 'HELO command failed, output: ' . trim(substr(trim($error),3));
            return FALSE;
        }
    }
    
    
    function ehlo()
    {
        if(is_resource($this->connection)
                AND $this->send_data('EHLO '.$this->helo)
                AND substr(trim($error = $this->get_data()), 0, 3) === '250' )
        {
            return TRUE;
        }
        else
        {
            $this->errors[] = 'EHLO command failed, output: ' . trim(substr(trim($error),3));
            return FALSE;
        }
    }
    
    function auth()
    {
        if(is_resource($this->connection)
                AND $this->send_data('AUTH LOGIN')
                AND substr(trim($error = $this->get_data()), 0, 3) === '334'
                AND $this->send_data(base64_encode($this->user))            // Send username
                AND substr(trim($error = $this->get_data()),0,3) === '334'
                AND $this->send_data(base64_encode($this->pass))            // Send password
                AND substr(trim($error = $this->get_data()),0,3) === '235' )
        {
            return TRUE;
        }
        else
        {
            $this->errors[] = 'AUTH command failed: ' . trim(substr(trim($error),3));
            return FALSE;
        }
    }
    
    function mail($from)
    {
        if($this->is_connected()
            AND $this->send_data('MAIL FROM:<'.$from.'>')
            AND substr(trim($this->get_data()), 0, 2) === '250' )
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    function rcpt($to)
    {
        if($this->is_connected()
            AND $this->send_data('RCPT TO:<'.$to.'>')
            AND substr(trim($error = $this->get_data()), 0, 2) === '25' )
        {
            return TRUE;
        }
        else
        {
            $this->errors[] = trim(substr(trim($error), 3));
            return FALSE;
        }
    }
    function data()
    {
        if($this->is_connected()
            AND $this->send_data('DATA')
            AND substr(trim($error = $this->get_data()), 0, 3) === '354' )
        { 
            return TRUE;
        }
        else
        {
            $this->errors[] = trim(substr(trim($error), 3));
            return FALSE;
        }
    }
    function is_connected()
    {
        return (is_resource($this->connection) AND ($this->status === SMTP_STATUS_CONNECTED));
    }
    function send_data($data)
    {
        if(is_resource($this->connection))
        {
            return fwrite($this->connection, $data.CRLF, strlen($data)+2);
        }
        else
        {
            return FALSE;
        }
    }
    function &get_data()
    {
        $return = '';
        $line   = '';
        if(is_resource($this->connection))
        {
            while(strpos($return, CRLF) === FALSE OR substr($line,3,1) !== ' ')
            {
                $line    = fgets($this->connection, 512);
                $return .= $line;
            }
            return $return;
        }
        else
        {
            return FALSE;
        }
    }
    function set($var, $value)
    {
        $this->$var = $value;
        return TRUE;
    }
} // End of class



class Email
{
	var $debug;
	var $host;
	var $port;
	var $auth;
	var $user;
	var $pass;
	
    public function __construct($params){
		$this->host=$params[0];
		$this->port=$params[1];
		$this->auth=$params[2];
		$this->user=$params[3];
		$this->pass=$params[4];
    }
		
	function smtp($host = "", $port = 25,$auth = false,$user,$pass){
		$this->host=$host;
		$this->port=$port;
		$this->auth=$auth;
		$this->user=$user;
		$this->pass=$pass;
	}

	function sendmail($to,$from, $subject, $content, $T=0){
		global $webdb;
		
		//$name, $email, $subject, $content, $type=0
		$type=1;
		$name=array("会员");
		$email=array($to);
		$_CFG['smtp_host']= $this->host;
		$_CFG['smtp_port']= $this->port;
		$_CFG['smtp_user']= $this->user;
		$_CFG['smtp_pass']= $this->pass;
		$_CFG['name']= $name;
		$_CFG['smtp_mail']= $from;

		//$name = "=?UTF-8?B?".base64_encode($name)."==?=";
		$subject = ($type == 0) ?"=?gbk?B?".base64_encode($subject)."==?=":"=?UTF-8?B?".base64_encode($subject)."==?=";
		$content = base64_encode($content);
		$headers[] = ($type == 0) ?"To:=?gbk?B?".base64_encode($name[0])."?= <$email[0]>":"To:=?UTF-8?B?".base64_encode($name[0])."?= <$email[0]>";
		$headers[] = ($type == 0) ?"From:=?gbk?B?".base64_encode($_CFG['name'])."?= <$_CFG[smtp_mail]>":"From:=?UTF-8?B?".base64_encode($_CFG['name'])."?= <$_CFG[smtp_mail]>";
		$headers[] = "MIME-Version: Blueidea v1.0";
		$headers[] = "X-Mailer: 9gongyu Mailer v1.0";
		//$headers[] = "From:=?UTF-8?B?".base64_encode($_CFG['shop_name'])."==?=<$_CFG[smtp_mail]>";
		$headers[] = "Subject:$subject";
		$headers[] = ($type == 0) ? "Content-Type: text/plain; charset=gbk; format=flowed" : "Content-Type: text/html; charset=UTF-8; format=flowed";
		$headers[] = "Content-Transfer-Encoding: base64";
		$headers[] = "Content-Disposition: inline";
		//    SMTP 服务器信息
		$params['host'] = $_CFG['smtp_host'];
		$params['port'] = $_CFG['smtp_port'];
		$params['user'] = $_CFG['smtp_user'];
		$params['pass'] = $_CFG['smtp_pass'];
		if (empty($params['host']) || empty($params['port']))
		{
			// 如果没有设置主机和端口直接返回 false
			return false;
		}
		else
		{
			//  发送邮件
			$send_params['recipients']	= $email;
			$send_params['headers']     = $headers;
			$send_params['from']        = $_CFG['smtp_mail'];
			$send_params['body']        = $content;
			        
/*			echo "<pre>";
			print_r($params);
			print_r($send_params);
			echo "</pre>";
			//exit;*/
			
			$smtp = new p8_smtp($params);
			if($smtp->connect() AND $smtp->send($send_params))
			{
				return TRUE;
			}
			else 
			{
				return FALSE;
			} // end if
		}
	}
}

?>