<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 文件管理-分层控制器
 */
namespace app\common\facade\library;
use app\common\model\SystemApis;
use app\common\facade\WechatMp;
use app\common\facade\WechatProgram;
use think\facade\Env;
use think\facade\Config;
use Yurun\Util\HttpRequest;
use util\Util;
use upload\Upload as upDriver;

class Upload {

    protected $rootpath;
    protected $config;

    public function __construct() {
        $this->config  = config::get('upload.');
        $config = SystemApis::Config('alioss');
        if($config){
            $this->config['upload_driver']   = $config['upload_driver'];
            $this->config['upload_exts']     = $config['upload_exts'];
            $this->config['upload_relative'] = $config['domain'];
            $this->config['upload_driver_config']['oss'] =  [
                "driver"     => 'oss',
                "city"       => $config['city'],
                "access_id"  => $config['access_id'],
                "secret_key" => $config['secret_key'],
                "bucket"     => $config['bucket'],
                "isInternal" => $config['is_internal'],
                "domain"     => $config['domain']
            ];
        }
    }

    /**
     * 文件上传
     */    
    public function index($dir_path = ''){
        $uploadDriver = $this->config['upload_driver_config'];
        $upConfig = array(
            'maxSize'      => intval($this->config['upload_size']) * 1024 * 1024,
            'allowExts'    => explode(',',$this->config['upload_exts']),
            'rootPath'     => PATH_RES,
            'savePath'     => $dir_path ? $dir_path.DS.$this->config['upload_path']: $this->config['upload_path'],
            'saveRule'     => 'md5_file',
            'driver'       => $this->config['upload_driver'],
            'driverConfig' => $uploadDriver[$this->config['upload_driver']],
        );
        //上传
        $upload = new upDriver($upConfig);
        if (!$upload->upload()) {
            return ['error'=>1,'message' => $upload->getError()];
        }
        //上传信息
        $list = $upload->getUploadFileInfo();
        if (empty($list)) {
            return ['error'=>1,'message'=>'上传文件不存在'];
        }
        if($this->config['upload_driver'] == 'oss'){
            foreach ($list as $info) {
                $filepath = $info['url'];
            }
        }else{
            foreach ($list as $info) {
                $imgpath = $info['savepath'].$info['savename'];
                $filepath = str_replace('\\','/',substr($imgpath,strlen(PATH_PUBLIC)));
                $filepath = empty($this->config['upload_relative']) ? '/'.$filepath : $this->config['upload_relative'].$filepath;
            }
        }
        return ['error'=>0,'message'=>'成功','url' => $filepath];
    }

    /**
     * 文件证书文件cart到runtime目录（外网没办法直接访问）
     */    
    public function cert($dir_path = 0){
        $uploadDriver = $this->config['upload_driver_config'];
        $upConfig = array(
            'maxSize'      => intval($this->config['upload_size']) * 1024 * 1024,
            'allowExts'    => ['pem'],
            'rootPath'     => Env::get('runtime_path'),
            'savePath'     => 'cert'.DS.$dir_path.DS,
            'saveRule'     => 'md5_file',
            'driver'       => 'local',
            'driverConfig' => $uploadDriver['local'],
        );
        //上传
        $upload = new upDriver($upConfig);
        if (!$upload->upload()) {
            return ['error'=>1,'message'=>$upload->getError()];
        }
        //上传信息
        $rel = $upload->getUploadFileInfo();
        if (empty($rel)) {
            return ['error'=>1,'message'=>'上传文件不存在'];
        }
        return ['error'=>0,'message'=>'成功','url' => $rel['file']['savename']];
    } 

    /**
     * 上传资源到微信返回media_id 
     * @param int     $weapp_id //应用ID
     * @param array   $img      //图片地址,数组或字符串
     * @return array
     */
    public function uploadWechatMp($weapp_id,$img){
        $this->wechat = WechatMp::isTypes($weapp_id);
        if(!$this->wechat){
            return [];
        }
        return  self::uploadWechat($img,true);
    }
    /**
     * 上传资源到微信返回media_id 
     * @param int     $weapp_id //应用ID
     * @param array   $img      //图片地址,数组或字符串
     * @return array
     */
    public function uploadWechatWeapp($weapp_id,$img){
        $this->wechat = WechatProgram::isTypes($weapp_id);
        if(!$this->wechat){
            return [];
        }
        return  self::uploadWechat($img,false);
    }

