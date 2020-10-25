<?php
/**
 * @file    request
 * @author  jesse <microji@126.com>
 * @created Apr 13, 2013 3:59:18 PM
 */

/**
 * php实现HTTP请求封装类，遵循一次只做好一件事的Unix/Linux哲学，没有解析任何请求结果，
 * 不能执行重定向指令，如果有需求，应在调用代码里实现，返回结果放入 $this->result，头部在$this->header里，
 * 出错时，$this->error含有错误信息
 * KC_Request Class to request data ! 
 * 设置rawheaders类型, User-Agent, Accept, Referer 等
 * 如果设置的post数据为字符串，则默认不会编码，如果需要编码，请手动调用 encode_data编码，
 * 如果设置了初始化的raw_cookies，则，该cookie也不会被编码，可以调用 encode_cookie手动编码。
 */
class KC_Request{
    var $curl_path      = '/usr/bin/curl' ; // freebsd curl 路径 '/usr/local/bin/curl'
    var $with_util      = 'socket' ;        // 请求的方式：socket|curl|php_curl
    var $tmp_dir        = '/tmp';           // tmp 目录
    var $raw_cookies    = array() ;         // 允许赋值！
    var $raw_headers    = array() ;         // 未编码的头部
    var $timeout        = 30;               // 设置超时 timeout !
    
    var $error          = '';               // 错误消息
    var $result         = '';               // 结果
    var $header         = '';               // 返回头部    
    var $cookie         = array();          // 服务器返回的cookie
    var $redirect       = '';               // 服务器返回的location 跳转
    
    var $root           = '';          // url 根路径
    var $follow         = false;       // 是否跟随请求的cookie
    var $protocol       = '';          // 明确指定套接字协议，有些网站的协议出现问题可以明确指定，如 ssl不能正确识别是 sslv2还是sslv3时
    var $host_ip        = '';          // 手动指定hostip
    private $_url       = '';          // 当前需要请求的 url
    private $_parsed    = '';          // url分析后返回的参数数组
    private $_cookies   = '';          // 经过编码后的cookie字符串，由函数生成，不要赋值！
    private $_headers   = '';          // 经过整理后的rawheaders字符串，不要赋值！
    
    function __construct($root='', $follow = false) {
        $this->root   = $root; 
        $this->follow = $follow ;
        $this->raw_headers = array('Accept-Encoding'=>'gzip,deflate');
    }
    
    public function fetch($method, $url, $data=array(), $opts=array()){
        if('GET'==$method){
            return $this->get($url, $data, $opts);
        }else{
            return $this->post($url, $data, $opts);
        }
    }
    
    public function current_url(){
        return $this->_url;
    }
    public function get($url, $data=array(), $opts=array()){
        return $this->_request($url, 'GET', $data, $opts);
    }
    
    public function post($url, $data=array(), $opts=array()){
        return $this->_request($url, 'POST', $data, $opts);
    }
    
    // 返回经过编码后的post_string 值
    public function encode_data($body){
        $items = explode('&', $body);
        $encoded = array();
        foreach($items as $e){
            list($key, $val) = explode('=', $e, 2);
            $encoded[] = urlencode($key).'='.urlencode($val);
        } // 或者：parse_str($body, $x); return http_build_query($x);
        return implode('&', $encoded); 
    }

    // 返回经过编码后的cookie值
    public function encode_cookie($cookie){
        $en_ck = array();
        if (!empty($cookie) && is_array($cookie)) {
            foreach ($cookie as $k => $v) {
                $en_ck[urlencode($k)] = urlencode($v);
            }
        }
        return $en_ck;
    }

    public function to_utf8(){
        $f = 'UTF-8';
        if(preg_match('/Content-Type:.*charset=([\w-]+?)\r\n/i', $this->header, $m)){
            $f = $m[1];
        }elseif(preg_match('/<meta.*charset="?([\w-]+?)"?\s*\/?>/i', $this->result, $m)){
            $f = $m[1];
        }
        return 'UTF-8'==$f ? $this->result : iconv($f, 'UTF-8', $this->result);
    }

