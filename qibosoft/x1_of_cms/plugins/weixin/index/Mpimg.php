<?php
namespace plugins\weixin\index;

use app\common\controller\IndexBase;

//采集公众号文章图片
class Mpimg extends IndexBase
{
    public function get($url='',$fid=0,$id=0){
        if (empty($url)) {
            return $this->error('图片地址不存在');
        }
        $fid = intval($fid);
        $id = intval($id);
        $name = login_user('uid')."_".substr(md5($url),0,10).'.gif';
        $imgpath = '/mpimg/'.substr(md5($url),0,2).'/';
        $path = config('upload_path').$imgpath;
        makepath($path);
        if(!is_file($path.$name)){
            $Referer = 'http://mp.weixin.qq.com/s?__biz=MzA5ODIyMTAyMg==&mid=211341616&idx=3&sn=f21c1cba5965c4c5a05aefdfac0b1655&scene=18#wechat_redirect';
            $filestr = $this->sock_open($url,'GET','',$Referer);
            if(strlen($filestr)>100){
                file_put_contents( $path.$name ,$filestr);
                self::oss('uploads'.$imgpath.$name);
            }
        }
        if(!is_file($path.$name)){
            return $this->error('图片不存在');
        }
        $file = read_file($path.$name);
        if (preg_match('/^http/', $file)) {
            $url = $file;
        }else{
            $url = PUBLIC_URL.'uploads'.$imgpath.$name;
        }
        header('location:'.$url);
        exit;
    }
    
    private static function oss($path='',$module='cms'){
        $from='wangeditor';
        $file_info = [
            'path'     => $path,
            'url'      => PUBLIC_URL . $path,
            'name'     => basename($path),
            'tmp_name' => PUBLIC_PATH . $path,
            'size'     => '0',
            'type'     => 'image/gif',
        ];
        if ( config( 'webdb.upload_driver' ) && config( 'webdb.upload_driver' ) != 'local' ) {
            $hook_result = \think\Hook::listen( 'upload_driver',$file_info,[
                'from'   => $from,
                'module' => $module,
                'type'   => 'gather',
            ],true );
            if ( false !== $hook_result && strstr($hook_result,'http')) {
                write_file( PUBLIC_PATH . $path , $hook_result);
                return $hook_result;
            }
        }
        return $path;
    }
    
    protected function sock_open($url,$method='GET',$postValue='',$Referer='Y'){
        if($Referer=='Y'){
            $Referer=$url;
        }
        $method = strtoupper($method);
        if(!$url){
            return ;
        }elseif(!strstr($url,'://')){
            $url="http://$url";
        }
        $urldb=parse_url($url);
        $port=$urldb['port']?$urldb['port']:80;
        $host=$urldb['host'];
        $query='?'.$urldb['query'];
        $path=$urldb['path']?$urldb['path']:'/';
        $method=$method=='GET'?"GET":'POST';
        
        if(function_exists('fsockopen')){
            $fp = fsockopen($host, $port, $errno, $errstr, 30);
        }elseif(function_exists('pfsockopen')){
            $fp = pfsockopen($host, $port, $errno, $errstr, 30);
        }elseif(function_exists('stream_socket_client')){
            $fp = stream_socket_client($host.':'.$port, $errno, $errstr, 30);
        }else{
            die("服务器不支持以下函数:fsockopen,pfsockopen,stream_socket_client操作失败!");
        }
        if(!$fp)
        {
            echo "$errstr ($errno)<br />\n";
        }
        else
        {
            $out = "$method $path$query HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Cookie: c=1;c2=2\r\n";
            $out .= "Referer: $Referer\r\n";
            $out .= "Accept: */*\r\n";
            $out .= "Connection: Close\r\n";
            if ( $method == "POST" ) {
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $length = strlen($postValue);
                $out .= "Content-Length: $length\r\n";
                $out .= "\r\n";
                $out .= $postValue;
            }else{
                $out .= "\r\n";
            }
            fwrite($fp, $out);
            while (!feof($fp)) {
                $file.= fgets($fp, 256);
            }
            fclose($fp);
            if(!$file){
                return ;
            }
            $ck=0;
            $string='';
            $detail=explode("\r\n",$file);
            foreach( $detail AS $key=>$value){
                if($value==''){
                    $ck++;
                    if($ck==1){
                        continue;
                    }
                }
                if($ck){
                    $stringdb[]=$value;
                }
            }
            $string=implode("\r\n",$stringdb);
            //$string=preg_replace("/([\d]+)(.*)0/is","\\2",$string);
            return $string;
        }
    }
}