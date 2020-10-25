<?php
/**
 * Created by PhpStorm.
 * User: lxl
 * Date: 16-10-13
 * Time: 上午9:07
 */
namespace Mohuishou\Lib;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Attachment{

    /**
     * 加载config对象
     * @var Config
     */
    protected $_config;

    /**
     * 七牛SDK当中的上传类的对象
     * @var
     */
    protected $_uploadMgr;

    /**
     * 七牛的token
     * @var string
     */
    protected $_token;

    /**
     * Attachment constructor.
     */
    public function __construct()
    {
        $this->_config=Config::getInstance();
    }

    /**
     * 保存文件，主要方法
     * @param string $filename 原博客文章标题
     * @param string $content 原博客文章内容
     * @return mixed
     */
    public function save($filename,$content){
        //匹配链接地址，匹配两种格式
        //格式一：![](http://)
        $pattern="/!\[.*\]\(([a-zA-z]+:\/\/[^\s]*\/([^\s]*\.[^\s]*)).*\)/";
        preg_match_all($pattern,$content,$res);
        //格式二：[]: http://
        $pattern2="/\[\d\]:\s([a-zA-z]+:\/\/[^\s]*\/([^\s]*\.[^\s]*))/";
        preg_match_all($pattern2,$content,$res2);
        //将匹配到的链接整合到数组
        $res_all[0]=array_merge($res[1],$res2[1]);
        $res_all[1]=array_merge($res[2],$res2[2]);
        //没有附件或者是图片直接返回
        if(empty($res_all[0][0])) return $content;
        //文件保存方式
        $type=$this->_config->get("attachment")["type"];
        if($type=="qiniu"){
            $this->initQiniu();
            $content=$this->saveQiniu($res_all,$content);
        }else{
            $content=$this->saveFile($res_all,$content,$filename);
        }
        return $content;

    }

    /**
     * 下载文件，保存到本地
     * @param array $res 链接数组
     * @param string $content 原博客文章内容
     * @param string $filename 原博客文章标题
     * @return string $content
     */
    protected function saveFile($res,$content,$filename){
        foreach ($res[0] as $key=> $value){
            //下载图片
            $this->download($value,$res[1][$key],__DIR__."/../FILE/".$filename);
            //替换图片链接
            $content=str_replace($value,$res[1][$key],$content);
        }
        return $content;
    }

    /**
     * 下载文件保存到七牛
     * @param array $res 链接数组
     * @param string $content 原博客文章内容
     * @return mixed
     */
    protected function saveQiniu($res,$content){
        $domain=$this->_config->get("qiniu")["domain"];
        foreach ($res[0] as $key=> $value){
            //下载图片
            $path=$this->download($value,$res[1][$key],__DIR__."/../FILE/tmp");
            $key="blog/old/".$res[1][$key];
            list($ret, $err) = $this->_uploadMgr->putFile($this->_token, $key, $path);
            if($err!==null){
                continue;
            }
            $link=$domain."/".$ret['key'];
            //替换图片链接
            $content=str_replace($value,$link,$content);
        }
        return $content;
    }

    /**
     * 七牛初始化
     */
    protected function initQiniu(){
        //加载七牛配置文件，并检查是否为空
        $qiniu=$this->_config->get("qiniu");
        foreach ($qiniu as $v){
            if(empty($v)){
                throw new \Exception("Error: 七牛配置文件错误，请检查config.php!");
            }
        }

        $auth=new Auth($qiniu['access_key'],$qiniu["secret_key"]);
        $this->_token=$auth->uploadToken($qiniu["bucket_name"]);
        // 初始化 UploadManager 对象并进行文件的上传
        $this->_uploadMgr = new UploadManager();
    }

    /**
     * 下载文件
     * @param string $url 下载链接
     * @param string $filename 文件名
     * @param string $dir 文件保存目录
     * @return string $path 文件保存的路径
     */
    protected function download($url,$filename,$dir){
        $path=$dir."/".$filename;
        $ch=curl_init();
        $timeout=60; //文件最长下载时间
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $res=curl_exec($ch);
        curl_close($ch);
        //检查文件夹是否存在
        if(!file_exists($dir)) mkdir($dir);
        file_put_contents($path,$res);
        return $path;
    }
}