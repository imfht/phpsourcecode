<?php
namespace wstmart\home\controller;
use think\Db;
use think\Loader;
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
 * 门店控制器
 */

class Suppliers extends Base{
    protected $beforeActionList = ['checkAuth'];
    
    // 登录验证方法--用户
    public function checkUserType(){
        $USER = session('WST_USER');
        if(!($USER['userType']==0 || $USER['userType']==3)){
            if(request()->isAjax()){
                die('{"status":-999,"msg":"当前账号已关联店铺/门店信息，不能申请供货商"}');
            }else{
                $this->redirect('home/suppliers/disableApply');
                exit;
            }
        }
    }
    /**
     * 跳去商家入驻
     */
    public function join(){
        $this->checkUserType();
        if((int)WSTConf('CONF.isOpenSupplierApply')!=1)return;
        $rs = model('suppliers')->checkApply();
        $this->assign('isApply',(!empty($rs) && $rs['applyStatus']>=1)?1:0);
        $this->assign('applyStep',empty($rs)?1:$rs['applyStep']);
        $articles = model('Articles')->getArticlesByCat(401);
        $this->assign('articles',$articles);
        $supplierFlows = model('suppliers')->getSupplierFlows();
        $flowId = $supplierFlows[0]['flowId'];
        session('tmpSupplierApplyFlow',$supplierFlows);
        $tmpApplyStep = (int)session('tmpApplyStep');
        if($tmpApplyStep==0)session('tmpApplyStep',$flowId);
        $this->assign('flowId',$flowId);
        return $this->fetch('suppliers/supplier_join');
    }

    /**
     * 检测有步骤有没有遗漏，不允许跳过步骤
     */
    public function checkStep($flowId){
        $this->checkUserType();
        if((int)WSTConf('CONF.isOpenSupplierApply')!=1)return;
        $tmpSupplierApplyFlow = session('tmpSupplierApplyFlow');
        $tmpApplyStep = (int)session('tmpApplyStep');
        //如果没有建立数组则强制重新开始
        if(!$tmpSupplierApplyFlow){
            return $this->redirect(Url('home/suppliers/join'));
        }
        $flowSteps = [];
        $isFind = false;
        foreach ($tmpSupplierApplyFlow as $key => $v) {
            $flowSteps[] = $v['flowId'];
            if($v['flowId']==$tmpApplyStep){
                $isFind = true;
                break;
            }
        }
        //没找到这个环节强制重新开始
        if(!$isFind){
            $this->redirect(Url('home/suppliers/joinStepNext',array('id'=>$tmpSupplierApplyFlow[0]['flowId'])));
            exit();
        }
        //如果找到则判断是否当前环节是否有效
        if(!in_array($flowId,$flowSteps)){
            $flowId = end($flowSteps);
            $this->redirect(Url('home/suppliers/joinStepNext',array('id'=>$flowId)));
            exit();
        }
    }
    /**
     * 申请流程页面
     */
    public function joinStepNext(){
        $this->checkUserType();
        if((int)WSTConf('CONF.isOpenSupplierApply')!=1)return;
        $flowId = (int)input('id');
        $this->checkStep($flowId);
        $supplierFlows = model('suppliers')->getSupplierFlowDatas($flowId);
        $supplierStep = $supplierFlows['currStep'];
        $stepFields = model('suppliers')->getFlowFieldsById($flowId);
        $this->assign('supplierFlows',$supplierFlows['flows']);
        $this->assign('flowId',$flowId);
        $this->assign('prevStep',$supplierFlows['prevStep']);
        $this->assign('currStep',$supplierFlows['currStep']);
        $this->assign('nextStep',$supplierFlows['nextStep']);
        $this->assign('stepFields',$stepFields);
        $apply = model('suppliers')->getSupplierApply();
        $this->assign('apply',$apply);
        $pkey = input('pkey','');
        $this->assign('pkey',$pkey);
        if($pkey){
            $pkey = WSTBase64urlDecode($pkey);
            $pkey = explode('@',$pkey);
            $catFee = (int)$pkey[0];
            $payStep = (int)$pkey[1];
            $this->assign('catFee',$catFee);
            $this->assign('payStep',$payStep);
            $payments = model('common/payments')->getOnlinePayments();
            $this->assign('payments',$payments);
        }
        return $this->fetch('suppliers/supplier_join_step');
    }
    /**
     *保存流程表单信息
     */
    public function saveStep(){
        $this->checkUserType();
        if((int)WSTConf('CONF.isOpenSupplierApply')!=1)return WSTReturn('未开启供货商入驻');
        $data = input('post.');
        if(isset($data['tradeId']) && (int)$data['tradeId']>0){
            $trade = model("common/Trades")->getFieldsById((int)$data['tradeId'],"tradeFee");
            if(empty($trade))return WSTReturn('非法的所属行业');
            if(((int)$trade["tradeFee"]>0)){
                $pkey = WSTBase64urlEncode($trade["tradeFee"]."@1");
                session('tmpPkey',$pkey);
            }else{
                session('tmpPkey',null);
            }
        }else{
            session('tmpPkey',null);
        }
        $rs = model('suppliers')->saveStep($data);
        $rs['data']["pkey"] = '';
        $tmpPkey = session('tmpPkey');
        if($tmpPkey){
            $rs['data']["pkey"] = $tmpPkey;
        }
        return $rs;
    }
    /**
     * 入驻进度查询
     */
    public function checkapplystatus(){
        $this->checkUserType();
        if((int)WSTConf('CONF.isOpenSupplierApply')!=1)return;
        $apply = model('suppliers')->checkApply();
        if(empty($apply)){
            $flows = model('suppliers')->getSupplierFlowDatas();
            session('tmpApplyStep',$flows['currStep']['flowId']);
            $this->redirect(Url('home/suppliers/joinStepNext',array('id'=>$flows['currStep']['flowId'])));
            exit();
        }else{
            $flows = model('suppliers')->getSupplierFlowDatas($apply['applyStep']);
            if($flows['flows'][count($flows['flows'])-2]['flowId']==$apply['applyStep']){
                session('tmpApplyStep',$flows['nextStep']['flowId']);
                $tmpPkey = session('tmpPkey')?session('tmpPkey'):'';
                $this->redirect(url('home/suppliers/joinStepNext',array('id'=>$flows['nextStep']['flowId'],'pkey'=>$tmpPkey)));
            }else{
                $this->redirect(url('home/suppliers/joinStepNext',array('id'=>$apply['applyStep'])));
            }
            exit();
        }
    }

    public function disableApply(){
        return $this->fetch('suppliers/display_apply');
    }
}
