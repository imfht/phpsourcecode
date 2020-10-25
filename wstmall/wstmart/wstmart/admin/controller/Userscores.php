<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\UserScores as M;
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
 * 积分日志控制器
 */
class Userscores extends Base{
	
    public function toUserScores(){
        $m = new M();
        $this->assign("p",(int)input("p"));
        $object = $m->getUserInfo();
        $this->assign("object",$object);
    	return $this->fetch("list");
    }
    
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	return WSTGrid($m->pageQuery());
    }

    /**
     * 跳去新增界面
     */
    public function toAdd(){
        $m = new M();
        $object = $m->getUserInfo();
        $this->assign("object",$object);
        return $this->fetch("box");
    }

    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->addByAdmin();
    }

    /**
     * 签到排行
     */
    public function ranking(){
         $this->assign("p",(int)input("p"));
        return $this->fetch("ranking");
    }

    /**
     * 获取签到排行分页
     */
    public function pageQueryByRanking(){
        $m = new M();
        return WSTGrid($m->pageQueryByRanking());
    }

    /**
     * 统计获取分页
     */
    public function statPageQuery(){
        $m = new M();
        return WSTGrid($m->statPageQuery());
    }
}
