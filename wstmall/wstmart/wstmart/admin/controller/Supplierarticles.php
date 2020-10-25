<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Articles as M;
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
 * 文章控制器
 */
class Supplierarticles extends Base{
	
   
    /**
     * 获取文章
     */
    public function get(){
    	$m = new M();
    	$rs = $m->get(Input("post.id/d",0));
    	return $rs;
    }
    
    /**
     * 设置是否显示/隐藏
     */
    public function editiIsShow(){
    	$m = new M();
    	$rs = $m->editiIsShow();
    	return $rs;
    }
    
    /**
     * 修改排序
     */
    public function changeSort(){
    	$m = new M();
    	return $m->changeSort();
    }

    /**
     * 店铺帮助
     */
    public function guide(){
        $m = new M();
        $object = $m->getById(404);
        $this->assign("p",(int)input("p"));
        $this->assign("object",$object);
        return $this->fetch("guide");
    }
    /**
     * 获取帮助列表
     */
    public function pageQueryByGuide(){
        $m = new M();
        $rs = $m->pageQueryByOther(401,[404]);
        return WSTGrid($rs);
    }
    /**
     * 进入编辑
     */
    public function toEditGuide(){
        $id = Input("get.id/d",0);
        $m = new M();
        if($id>0){
            $object = $m->getById($id);
        }else{
            $object = $m->getEModel('articles');
        }
        $this->assign('object',$object);
        $this->assign("p",(int)input("p"));
        return $this->fetch("edit_guide");
    }
    /**
     * 新增
     */
    public function addGuide(){
        $m = new M();
        $rs = $m->addOther(401,'add');
        return $rs;
    }
    
    
    /**
     * 编辑
     */
    public function editGuide(){
        $m = new M();
        $rs = $m->editOther(401,'edit');
        return $rs;
    }
    
    /**
     * 删除
     */
    public function delGuide(){
        $m = new M();
        $rs = $m->delOther(401);
        return $rs;
    }

    /**
     * 批量删除
     */
    public function delByBatchGuide(){
        $m = new M();
        $rs = $m->delByBatchOther(401);
        return $rs;
    }
    /**
     * 保存入驻协议
     */
    public function editAgreement(){
        $m = new M();
        $rs = $m->editAgreement(404,401);
        return $rs;
    }

}