    private function _request($url, $method, $data=array(), $opts=array()){
        if(!in_array($this->with_util, array('php_curl', 'curl', 'socket'))){
            $this->with_util = 'socket';
        }
        $address = trim($url, " \r\n") ;
        if($this->root && !preg_match('/^https?:\/\//', $address)){
            $address = $this->root . $address;
        }
        $query_string = !empty($data) ? (is_array($data) ? http_build_query($data) : $data) : '';
        $post = '';
        if($query_string){
            if('GET' == $method) {
                $address .= (false!==strpos($url, '?') ? '&' : '?').$query_string;
            }else{
                $post = $query_string; // query_string 请手动编码！或调用上面的 post_encode函数
            }
        }
        $this->error    = '';
        $this->result   = ''; // 先清空返回值，防止上次数据残留
        $this->header   = '';
        
        $append_headers  = array();
        if(!empty($opts) && !empty($opts['append_headers'])){
            $append_headers = $opts['append_headers'];
        }
        
        $this->_parse_url($address);
        $this->_build_cookie();
        $this->_build_rawhead($append_headers);
        
        $r = false; 
        if('php_curl'==$this->with_util){
            $r = $this->_request_by_php_curl($address, $method, $data); // php_curl 可以直接指定数组，可以用来发送文件
        }elseif('curl'==$this->with_util){
            $r = $this->_request_by_curl($address, $method, $post);
        }else{
            $r = $this->_request_by_socket($address, $method, $post);
        }
        if( $r ){
            if($this->follow) $this->_parse_cookie(); # 处理cookie，放入下次请求中
            if(preg_match('/Transfer-Encoding:\s+chunked/', $this->header)){
                $this->result = $this->_decode_chunked($this->result);
            }
            if(preg_match('/Content-Encoding:\s+(gzip|deflate)/', $this->header, $e_matched)){
                $this->result = $this->_deocode_compress($e_matched[1], $this->result);
            }
        }
        
        $this->_redirect_loaction();
        
        return $r;
    }
    
    private function _parse_cookie(){ // 处理服务器返回的cookie
        if(preg_match_all('/.*?\r\nSet-Cookie: (.*?);.*?/si', $this->header, $matches)) {
            foreach($matches[1] as $d){
                $ic = explode('=', $d, 2);
                if(!empty($ic[1]) && $ic[1]!=='deleted'){
                    $this->cookie[$ic[0]] = $ic[1]; 
                }
            }
        }
    }
    
    private function _parse_url($url) {
        $this->_url = $url;
        $p = array_merge(array('scheme' => '', 'host' => '', 'port' => 80, 'user' => '', 'pass' => '', 'path' =>'', 'query'=>'', 'fragment' => ''), parse_url($url));
        $p['port'] = (int) $p['port'];
        $this->_parsed = $p;
        $this->_parsed['url'] = ($p['scheme'] ? $p['scheme'] . '://' : '') . $p['user'] . ($p['pass'] ? ':' . $p['pass'] : '') . ($p['user'] || $p['pass'] ? '@' : '') .
                $p['host'] . $p['path'] . ($p['query'] ? '?' . $p['query'] : '') . ($p['fragment'] ? '#' . $p['fragment'] : '');
        $this->_parsed['uri'] = ($p['path'] ? $p['path'] : '/') . ($p['query'] ? '?' . $p['query'] : '');
    }
    
    private function _build_cookie() {
        $c = array();
        $all_cookie = is_array($this->cookie) ? array_merge($this->raw_cookies, $this->cookie) : $this->raw_cookies;
        if (!empty($all_cookie)) {
            foreach ($all_cookie as $k => $v) {
                $c[] = $k . '=' . $v;
            }
        }
        $this->_cookies = implode('; ', $c);
    }

    private function _build_rawhead($append=array()){
        $h = '';
        $ha = array();
        $headers = array_merge(is_array($this->raw_headers) ? $this->raw_headers : array(), is_array($append) ? $append : array());
        if (!empty($headers) && is_array($headers)) {
            foreach ($headers as $k => $v) {
                $h .= "$k: $v\r\n";
                $ha[] = "$k: $v";
            }
        }
        $this->_headers = 'socket' === $this->with_util ? $h : $ha;
    }
    
