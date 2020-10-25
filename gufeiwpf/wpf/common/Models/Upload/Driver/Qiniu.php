<?php
namespace Wpf\Common\Models\Upload\Driver;
require_once COMMON_PATH.'/Models/Upload/Driver/Qiniu/vendor/autoload.php';
class Qiniu extends \Phalcon\Mvc\Model{
    
    protected $_config = array(
    );
    
    public function onConstruct($config = null){
        
        $this->_config = array(
            'secrectKey'     => $this->getDI()->get("config")->PHOTO_QINIU_SK, //七牛SK
            'accessKey'      => $this->getDI()->get("config")->PHOTO_QINIU_AK, //七牛AK
            'domain'         => $this->getDI()->get("config")->PHOTO_QINIU_DOMAIN, //七牛域名
            'bucket'         => $this->getDI()->get("config")->PHOTO_QINIU_BUCKET, //空间名称
            'timeout'        => $this->getDI()->get("config")->PHOTO_QINIU_TIMEOUT, //超时时间
        );
        if(is_array($config)){
            $this->_config = array_merge($this->_config, $config);
        }
        
    }
    /**
     * 保存指定文件
     * Qiniu::save()
     * 
     * @param mixed $file
     * @param mixed $savefile
     * @return
     */
    public function savefile($file,$savefile) {
        
        if((! is_object($file)) || (! method_exists($file,"getTempName"))){
            $this->error = '原始文件错误';
            return false;
        }
        
        $key = str_replace(PUBLIC_PATH."/","",$savefile);
        
        $auth = new \Qiniu\Auth($this->_config['accessKey'], $this->_config['secrectKey']);
        $token = $auth->uploadToken($this->_config['bucket'],null,$this->_config['timeout']);
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        
        list($ret, $err) = $uploadMgr->put($token, $key, file_get_contents($file->getTempName()));
        
        if ($err !== null) {
            $this->error = $err->message();
        } else {
            return true;
        }
    }
    
    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }
}