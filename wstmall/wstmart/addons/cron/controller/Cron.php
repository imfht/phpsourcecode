<?php
namespace addons\cron\controller;

use think\addons\Controller;
use addons\cron\model\Crons as M;
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
 * 计划任务控制器
 */
class Cron extends Controller{
    
    public function index(){
        $this->assign("p",(int)input("p"));
    	return $this->fetch("admin/list");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return WSTGrid($m->pageQuery());
    }
    public function toEdit(){
        $m = new M();
        $rs = $m->getById(Input("id/d",0));
        $this->assign("data",$rs);
        $this->assign("p",(int)input("p"));
        return $this->fetch("admin/edit");
    }
    /**
     * 修改
     */
    public function edit(){
        $m = new M();
        return $m->edit();
    }

    /**
     * 执行计划任务
     */
    public function runCron(){
        $m = new M();
        return $m->runCron();
    }
    public function runCrons(){
        $m = new M();
        return $m->runCrons();
    }

    /**
     * 停用计划任务 
     */
    public function changeEnableStatus(){
        $m = new M();
        return $m->changeEnableStatus();
    }
    
}
