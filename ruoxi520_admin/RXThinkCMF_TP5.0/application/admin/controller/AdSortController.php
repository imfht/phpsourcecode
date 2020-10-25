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
 * 广告位-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\controller;
use app\admin\model\AdSortModel;
use app\admin\service\AdSortService;
use app\admin\model\AdModel;
class AdSortController extends AdminBaseController
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
        $this->model = new AdSortModel();
        $this->service = new AdSortService();
    }
    
    /**
     * 删除数据
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::drop()
     */
    function drop()
    {
        if(IS_POST) {
            $id = input('post.id');
            $adMod = new AdModel();
            $count = $adMod->where(["ad_sort_id"=>$id,'mark'=>1])->count();
            if($count>0) {
                return message("当前广告位已经在使用,无法删除",false);
            }
            return parent::drop();
        }
    }
    
}