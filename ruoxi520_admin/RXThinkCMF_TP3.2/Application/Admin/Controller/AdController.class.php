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
 * 广告-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Controller;
use Admin\Model\AdModel;
use Admin\Model\AdSortModel;
use Admin\Service\AdService;
class AdController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new AdModel();
        $this->service = new AdService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::edit()
     */
    function edit() {
        //获取广告位
        $adSortMod = new AdSortModel();
        $sortList = $adSortMod->where(['status'=>1,'mark'=>1])->select();
        $this->assign('sortList',$sortList);
        
        parent::edit([
            't_type'=>1,
            'type'=>1,
        ]);
    }
    
}