    private function _request_by_curl($url, $method, $post){
        if(!$this->curl_path || !is_executable($this->curl_path)){
            $this->error = 'There is not executable curl file!';
            return false;
        }
        $head  = array_merge(array("HOST: {$this->_parsed['host']}"), $this->_headers, array("Cookie: $this->_cookies"));
        $param = '';
        foreach($head as $v){
            $param .= ' -H "'.strtr($v, '"', ' ').'"';
        }
        $param .= " -m ".$this->timeout;
        if( $post ) {
            if( strlen($post)> 256 ){ // 超过 256个字节的post内容时，采用文件post，防止命令行字符串过长导致执行失败
                $body_file = tempnam($this->tmp_dir, 'kc_post_body');
                file_put_contents($body_file, $post);
                $param .= ' -d "@'.$body_file.'"';
            }else{
                $param .= ' -d "'.$post.'"';
            }
        }
        $header_file = tempnam($this->tmp_dir, 'kc_post_head');
        $safe_url    = str_replace(array(' ', '"'), array('%20', '%22'), $url);
        $cmd = $this->curl_path.' -s '.('https'===$this->_parsed['scheme'] ? ' -k':'').' -D "'.$header_file.'"'.$param.' "'.$safe_url.'"';
        exec($cmd, $result, $return);
        if ( $return ) {
            $this->error = "Error: cURL could not retrieve the document, error $return.";
            return false;
        }
        $this->result = implode("\r\n",$result);
        $this->header = file_get_contents("$header_file");
        unlink($header_file);
        if(!empty($body_file) && file_exists($body_file) ) unlink($body_file);
        return true;
    }
    
