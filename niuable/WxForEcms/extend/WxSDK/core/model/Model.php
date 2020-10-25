<?php
namespace WxSDK\core\model;

class Model{
    private $postData;
    private $hasMedia = FALSE;
    /**
     * 
     * @param array|string $postData
     * @param bool $hasMedia
     */
    function __construct($postData = [], bool $hasMedia = FALSE){
        $this->postData = $postData;
        $this->hasMedia = $hasMedia;
    }
    public function hasMedia() {
        return $this->hasMedia;
    }
    /**
     * 提供request作为post参数
     * @return array | Model 结果
     */
    public function getPostData(){
        if($this->postData && !empty($this->postData)){
            return $this->postData;
        }
        return $this;
    }
}