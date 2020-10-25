<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 标签-模型
 * 
 * @author 牧羊人
 * @date 2018-10-19
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class TagsModel extends CBaseModel {
    function __construct() {
        parent::__construct('tags');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-19
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //类型
            if($info['type']) {
                $info['type_name'] = C('TAGS_TYPE')[$info['type']];
            }
            
        }
        return $info;
    }
    
}