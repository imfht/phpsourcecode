<?php
namespace app\page\service;
/**
 * 内容模型接口
 */
class ContentModelService{
    /**
     * 获取模型信息
     */
    public function getContentModel(){
        return array(
            'name'=>'页面',
            'listType'=>0,
            'order'=>0,
            );
    }
    


}
