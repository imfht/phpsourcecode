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
 * @date 2018-12-13
 */
namespace app\admin\controller;
use app\admin\model\AdModel;
use app\admin\service\AdService;
class AdController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new AdModel();
        $this->service = new AdService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::edit()
     */
    function edit()
    {
        //获取广告位
        $sortList = db('adSort')->where(['mark'=>1])->select();
        $this->assign('sortList',$sortList);
        
        return parent::edit([
            't_type'=>1,
            'type'=>1,
        ]);
    }
    
}