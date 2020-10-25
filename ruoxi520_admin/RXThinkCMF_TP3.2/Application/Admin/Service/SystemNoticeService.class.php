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
 *通知公告-服务类
 *
 * @author 牧羊人
 * @date 2018-12-04
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\SystemNoticeModel;
class SystemNoticeService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new SystemNoticeModel();
    }

    /**
     * 获取数据列表
     *
     * @author 牧羊人
     * @date 2018-12-04
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::getList()
     */
    function getList() {
        $param = I("request.");

        $map = [];
        //查询条件
        $keywords = trim($param['keywords']);
        if($keywords) {
            $map['title'] = array('like',"%{$keywords}%");
        }
        return parent::getList($map);
    }

    /**
     * 添加或编辑
     *
     * @author 牧羊人
     * @date 2018-12-04
     */
    function edit() {
        $data = I('post.', '', 'trim');
        //内容处理
        //\Zeus::saveImageByContent($data['content'],$data['title'],"systemNotice");

        return parent::edit($data);

    }

}