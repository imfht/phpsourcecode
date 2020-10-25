<?php
/**
 * ipa下载安装
 * @Copyright (C) 2018 汉潮 All rights reserved.
 * @License http://www.hanchao9999.com
 * @Author xiaogg <xiaogg@sina.cn>
 */
class Ipainstall{
    private $cachepath='cache/';//缓存目录
    public function __construct($cachepath=''){
        if(!empty($cachepath))$this->cachepath=$cachepath;
    }
    //获取plist文件路径
    public function getplist($param){
        $xml=$this->loadplisttpl();
        $content=$this->replacecontent($xml,$param);//批量替换变量
        $name=$this->savefile($param['bundleid'],$content);//缓存
        $dir=$this->getdir();
        return $this->get_http().$_SERVER['HTTP_HOST'].$dir.'/'.$name; 
    }
    //返回当前文件所在路径
    private function getdir(){
        if(@IS_CGI) {//CGI/FASTCGI模式下                
            $_temp = explode('.php',$_SERVER['PHP_SELF']);
            $self= rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/');
        }else {
            $self=rtrim($_SERVER['SCRIPT_NAME'],'/');
        }
        $_root = rtrim(dirname($self),'/');
        return ($_root=='/' || $_root=='\\')?'':$_root;
    }    
    private function loadplisttpl(){//加载plist模板
        return "<?xml version='1.0' encoding='UTF-8'?><!DOCTYPE plist PUBLIC '-//Apple//DTD PLIST 1.0//EN' 'http://www.apple.com/DTDs/PropertyList-1.0.dtd'><plist version='1.0'><dict><key>items</key><array><dict><key>assets</key><array><dict><key>kind</key><string>software-package</string><key>url</key><string>[ipaurl]</string></dict><dict><key>kind</key><string>display-image</string><key>needs-shine</key><true/><key>url</key><string>[imgurl]</string></dict><dict><key>kind</key><string>full-size-image</string><key>needs-shine</key><true/><key>url</key><string>[imgurl]</string></dict></array><key>metadata</key><dict><key>bundle-identifier</key><string>[bundleid]</string><key>bundle-version</key><string>[version]</string><key>kind</key><string>software</string><key>subtitle</key><string>[title]</string><key>title</key><string>[title]</string></dict></dict></array></dict></plist>";
    }    
    private function get_http(){//获取http协议
    	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    }
    /**
     * 批量替换变量
     * @param $content 模板
     * @param $param 替换的变量及值数组
     */
    private function replacecontent($content,$param){
        $resplace=array('ipaurl','bundleid','imgurl','title','version');$rep=array();        
        foreach($resplace as $v){$rep['['.$v.']']=$param[$v];}
        return strtr($content,$rep);
    }
    /**
     * 缓存文件 每日清空一次
     * @param $key 缓存文件名
     * @param $value 缓存值
     */
    private function savefile($key,$value){
        if(empty($key) || empty($value))return false;
        if(is_array($key))$key=md5(json_encode($key));
        $filename=$this->cachepath.$key.'.plist';$this->clearcache();//清空过早缓存
        file_put_contents($filename,$value);
        return $filename;
    }
    private function clearcache(){//清空所有缓存
       $cachepath=$this->cachepath;$cachename='cachetime.cache';
       $cachetime=file_get_contents($cachepath.$cachename);
       $date=date('Y-m-d');
       if($cachetime==$date)return false;
       foreach(scandir($cachepath) as $fn) {
           if(in_array($fn,array(".","..",$cachename)))continue;
    	   unlink($cachepath.$fn);
       }file_put_contents($cachepath.$cachename,$date);
       return true;
    }
}
?>