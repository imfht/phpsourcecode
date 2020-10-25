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
 * 通知公告-模型
 * 
 * @author 牧羊人
 * @date 2018-12-03
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class SystemNoticeModel extends CBaseModel {
    function __construct() {
        parent::__construct('system_notice');
    }

    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-03
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //获取栏目
            if($info['cate_id']) {
                $cateMod = M("itemCate");
                $cateInfo = $cateMod->getInfo($info['cate_id']);
                $info['cate_name'] = $cateInfo['name'];
            }
            
            //封面
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }

            
            //图集
            if($info['imgs']) {
                $imgsList =  unserialize($info['imgs']);
                foreach ($imgsList as &$row) {
                    $row = IMG_URL . $row;
                }
                $info['imgsList'] = $imgsList;
            }

        }
        return $info;
    }
    
}