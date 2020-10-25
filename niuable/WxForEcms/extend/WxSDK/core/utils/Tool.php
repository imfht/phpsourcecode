<?php
namespace WxSDK\core\utils;

use WxSDK\core\common\Ret;

class Tool{
    /**
     * doCurl
     * 执行 curl 的方法
     * @param String $url 链接
     * @param array|string $postfields 表单数据
     * @param bool $media 是否含对媒体文件,默认不含
     * @param bool $filterNull 是否过滤$postfields中值为null的属性
     * @return Ret 对网络返回数据（字符串），按微信返回规则解析后的结果
     */
    static function doCurl(string $url, $postfields = NULL, $media = FALSE, $filterNull = TRUE) {
        $ch = curl_init ();
        if (class_exists ( '/CURLFile' )) { //php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
            curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, true );
        } else {
            if (defined ( 'CURLOPT_SAFE_UPLOAD' )) {
                curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, TRUE);
            }
        }
        $is_https = true;
        $url = trim ( $url );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        if (stripos ( $url, "https" ) !== 0) {
            $is_https = false;
        }
        
        if ($is_https) {
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
            curl_setopt ( $ch, CURLOPT_SSLVERSION, 1 );
        }
        if (!empty ( $postfields )) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if($postfields instanceof string){
                $json = $postfields;
                if($filterNull){
                    $json = self::removeNullInJson($json);
                }
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $json);
            }elseif ($media){
                curl_setopt ( $ch, CURLOPT_HEADER, false );
                curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true );
                
                if(is_array($postfields)){
                    if($filterNull){
                        $post = [];
                        foreach ($postfields as $k => $v){
                            if($v != NULL){
                                $post[$k] = $v;
                            }
                        }
                    }else{
                        $post = $postfields;
                    }
                    $data = $post;
                    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data);
                }else{
                    return new Ret('',NULL,500,'文件表单必须是数组');
                }
            }else{
                $json = json_encode($postfields);
                if($filterNull){
                    $json = self::removeNullInJson($json);                
                }

                curl_setopt ( $ch, CURLOPT_POSTFIELDS, urldecode($json));
            }
        }
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        $result = curl_exec ( $ch );
        if (curl_errno ( $ch )) {
            $result = curl_error ( $ch );
        }
        curl_close ( $ch );
        return new Ret($result);
    }
    static function download(String $url, $downloadPathName){        
        $ch = curl_init();
        $fp =  fopen($downloadPathName, 'w+');
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $url = trim ( $url );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        
        $is_https = true;
        if (stripos ( $url, "https" ) !== 0) {
            $is_https = false;
        }
        if ($is_https) {
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
            curl_setopt ( $ch, CURLOPT_SSLVERSION, 1 );
        }else{
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, true );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);

        $res = curl_exec ($ch);
        fclose($fp);
        curl_close ($ch);
        return $res;
    }
    
    public static function createMediaData(String $filename,string $key = null){
        $key = $key ? $key : "media";
        if (class_exists ( '\CURLFile' )) {//关键是判断curlfile,官网推荐php5.5或更高的版本使用curlfile来实例文件
            $mediaData= array (
                $key => new \CURLFile ( realpath($filename))
            );
        } else {
            $mediaData= array (
                $key => '@' . realpath($filename)
            );
        }
        return $mediaData;
    }
    
    public static function createMedia4VideoData(String $filename, string $title, string $introduction)
    {
        $a = array(
            'title' => $title,
            'introduction' => $introduction
        );
        $a = json_encode($a, JSON_UNESCAPED_UNICODE);
        if (class_exists('\CURLFile')) {//关键是判断curlfile,官网推荐php5.5或更高的版本使用curlfile来实例文件
            $mediaData = array(
                'media' => new \CURLFile (realpath($filename)),
                'description' => $a
            );
        } else {
            $mediaData = array(
                'media' => '@' . realpath($filename),
                'description' => $a
            );
        }
        return $mediaData;
    }
    
    /**
     * 删除json格式中属性值为null的所有属性
     * @param string $json
     * @return string
     */
    public static function removeNullInJson(string $json) {
        if(NULL == $json){
            return "";
        }
        return preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', $json);
    }
    
    //unicode转中文
    public static function unicodeDecode($unicode_str){
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }
}