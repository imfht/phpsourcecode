<?php
final class Sms {
/*--------------------------------
程序版权：上海创明信息技有限公司
服务热线：4008885262
技术  QQ：2355373292
修改时间：2013-08-18
程序功能：创明网PHP接口示例 通过接口进行单发、群发；
说明:		http://dxhttp.c123.cn/tx/?uid=用户账号&pwd=MD5位32密码&mobile=号码&content=内容
状态:
	100 发送成功
	101 验证失败
	102 短信不足
	103 操作失败
	104 非法字符
	105 内容过多
	106 号码过多
	107 频率过快
	108 号码内容空
	109 账号冻结
	110 禁止频繁单条发送
	111 系统暂定发送
	112 号码不正确
	113 定时时间格式不对
	114 账号被锁，10分钟后登录
	115 连接失败
	116 禁止接口发送
	117 绑定IP不正确
	120 系统升级
--------------------------------*/
public $url;
public $uid;		//用户账号
public $pwd;		//密码
public $mobile;	//号码,多个号码用逗号隔开
public $content;		//内容
public $time=''; //发送时间
public $mid=''; //可选项，根据用户账号是否支持扩展
//即时发送
public function sendSMS()
{
	//header("Content-Type: text/html; charset=utf-8");
	$data = array
		(
		'uid'=>$this->uid,					//用户账号
		'pwd'=>strtolower(md5($this->pwd)),	//MD5位32密码
		'mobile'=>$this->mobile,				//号码
		'content'=>$this->content,		    //如果页面是gbk编码，则转成utf-8编码，如果是页面是utf-8编码，则不需要转码
		'time'=>$this->time,		//定时发送
		'mid'=>$this->mid						//子扩展号
		);
	$re= $this->postSMS($this->url,$data);			//POST方式提交
	if(trim($re) == '100' )
	{
	return 'Y';
	}
	else 
	{
	return 'N';
	}
}

public function postSMS($url,$data='')
{
	$row = parse_url($url);
	$host = $row['host'];
	$port = 80;
	$file = $row['path'];
	$post='';
	while (list($k,$v) = each($data)) 
	{
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$post = substr( $post , 0 , -1 );
	$len = strlen($post);
	$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
	if (!$fp) {
		return "$errstr ($errno)\n";
	} else {
		$receive = '';
		$out = "POST $file HTTP/1.0\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Content-type: application/x-www-form-urlencoded\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Content-Length: $len\r\n\r\n";
		$out .= $post;		
		fwrite($fp, $out);
		while (!feof($fp)) {
			$receive .= fgets($fp, 128);
		}
		fclose($fp);
		$receive = explode("\r\n\r\n",$receive);
		unset($receive[0]);
		return implode("",$receive);
	}
}
}
?>