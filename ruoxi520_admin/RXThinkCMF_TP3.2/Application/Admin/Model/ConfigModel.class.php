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
 * 配置-模型
 * 
 * @author 牧羊人
 * @date 2018-09-22
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class ConfigModel extends CBaseModel {
    function __construct() {
        parent::__construct('config');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-09-22
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //类型名称
            $info['type_name'] = C('SYSTEM_CONFIG_TYPE')[$info['type']];
            
            //类型解析
            if($info['content']) {
                if($info['type']==4) {
                    //单图
                    $info['image_url'] = IMG_URL . $info['content'];
                }else if($info['type']==5) {
                    //图集
                    $imgsList =  unserialize($info['content']);
                    foreach ($imgsList as &$row) {
                        $row = IMG_URL . $row;
                    }
                    $info['imgsList'] = $imgsList;
                }
            }
            
            //分组名称
            if($info['group_id']) {
                $groupMod = new ConfigGroupModel();
                $groupInfo = $groupMod->getInfo($info['group_id']);
                $info['group_name'] = $groupInfo['name'];
            }
            
        }
        return $info;
    }
    
}