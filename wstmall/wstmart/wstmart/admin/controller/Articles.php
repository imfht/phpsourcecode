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
class Articles extends Base{
	
    public function index(){
        $this->assign("p",(int)input("p"));
    	return $this->fetch("list");
    }
    
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	$rs = $m->pageQuery();
    	return WSTGrid($rs);
    }
    
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
     * 跳去新增/编辑页面
     */
    public function toEdit(){
    	$id = Input("get.id/d",0);
    	$m = new M();
    	if($id>0){
    		$object = $m->getById($id);
    	}else{
    		$object = $m->getEModel('articles');
    		$object['catName'] = '';
    	}
    	$this->assign('object',$object);
    	$this->assign('articlecatList',model('Article_Cats')->listQuery(0));
        $this->assign("p",(int)input("p"));
    	return $this->fetch("edit");
    }
    
    /**
     * 新增
     */
    public function add(){
    	$m = new M();
    	$rs = $m->add();
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
     * 删除
     */
    public function del(){
    	$m = new M();
    	$rs = $m->del();
    	return $rs;
    }

    /**
     * 批量删除
     */
    public function delByBatch(){
        $m = new M();
        $rs = $m->delByBatch();
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
     * 店铺公告
     */
    public function notice(){
        $this->assign("p",(int)input("p"));
        return $this->fetch("notice");
    }
    /**
     * 获取公告列表
     */
    public function pageQueryByNotice(){
        $m = new M();
        $rs = $m->pageQueryByOther(51);
        return WSTGrid($rs);
    }
    /**
     * 进入公告编辑
     */
    public function toEditNotice(){
        $id = Input("get.id/d",0);
        $m = new M();
        if($id>0){
            $object = $m->getById($id);
        }else{
            $object = $m->getEModel('articles');
        }
        $this->assign('object',$object);
        $this->assign("p",(int)input("p"));
        return $this->fetch("edit_notice");
    }
    /**
     * 新增
     */
    public function addNotice(){
        $m = new M();
        $rs = $m->addOther(51,'add');
        return $rs;
    }
    
    
    /**
     * 编辑
     */
    public function editNotice(){
        $m = new M();
        $rs = $m->editOther(51,'edit');
        return $rs;
    }
    
    /**
     * 删除
     */
    public function delNotice(){
        $m = new M();
        $rs = $m->delOther(51);
        return $rs;
    }

    /**
     * 批量删除
     */
    public function delByBatchNotice(){
        $m = new M();
        $rs = $m->delByBatchOther(51);
        return $rs;
    }
    /**
     * 店铺帮助
     */
    public function help(){
        $this->assign("p",(int)input("p"));
        return $this->fetch("help");
    }
    /**
     * 获取帮助列表
     */
    public function pageQueryByHelp(){
        $m = new M();
        $rs = $m->pageQueryByOther(300);
        return WSTGrid($rs);
    }
    /**
     * 进入编辑
     */
    public function toEditHelp(){
        $id = Input("get.id/d",0);
        $m = new M();
        if($id>0){
            $object = $m->getById($id);
        }else{
            $object = $m->getEModel('articles');
        }
        $this->assign('object',$object);
        $this->assign("p",(int)input("p"));
        return $this->fetch("edit_help");
    }
    /**
     * 新增
     */
    public function addHelp(){
        $m = new M();
        $rs = $m->addOther(300,'add');
        return $rs;
    }
    
    
    /**
     * 编辑
     */
    public function editHelp(){
        $m = new M();
        $rs = $m->editOther(300,'edit');
        return $rs;
    }
    
    /**
     * 删除
     */
    public function delHelp(){
        $m = new M();
        $rs = $m->delOther(300);
        return $rs;
    }

    /**
     * 批量删除
     */
    public function delByBatchHelp(){
        $m = new M();
        $rs = $m->delByBatchOther(300);
        return $rs;
    }
    /**
     * 店铺帮助
     */
    public function guide(){
        $m = new M();
        $object = $m->getById(109);
        $this->assign("p",(int)input("p"));
        $this->assign("object",$object);
        return $this->fetch("guide");
    }
    /**
     * 获取帮助列表
     */
    public function pageQueryByGuide(){
        $m = new M();
        $rs = $m->pageQueryByOther(53,[109]);
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
        $rs = $m->addOther(53,'add');
        return $rs;
    }
    
    
    /**
     * 编辑
     */
    public function editGuide(){
        $m = new M();
        $rs = $m->editOther(53,'edit');
        return $rs;
    }
    
    /**
     * 删除
     */
    public function delGuide(){
        $m = new M();
        $rs = $m->delOther(53);
        return $rs;
    }

    /**
     * 批量删除
     */
    public function delByBatchGuide(){
        $m = new M();
        $rs = $m->delByBatchOther(53);
        return $rs;
    }
    /**
     * 保存入驻协议
     */
    public function editAgreement(){
        $m = new M();
        $rs = $m->editAgreement();
        return $rs;
    }

    /**
     * 查看用户注册协议
     */
    public function userAgreement(){
        $m = new M();
        $object = $m->getById(300);
        $this->assign('object',$object);
        return $this->fetch("edit_agreement");
    }
    /**
     * 保存入驻协议
     */
    public function editUserAgreement(){
        $m = new M();
        $rs = $m->editUserAgreement();
        return $rs;
    }
}
