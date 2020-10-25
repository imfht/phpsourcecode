<?php

/**
 * 七牛CDNAPI
 *
 * @package Api
 * @author chengxuan <i@chengxuan.li>
 */
namespace Api;

class Qiniu {

    /**
     * AK
     * 
     * @var string
     */
    protected $_ak = '';
    
    /**
     * SK
     * 
     * @var string
     */
    protected $_sk = '';
            
    /**
     * 构造方法
     * 
     * @param string $ak
     * @param string $sk
     * 
     * @return void
     */
    public function __construct($ak, $sk) {
        $this->_ak = $ak;
        $this->_sk = $sk;
    }
    
    /**
     * 上传一个文件到七牛
     * 
     * @param string $bucket      存储内容
     * @param string $path        路径
     * @param string $source_path 源文件路径
     * 
     * @return stdClass
     */
    public function upload($bucket, $path, $source_path) {
        $put_policy = array(
            'scope' => "{$bucket}:{$path}",
            'deadline' => time() + 3600,
        );
        $data = array(
            'token' => $this->showToken($put_policy),
            'file'  => \Comm\Request\Single::file($source_path),
            'key'   => "{$path}",
        );
        
        $request = new \Comm\Request\Single('http://upload.qiniu.com');
        $request->setPostData($data, false);
        $result = $request->exec();
        
        $result = $result ? json_decode($result) : new \stdClass();
        
        return $result;
    }
    
    /**
     * 获取Token
     * 
     * @param array $put_policy 要传递的数据
     * 
     * @return string
     */
    public function showToken(array $put_policy) {
        $put_policy_encoded = \Comm\Base64::urlEncode(\Comm\Json::encode($put_policy));
        $sign = hash_hmac('sha1', $put_policy_encoded, $this->_sk, true);
        $sign = \Comm\Base64::urlEncode($sign);
        $result = "{$this->_ak}:{$sign}:{$put_policy_encoded}";
        return $result;
    }
    
}