    private function _request_by_php_curl($url, $method, $post){
        if(!function_exists('curl_init')){
            $this->error = 'php_curl compenent is not enabled!';
            return false ;
        }
        $ch = curl_init($url);
        if(!empty($this->_headers)) 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
        if(!empty($this->_cookies)) 
            curl_setopt($ch, CURLOPT_COOKIE, $this->_cookies);
        if ( 'POST'===$method ) { // 是否为 POST请求
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL安全链接不执行检查
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout ) ; // 最大执行时间
        curl_setopt($ch, CURLOPT_HEADER, true);      // 返回头部信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回请求后的结果字符串
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            $this->error = "php_curl request error: ".curl_error($ch);
            return false;
        }
        curl_close($ch); // 关闭释放资源
        list($this->header, $this->result) = explode("\r\n\r\n", $result, 2);
        if(false!==strpos($this->header, " 100 Continue")){
            list($this->header, $this->result) = explode("\r\n\r\n", $this->result, 2);
        }
        /*
        $pt = strpos($result, "\r\n\r\n");
        
        $this->result = substr($result, $pt+4);
        $this->header = substr($result, 0 , $pt+4);
        */
        return true;
    }
    
    private function _request_by_socket($url, $method, $post) {
        if(!function_exists('fsockopen')){
            $this->error = 'fsockopen function could not been found!';
            return false;
        }
        $auth = !empty($this->_parsed['user']) ? base64_encode($this->_parsed['user'].':'.$this->_parsed['pass']) : '';
        $req  = '';
        $req .= "$method {$this->_parsed['uri']} HTTP/1.1\r\n";
        $req .= "Host: {$this->_parsed['host']}\r\n" ;
        $req .= $auth ? "Authorization: Basic ".($auth)."\r\n" : '';
        $req .= $this->_headers  ? $this->_headers : '';
        $req .= $this->_cookies  ? "Cookie: $this->_cookies\r\n" : '';

        if ( $post ) {
            $req .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $req .= "Content-Length: " . strlen($post) . "\r\n";
            $req .= "Connection: close\r\n\r\n";
            $req .= $post;
        } else {
            $req .= "Connection: close\r\n\r\n";
        }
        $host = $this->host_ip ? $this->host_ip : $this->_parsed['host'];
        $port = $this->_parsed['port'];
        if ('https' === $this->_parsed['scheme']) {
            $host = ($this->protocol ? $this->protocol : 'ssl').'://' . $host;
            $port = 443;
        }

        $fp = @fsockopen($host, $port, $errno, $errstr, $this->timeout);
        if( !$fp ) {
            $this->error = "fsockopen open failed: $errstr!";
            return false;
        }
        if($this->timeout){
            @stream_set_timeout($fp, 0, 1000*$this->timeout) ;
        }
        @fwrite($fp, $req, strlen($req));
        
        while (!feof($fp)) {
            $line = @fgets($fp);
            $this->header .= $line;
            if ("\r\n" === $line)  break;
        }
        while (!feof($fp)) {
            $this->result .= @fgets($fp);
        } 
        fclose($fp);
        return true;
    }

    // decode chunked, from zend framework
    private function _decode_chunked($body){
        $decBody = '';
        while (trim($body)) {
            if (! preg_match("/^([\da-fA-F]+)[^\r\n]*\r\n/sm", $body, $m)) {
                $this->error = "Error parsing body - doesn't seem to be a chunked message";
                return $body;
            }
            $length   = hexdec(trim($m[1]));
            $cut      = strlen($m[0]);
            $decBody .= substr($body, $cut, $length);
            $body     = substr($body, $cut + $length + 2);
        }
        return $decBody;
    }

    // uncompress , from zend framework
    private function _deocode_compress($type, $body){
        if(empty($body)) return '';
        if('gzip'==$type){
            return @gzinflate(substr($this->result, 10)); // 或者用@来屏蔽警告
        }else{ // deflate 
            $zlibHeader = unpack('n', substr($body, 0, 2));
            return 0 == $zlibHeader[1] % 31 ? @gzuncompress($body) : @gzinflate($body);
        }
    }
    
    private function _redirect_loaction(){
        $this->redirect = '';
        if(preg_match('/Location: (.+?)\r\n/', $this->header, $loc_match)){
            $this->redirect = $this->to_full_url($this->_url, $loc_match[1]);
        }
    }
    
    public function to_full_url($url, $path){
        if(empty($path)) return $url;
        if(preg_match('/^http:\/\//', $path)) return $path;
        $p = array_merge(array('scheme' => '', 'host' => '', 'port' => 80, 'user' => '', 'pass' => '', 'path' =>'', 'query'=>'', 'fragment' => ''), parse_url($url));
        $p['port'] = (int) $p['port'];
        $root = ($p['scheme'] ? $p['scheme'] . '://' : 'http://') . $p['user'] . ($p['pass'] ? ':' . $p['pass'] : '') . ($p['user'] || $p['pass'] ? '@' : '') .$p['host'] ;
        if(preg_match('/^\//', $path)) return $root.$path;
        if(preg_match('/^\.\/.+/', $path)){ // 是 ./ 开头
            return $root.dirname($p['path']).ltrim($path,'.') ;
        }elseif(preg_match('/^\.\.\//', $path)){
            $count = substr_count($path, '../');
            $i = preg_match('/\/$/', $p['path']) ? 1 : 0; $dir = dirname($p['path']);
            while($i++<$count){
                $dir = dirname($dir);
            }
            return $root . preg_replace('/\/+/', '/', $dir .'/'. preg_replace('/^(\.\.\/)+/', '',  $path));
        }else{
            $dp = dirname($p['path']);
            $dp = $dp !== '/' ? $dp.'/' : $dp;
            return $root . preg_replace('/\/+/', '/',(preg_match('/\/$/', $p['path']) ? $p['path'] : $dp)) . $path;
        }
    }

    function __destruct() {
        $this->error    = '';// 清空
        $this->result   = ''; 
        $this->header   = '';
    }
}

/* tesing */
/*
if(!empty($argv) && !empty($argv[0][0]) && ('/'==$argv[0][0] ? __FILE__ == $argv[0] : basename(__FILE__)==$argv[0])){
    $kr = new KC_Request('');
    $kr->get('http://www.baidu.com/');
    echo $kr->header;
    echo $kr->to_utf8();
}*/ 


?>
