<?php
namespace Naka507\Socket;
class Response
{
    public $buffer;
    public $connection;
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->buffer = '';
    }

    public function write($content){
        // Default http-code.
        if (!isset(Http::$header['Http-Code'])) {
            $header = "HTTP/1.1 200 OK\r\n";
        } else {
            $header = Http::$header['Http-Code'] . "\r\n";
            unset(Http::$header['Http-Code']);
        }

        // Content-Type
        if (!isset(Http::$header['Content-Type'])) {
            $header .= "Content-Type: text/html;charset=utf-8\r\n";
        }

        // other headers
        foreach (Http::$header as $key => $item) {
            if ('Set-Cookie' === $key && is_array($item)) {
                foreach ($item as $it) {
                    $header .= $it . "\r\n";
                }
            } else {
                $header .= $item . "\r\n";
            }
        }
        if(Http::$gzip && isset($connection->gzip) && $connection->gzip){
            $header .= "Content-Encoding: gzip\r\n";
            $content = gzencode($content,$connection->gzip);
        }
        // header
        $header .= "Server: HTTP Server \r\nContent-Length: " . strlen($content) . "\r\n\r\n";

        // save session
        Http::sessionWriteClose();

        // the whole http package
        $this->buffer =  $header . $content;
    }

    public function header($name,$value){
        Http::$header[$name] = "$name : $value" ;
    }   

    public function status($code){
        Http::$header['Http-Code'] = "HTTP/1.1 $code " . Http::$codes[$code];
    }

    public function setCookie($name, $value = '', $maxage = 0, $path = '/', $domain = '', $secure = false, $HTTPOnly = false){
        return Http::setcookie($name, $value, $maxage, $path, $domain, $secure, $HTTPOnly);
    }

    public function getCookie($name){
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    public function clearCookie(){
        foreach ( $_COOKIE as $key => $value) {
            if ( $key == "PHPSESSID") {
                continue;
            }
            Http::setcookie($key, '', -1 ,'/');
        }
    }

    public function setSession($name,$value){
        $_SESSION[$name] = $value;
    }

    public function getSession($name){
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    public function clearSession(){
        $_SESSION = [];
        file_put_contents(Http::$instance->sessionFile, '');
    }

    public function end(){
        $this->connection->send($this->buffer);
    }

}