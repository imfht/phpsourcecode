<?php
namespace wstmart\shop\controller;
use wstmart\common\model\Brands as M;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 品牌控制器
 */
class Brands extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
     * 获取品牌列表
     */
    public function listQuery(){
        $m = new M();
        $shopId = (int)session('WST_USER.shopId');
        return ['status'=>1,'list'=>$m->shopBrandListQuery($shopId,input('post.catId/d'))];
    }
}
