<?php
namespace Lib;

class Curl{

	private $curl=null;

	public function __construct() {

        if (!function_exists('curl_init')) { return false; }
        $curl = curl_init();
        //是否显示头部信息
        curl_setopt($curl, CURLOPT_HEADER, false);
        //获取的信息以文件流的形式返回，而不是直接输出；
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $this->curl = $curl;

	}

	public function __destruct(){
		curl_close($this->curl);
	}

	public function get($url){
		$curl=$this->curl;

        curl_setopt($curl, CURLOPT_URL, $url);

        $result=curl_exec($curl);
        return $result;
	}


	public function post($url, $data=array()) 
	{
		$curl=$this->curl;

		if(is_array($data)) $data = http_build_query($data);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($curl);
        return $result;

	}

	public function xmlToArray($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}

}

/*
*CURLOPT_INFILESIZE: 当你上传一个文件到远程站点，这个选项告诉PHP你上传文件的大小。 
*CURLOPT_VERBOSE: 如果你想CURL报告每一件意外的事情，设置这个选项为一个非零值。 
*CURLOPT_HEADER: 如果你想把一个头包含在输出中，设置这个选项为一个非零值。 
*CURLOPT_NOPROGRESS: 如果你不会PHP为CURL传输显示一个进程条，设置这个选项为一个非零值。 

注意：PHP自动设置这个选项为非零值，你应该仅仅为了调试的目的来改变这个选项。 

*CURLOPT_NOBODY: 如果你不想在输出中包含body部分，设置这个选项为一个非零值。 
*CURLOPT_FAILONERROR: 如果你想让PHP在发生错误(HTTP代码返回大于等于300)时，不显示，设置这个选项为一人非零值。默认行为是返回一个正常页，忽略代码。 
*CURLOPT_UPLOAD: 如果你想让PHP为上传做准备，设置这个选项为一个非零值。 
*CURLOPT_POST: 如果你想PHP去做一个正规的HTTP POST，设置这个选项为一个非零值。这个POST是普通的 application/x-www-from-urlencoded 类型，多数被HTML表单使用。 
*CURLOPT_FTPLISTONLY: 设置这个选项为非零值，PHP将列出FTP的目录名列表。 
*CURLOPT_FTPAPPEND: 设置这个选项为一个非零值，PHP将应用远程文件代替覆盖它。 
*CURLOPT_NETRC: 设置这个选项为一个非零值，PHP将在你的 ~./netrc 文件中查找你要建立连接的远程站点的用户名及密码。 
*CURLOPT_FOLLOWLOCATION: 设置这个选项为一个非零值(象 “Location: “)的头，服务器会把它当做HTTP头的一部分发送(注意这是递归的，PHP将发送形如 “Location: “的头)。 
*CURLOPT_PUT: 设置这个选项为一个非零值去用HTTP上传一个文件。要上传这个文件必须设置CURLOPT_INFILE和CURLOPT_INFILESIZE选项. 
*CURLOPT_MUTE: 设置这个选项为一个非零值，PHP对于CURL函数将完全沉默。 
*CURLOPT_TIMEOUT: 设置一个长整形数，作为最大延续多少秒。 
*CURLOPT_LOW_SPEED_LIMIT: 设置一个长整形数，控制传送多少字节。 
*CURLOPT_LOW_SPEED_TIME: 设置一个长整形数，控制多少秒传送CURLOPT_LOW_SPEED_LIMIT规定的字节数。 
*CURLOPT_RESUME_FROM: 传递一个包含字节偏移地址的长整形参数，(你想转移到的开始表单)。 
*CURLOPT_SSLVERSION: 传递一个包含SSL版本的长参数。默认PHP将被它自己努力的确定，在更多的安全中你必须手工设置。 
*CURLOPT_TIMECONDITION: 传递一个长参数，指定怎么处理CURLOPT_TIMEVALUE参数。你可以设置这个参数为TIMECOND_IFMODSINCE 或 TIMECOND_ISUNMODSINCE。这仅用于HTTP。 
*CURLOPT_TIMEVALUE: 传递一个从1970-1-1开始到现在的秒数。这个时间将被CURLOPT_TIMEVALUE选项作为指定值使用，或被默认TIMECOND_IFMODSINCE使用。 

下列选项的值将被作为字符串：　 

*CURLOPT_URL: 这是你想用PHP取回的URL地址。你也可以在用curl_init()函数初始化时设置这个选项。 
*CURLOPT_USERPWD: 传递一个形如[username]:[password]风格的字符串,作用PHP去连接。 
*CURLOPT_PROXYUSERPWD: 传递一个形如[username]:[password] 格式的字符串去连接HTTP代理。 
*CURLOPT_RANGE: 传递一个你想指定的范围。它应该是”X-Y”格式，X或Y是被除外的。HTTP传送同样支持几个间隔，用逗句来分隔(X-Y,N-M)。 
*CURLOPT_POSTFIELDS: 传递一个作为HTTP “POST”操作的所有数据的字符串。 
*CURLOPT_REFERER: 在HTTP请求中包含一个”referer”头的字符串。 
*CURLOPT_USERAGENT: 在HTTP请求中包含一个”user-agent”头的字符串。 
*CURLOPT_FTPPORT: 传递一个包含被ftp “POST”指令使用的IP地址。这个POST指令告诉远程服务器去连接我们指定的IP地址。 这个字符串可以是一个IP地址，一个主机名，一个网络界面名(在UNIX下)，或是‘-’(使用系统默认IP地址)。 
*CURLOPT_COOKIE: 传递一个包含HTTP cookie的头连接。 
*CURLOPT_SSLCERT: 传递一个包含PEM格式证书的字符串。 
*CURLOPT_SSLCERTPASSWD: 传递一个包含使用CURLOPT_SSLCERT证书必需的密码。 
*CURLOPT_COOKIEFILE: 传递一个包含cookie数据的文件的名字的字符串。这个cookie文件可以是Netscape格式，或是堆存在文件中的HTTP风格的头。 
*CURLOPT_CUSTOMREQUEST: 当进行HTTP请求时，传递一个字符被GET或HEAD使用。为进行DELETE或其它操作是有益的，更Pass a string to be used instead of GET or HEAD when doing an HTTP request. This is useful for doing or another, more obscure, HTTP request. 

注意: 在确认你的服务器支持命令先不要去这样做。 

下列的选项要求一个文件描述(通过使用fopen()函数获得)： 
　 
*CURLOPT_FILE: 这个文件将是你放置传送的输出文件，默认是STDOUT. 
*CURLOPT_INFILE: 这个文件是你传送过来的输入文件。 
*CURLOPT_WRITEHEADER: 这个文件写有你输出的头部分。 
*CURLOPT_STDERR: 这个文件写有错误而不是stderr。 

*/
