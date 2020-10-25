<?php
/**
 * 七牛Model
 *
 * @package Model
 * @author chengxuan <i@chengxuan.li>
 */
namespace Model;
class Qiniu extends Abs {
    
    /**
     * Qiniu API对象
     * 
     * @var \Api\Qiniu
     */
    public $api;
    
    /**
     * Qiniu bucket
     * 
     * @var string
     */
    public $bucket;
    
    
    /**
     * 构造方法
     * 
     * @return void
     */
    protected function __construct() {
        $blog = Blog::show();
        if(empty($blog['data']['qiniu-ak']) || empty($blog['data']['qiniu-sk']) || empty($blog['data']['qiniu-domain']) || empty($blog['data']['qiniu-bucket'])) {
            throw new \Exception\Msg(_('尚未配置七牛CDN'));
        }
        $this->api = new \Api\Qiniu($blog['data']['qiniu-ak'], $blog['data']['qiniu-sk']);
        $this->bucket = $blog['data']['qiniu-bucket'];
    }
    
    /**
     * 获取一个操作对象
     * 
     * @return Qiniu
     */
    static public function init() {
        return new self();
    }
    

    /**
     * 上传一个文件到七牛
     * 
     * @param string $path        路径
     * @param string $source_path 源文件路径
     * 
     * @return stdClass
     */
    static public function upload($path, $source_path) {
        $obj = self::init();
        return $obj->api->upload($obj->bucket, $path, $source_path);
    }
    
    
}
