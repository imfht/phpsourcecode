<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use app\common\fun\Wxapp;

class Qrcode extends IndexBase
{
    /**
     * 生成普通二维码
     * @param string $url
     * @param string $logo
     */
    public function index($url = 'http://www.baidu.com',$logo=''){        
        $url = get_url($url);        
        if($logo=$this->get_file($logo)){
        }elseif($logo=$this->get_file($this->webdb['logo'])){
        }elseif($logo=$this->get_file($this->webdb['qrcode_logo'])){
        }elseif($logo=$this->get_file('static/index/default/logo.png')){
        }
        include_once (ROOT_PATH.'vendor/phpqrcode/phpqrcode.php');
    }
    
    /**
     * 小程序码
     * @param string $url
     * @param string $logo
     */
    public function wxapp($url = 'http://www.baidu.com',$logo=''){
        if (is_numeric($url)) {
            $url = Wxapp::qun_code($url, $logo);
        }else{
            $url = Wxapp::wxapp_codeimg($url,$this->user['uid'], $logo);
        }        
        header("location:".$url);
        exit;
    }
    
    /**
     * 获取LOGO的物理地址
     * @param string $file
     * @return string|mixed
     */
    private function get_file($file=''){
        $logo = '';
        if($file!=''){
            if (strstr($file,$this->request->domain())) {
                $path = str_replace($this->request->domain().'/', ROOT_PATH, $file);
                if(is_file($path)){
                    $logo = $path;
                }
            }else{
                if(preg_match("/^http/", $file)){
                    if (!is_dir(UPLOAD_PATH.'temp')) {
                        mkdir(UPLOAD_PATH.'temp');
                    }
                    $name = preg_match("/\.(jpg|jpeg|png|gif)$/i", $file)?basename($file):(md5($file).'.jpg');
                    $path = UPLOAD_PATH.'temp/'.$name;
                    if (is_file($path)) {
                        $logo = $path;
                    }elseif($string = file_get_contents($file)?:http_curl($file)){
                        $logo = $path;
                        file_put_contents(UPLOAD_PATH.'temp/'.$name, $string);                        
                    }
                }else{
                    if (is_file(ROOT_PATH.$file)) {
                        $logo = ROOT_PATH.$file;
                    }elseif(is_file(PUBLIC_PATH.$file)){
                        $logo = PUBLIC_PATH.$file;
                    }
                }                
            }
        }
        return $logo;
    }
}