    /**
     * 上传资源到微信返回media_id 
     * @param array   $img      //图片地址,数组或字符串
     */
    private function uploadWechat($img,$is_mp){
        $imgs = is_array($img) ? $img : [$img];
        $data = [];
        if($this->config['upload_driver'] == 'oss'){
            foreach ($imgs as $key => $value) {
                $thumb_img = substr(parse_url($value)['path'],1);
                $thumb_path = PATH_PUBLIC.$thumb_img;
                if (!file_exists($thumb_path)) {
                    if(Util::mkdir(dirname($thumb_path))){
                        $http = new HttpRequest;
                        $http->download($thumb_path,$value);
                    }
                }
                if(file_exists($thumb_path)){
                    $thumb = $is_mp ? $this->wechat->material->uploadThumb($thumb_path) : $this->wechat->media->uploadImage($thumb_path);
                    if(!empty($thumb['media_id'])){
                        $data[$key]['media_id'] = $thumb['media_id'];
                        $data[$key]['media']  = json_encode($thumb);
                    }
                }
            }
        }else{
            foreach ($imgs as $key => $value) {
                $thumb_img = substr(parse_url($value)['path'],1);
                $thumb_path = PATH_PUBLIC.$thumb_img;
                if(file_exists($thumb_path)){
                    $thumb = $this->wechat->material->uploadThumb($thumb_path);
                    if(!empty($thumb['media_id'])){
                        $data[$key]['media_id'] = $thumb['media_id'];
                        $data[$key]['media']  = json_encode($thumb);
                    }
                }
            }
        }
        return $data;
    }


    /**
     * 读取目录文件
     * @param  [string] $path     [默认访问目录]
     * @param  [string] $is_tpl   [是否模板目录]
     * @return [string] $dir_path [模仿根目录]
     */
    public function directoryResource($path = null,$is_tpl = false,$dir_path = ''){
        $folder    = $is_tpl ? PATH_THEMES: PATH_RES;
        $root_path = $dir_path ? $folder.$dir_path.DS : $folder;
        $folder_path = self::accessPath($path,$root_path);
        if(!self::isDir($folder_path['nowpath'])) return;   //没有权限直接返回
        $file_list['backpath'] = [];
        $file_list['folder']   = [];
        $file_list['file']     = [];
        if(isset($folder_path['backpath'])){
            $file_list['backpath'][0]['name'] = '返回上级';
            $file_list['backpath'][0]['path'] = $folder_path['backpath'];
        }
        $filepath = str_replace('\\','/',substr($folder_path['nowpath'],strlen(PATH_PUBLIC)));
        $imgurl   = $filepath ? $filepath:'/';
        $file_info = scandir($folder_path['nowpath']);
        $relative  = empty($this->config['upload_relative']) ? '/' : $this->config['upload_relative'];
        foreach ($file_info as $key => $value) {
            if(is_dir($folder_path['nowpath'].'/'.$value)){
                if ($value != "." && $value != ".."){
                    $file_list['folder'][] = ['name' => $value,'path' =>$path.'/'.$value]; 
                }
            }else{

                $file_list['file'][] = [$relative.$imgurl.$value,$value];
            }               
        }
        krsort($file_list['file']);
        krsort($file_list['folder']);
        return $file_list;
    }   

    /**
     * 处理访问路径参数
     * @param  [type]  $path [当前访问路径]
     * @return array         [上一级目录或当前目录]
     */
    private function accessPath($path = null,$root_path = PATH_RES){
        if($path){
            $path    = str_replace('\\','/',$path);
            $path    = str_replace('../','',$path);
            $path    = str_replace('..','',$path);
            $newpath = realpath($root_path.$path);
            if(is_dir($newpath)){
                $len        = strlen($root_path);
                $back_path  = substr($newpath,$len);
                $folder_ary = $back_path ? explode('\\',$back_path) : [];
                array_pop($folder_ary);
                $folder_path['backpath'] = implode('/',$folder_ary);
                $folder_path['nowpath'] = $newpath.DS;
            }else{
                $folder_path['nowpath'] = $root_path;
            }
            return $folder_path;
        }
        $folder_path['nowpath'] = $root_path;
        return $folder_path;
    }

    /**
     * 检测目录权限
     * @param  [type] $path [当前判断目录]
     * @return [boolean]    [是否存在或有权限]
     */
    private function isDir($path) {
        if(!(is_dir($path) && is_writable($path))){
            $this->errorMsg = '上传根目录不存在！';
            return false;
        }
        return true;
    }  
}