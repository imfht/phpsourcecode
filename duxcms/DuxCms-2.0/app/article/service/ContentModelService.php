<?php
namespace app\article\service;
/**
 * 内容模型接口
 */
class ContentModelService{
    /**
     * 获取模型信息
     */
    public function getContentModel(){
        return array(
            'name'=>'文章',
            'listType'=>1,
            'order'=>0,
            );
    }
    


}
