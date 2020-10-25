<?php
namespace wstmart\shop\controller;
use wstmart\shop\model\Express as M;
use wstmart\common\model\Areas as AM;
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
 * 门店角色控制器
 */
class Express extends Base{

	/**
	 * 列表
	 */
	public function index(){
        $this->assign('p',(int)input('p'));
		return $this->fetch("express/list");
	}
	
    /**
    * 查询
    */
    public function pageQuery(){
        $m = new M();
        $data = $m->pageQuery();
        return WSTGrid($data);
    }
    
    /**
    * 启用
    */
    public function toggleSet(){
        $m = new M();
        $rs = $m->toggleSet();
        return $rs;
    }
    
    /**
    * 启用
    */
    public function enableExpress(){
        $m = new M();
        $rs = $m->enableExpress();
        return $rs;
    }

    /**
    * 停用
    */
    public function disableExpress(){
        $m = new M();
        $rs = $m->disableExpress();
        return $rs;
    }

    /**
     * 列表
     */
    public function index2(){
        $m = new M();
        $shopExpressId = (int)input("shopExpressId");
        $shopExpress = $m->getShopExpressInfo($shopExpressId);
        $this->assign('shopExpressId',$shopExpressId);
        $this->assign('p',(int)input('p'));
        $this->assign('shopExpress',$shopExpress);
        return $this->fetch("express/list2");
    }
    

    /**
    * 查询
    */
    public function listQuery2(){
        $m = new M();
        $rs = $m->listQuery2();
        return WSTReturn("", 1,$rs);
    }
    
    /**
     * 新增运费模板
     */
    public function edit(){
    	$m = new M();
        $id = (int)input("id");
        $shopExpressId = (int)input("shopExpressId");
        $pnames = [];
        if($id>0){
            $object = $m->getFreightById();
            $areas = model("common/Areas")->getAreasByIds($object['provinceIds']);
            foreach ($areas as $key => $vo) {
                $pnames[] = $vo["areaName"];
            }
        }else{
            $object = $m->getEModel('shop_freight_template');

        }
        $object["pnames"] = implode(",", $pnames);
        $otherAreas = $m->getOtherAreas($id,$shopExpressId);
        $object["otherAreas"] = $otherAreas;
        $otherCityIds = $otherAreas["otherCityIds"];
        $data = ['object'=>$object];
    	$m = new AM();
        $areaList = $m->listQuery(0);
        foreach ($areaList as $key => $vo) {
            $list = $m->listQuery($vo['areaId']);
            $areaList[$key]["list"] = $list;
            $provinceIds = explode(",",$object["provinceIds"]);
            if(in_array($vo['areaId'],$provinceIds)){
                $areaList[$key]["isDisabled"] = 0;
            }else{
                $areaList[$key]["isDisabled"] = 1;
                foreach ($list as $key2 => $v) {
                    if(!in_array($v["areaId"],$otherCityIds)){
                        $areaList[$key]["isDisabled"] = 0;
                        break;
                    }
                }
            }
        }
        $this->assign('id',$id);
        $this->assign('shopExpressId',$shopExpressId);
        $this->assign('areaList',$areaList);
    	return $this->fetch('express/edit',$data);
    }
	
	/**
     * 新增运费模板
     */
    public function toAdd(){
    	$m = new M();
    	return $m->add();
    }

    /**
     * 编辑运费模板
     */
    public function toEdit(){
        $m = new M();
        return $m->edit();
    }

	
	
    /**
     * 删除操作
     */
    public function del(){
    	$m = new M();
    	$rs = $m->del();
    	return $rs;
    }
    
}
