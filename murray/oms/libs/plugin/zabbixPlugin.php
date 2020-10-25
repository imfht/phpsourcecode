<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package zabbix类
*/

defined('INPOP') or exit('Access Denied');

class zabbixPlugin{

	public $username;
	public $passwd;
	public $tmpfile;   
//$_SESSION['username']  $_SESSION['passwd']
	public function __construct($username = '', $passwd = ''){
		$this->username = $username;
		$this->passwd = $passwd;
		$this->tmpfile = '/tmp/'.$username.'txt';
	}
    
    //生成图片
	public function getgraph($graphid = '' , $period = '',$stime = ''){
				if(!$graphid) return false;
				if(!$period) return false;
				if(!$stime) return false;
				
        //$this->savecookie($this->username , $this->passwd,$this->tmpfile);
        $this->savecookie($this->username , $this->passwd,'/tmp/cookie.txt');
        $this->outputgraph($graphid , $period,$stime,'/tmp/cookie.txt',800,300);
        return $graphid.$stime.$period.'.png';	
        
    }


    //zabbix验证 
	public function savecookie($username = '' , $passwd = '', $tmpfile = ''){
		$url = "http://localhost/monitor/index.php" ;
		//$poststr='request=&name='.$username.'&password='.$passwd.'&autologin=1&enter=Sign+in';
		$poststr="request=&name=admin&password=Esun@521&autologin=1&enter=Sign+in";

		$ch = curl_init($url);  
		curl_setopt($ch, CURLOPT_HEADER, 0);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');  
		curl_setopt($ch, CURLOPT_POST, 1 ) ;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $poststr );
		curl_exec($ch);
		curl_close($ch);
//print_r($o);
return '/tmp/'.$username.$passwd;	
	}
	//获取graph
	public function outputgraph($graphid = '' , $period = '',$stime = '', $tmpfile = '' ,$width = '',$height = ''){
		$url = "http://localhost/monitor/chart2.php" ;
		
		$fields = array(
               'graphid'=>$graphid,
               'period'=>$period,
               'stime'=>$stime,
               'width'=>$width ,
               'height'=>$height 
              );

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
		curl_setopt($ch, CURLOPT_POST,count($fields)) ;
		curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$response = curl_exec($ch);
		$fp = fopen('/tmp/'.$graphid.$stime.$period.$this->passwd.$this->username.'.png', 'wb'); 
		fwrite($fp,$response);
		fclose($fp);
		curl_close($ch);
    return '/tmp/';
    
	}
}





