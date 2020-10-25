<?php
namespace wstmart\home\controller;
use wstmart\home\model\GoodsVirtuals as M;
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
 * 虚拟商品卡券控制器
 */
class Goodsvirtuals extends Base{
    protected $beforeActionList = ['checkShopAuth'];
	/**
     * 查看虚拟商品库存
     */
    public function stock(){
    	$src = input('src','sale');
    	if(!in_array($src,['sale','audit','store','stockWarnByPage','illegal']))$src = 'sale';
    	$this->assign('src',$src);
        $this->assign('id',(int)input('id'));
        return $this->fetch("shops/goodsvirtuals/list");
    }
    /**
     * 获取虚拟商品库存列表
     */
    public function stockByPage(){
        $m = new M();
        $rs = $m->stockByPage();
        $rs['status'] = 1;
        return $rs;
    }
    /**
     *  跳去新增页
     */
    public function toAdd(){
        $this->assign('object',['cardNo'=>'','cardPwd'=>'','id'=>0]);
        return $this->fetch('shops/goodsvirtuals/edit');
    }
    /**
     *  跳去编辑页
     */
    public function toEdit(){
        $shopId = (int)session('WST_USER.shopId');
        $m = new M();
        $rs = $m->where([['id','=',(int)input('id')],['shopId','=',$shopId]])->find();
        $this->assign('object',$rs);
        return $this->fetch('shops/goodsvirtuals/edit');
    }
    /**
     * 生成卡券
     */
    public function add(){
    	$m = new M();
        $rs = $m->add();
        return $rs;
    }
    /**
     * 删除
     */
    public function del(){
    	$m = new M();
        $rs = $m->del();
        return $rs;
    }
    /**
     * 编辑
     */
    public function edit(){
    	$m = new M();
        $rs = $m->edit();
        return $rs;
    }
    /**
     * 导入卡券
     */
    public function importCards(){
        $rs = WSTUploadFile();
        if(json_decode($rs)->status==1){
            $m = new M();
            $rss = $m->importCards($rs);
            return $rss;
        }
        return $rs;
    }
}
