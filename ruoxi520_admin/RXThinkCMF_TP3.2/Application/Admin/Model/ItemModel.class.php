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
 * 站点-模型
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class ItemModel extends CBaseModel {
    function __construct() {
        parent::__construct('item');
    }
    
    //自动验证
    protected $_validate = array(
        array('name', 'require', '站点名称不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('name', '1,15', '站点名称长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('type', 'require', '请选择类型！', self::EXISTS_VALIDATE, '', 3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-16
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //站点图片
            if($info['image']) {
                $info['image_url'] = IMG_URL . $info['image'];
            }
            
            //站点类型
            if($info['type']) {
                $info['type_name'] = C("ITEM_TYPE")[$info['type']];
            }
            
        }
        return $info;
    }
    
}