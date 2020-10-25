<?php
namespace wstmart\common\model;
use wstmart\supplier\model\SupplierConfigs;
use think\Db;
use think\facade\Cache;
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
 * 门店类
 */
class Suppliers extends Base{
	protected $pk = 'supplierId';
    /**
     * 获取供货商指定字段
     */
    public function getFieldsById($supplierId,$fields){
        return $this->where(['supplierId'=>$supplierId,'dataFlag'=>1])->field($fields)->find();
    }

    /**
     * 获取供货商评分
     */
    public function getSupplierScore($supplierId){
        $supplier = $this->alias('s')->join('__SUPPLIER_SCORES__ cs','cs.supplierId = s.supplierId','left')
                    ->where(['s.supplierId'=>$supplierId,'s.supplierStatus'=>1,'s.dataFlag'=>1])->field('s.supplierAddress,s.supplierKeeper,s.supplierImg,s.supplierQQ,s.supplierId,s.supplierName,s.supplierTel,s.areaId,cs.*')->find();
        if(empty($supplier))return [];
        $supplier->toArray();
        $supplier['totalScore'] = WSTScore($supplier['totalScore']/3,$supplier['totalUsers']);
        $supplier['goodsScore'] = WSTScore($supplier['goodsScore'],$supplier['goodsUsers']);
        $supplier['serviceScore'] = WSTScore($supplier['serviceScore'],$supplier['serviceUsers']);
        $supplier['timeScore'] = WSTScore($supplier['timeScore'],$supplier['timeUsers']);
        WSTUnset($supplier, 'totalUsers,goodsUsers,serviceUsers,timeUsers');
        return $supplier;
    }
    /**
     * 获取供货商首页信息
     */
    public function getSupplierInfo($supplierId,$uId = 0){
        $rs = Db::name('suppliers')->alias('s')
        ->join('__SUPPLIER_EXTRAS__ ser','ser.supplierId=s.supplierId','inner')
        ->where(['s.supplierId'=>$supplierId,'s.supplierStatus'=>1,'s.dataFlag'=>1])
        ->field('s.supplierId,s.supplierImg,s.supplierName,s.supplierAddress,s.supplierQQ,s.supplierWangWang,s.supplierTel,s.serviceStartTime,s.longitude,s.latitude,s.serviceEndTime,s.supplierKeeper,mapLevel,s.areaId,s.isInvoice,s.invoiceRemarks,ser.*')
        ->find();
        if(empty($rs))return [];
        //仅仅是为了获取businessLicenceImg而写的，因为businessLicenceImg不排除被删除掉了
        WSTAllow($rs,'supplierId,supplierImg,supplierName,supplierAddress,supplierQQ,supplierWangWang,supplierTel,serviceStartTime,longitude,latitude,serviceEndTime,supplierKeeper,mapLevel,areaId,isInvoice,invoiceRemarks,businessLicenceImg');
        //评分
        $score = $this->getSupplierScore($rs['supplierId']);
        $rs['scores'] = $score;
        //供货商地址
        $rs['areas'] = Db::name('areas')->alias('a')->join('__AREAS__ a1','a1.areaId=a.parentId','left')
        ->where([['a.areaId','=',$rs['areaId']]])->field('a.areaId,a.areaName areaName2,a1.areaName areaName1')->find();
       
        //分类
        $goodsCatMap = [];
        $goodsCats = Db::name('cat_suppliers')->alias('cs')->join('__GOODS_CATS__ gc','cs.catId=gc.catId and gc.dataFlag=1','left')
        ->where(['supplierId'=>$rs['supplierId']])->field('cs.supplierId,gc.catName')->select();
        foreach ($goodsCats as $v){
            $goodsCatMap[] = $v['catName'];
        }
        $rs['catsuppliers'] = (isset($goodsCatMap))?implode(',',$goodsCatMap):'';
        
        $supplierAds = array();
        $config = Db::name('supplier_configs')->where("supplierId=".$rs['supplierId'])->find();
        //取出轮播广告
        if($config["supplierAds"]!=''){
            $supplierAdsImg = explode(',',$config["supplierAds"]);
            $supplierAdsUrl = explode(',',$config["supplierAdsUrl"]);
            for($i=0;$i<count($supplierAdsImg);$i++){
                $adsImg = $supplierAdsImg[$i];
                $supplierAds[$i]["adImg"] = $adsImg;
                $supplierAds[$i]["adUrl"] = $supplierAdsUrl[$i];
                $supplierAds[$i]['isOpen'] = false;
                if(stripos($supplierAdsUrl[$i],'http:')!== false || stripos($supplierAdsUrl[$i],'https:')!== false){
                    $supplierAds[$i]['isOpen'] = true;
                }
            }
            $rs['supplierAds'] = $supplierAds;
            unset($config['supplierAds']);
        }
        $rs = array_merge($rs,$config);
        //热搜关键词
        $rs['supplierHotWords'] = ($rs['supplierHotWords']!='')?explode(',',$rs['supplierHotWords']):[];

        return $rs;
    }

    /*
     * 入驻缴纳年费异步回调方法
     */
    public function completeEnter($obj){
        $trade_no = $obj["trade_no"];
        $orderNo = $obj["out_trade_no"];
        $targetId = (int)$obj["targetId"];
        $targetType = (int)$obj["targetType"];
        $payFrom = $obj["payFrom"];
        $payMoney = (float)$obj["total_fee"];
        $scene = $obj["scene"];
        $log = Db::name('supplier_fees')->where(["tradeNo"=>$trade_no])->find();
        if(!empty($log)){
            return WSTReturn('已缴纳年费',-1);
        }
        Db::startTrans();
        try {
            // 更新供货商铺的到期日期、申请状态、申请日期等
            if($scene=="enter"){
                $supplier = $this->where(['userId'=>$targetId])->find();
            }else{
                $supplier = $this->where(['supplierId'=>$targetId])->find();
            }
            $supplierExpireDate = $supplier["expireDate"];
            $newExpireDate = date('Y-m-d',strtotime("$supplierExpireDate +1 year"));
            $suppliersData = [];
            $suppliersData['expireDate'] = $newExpireDate;
            $logContent = '';
            if($scene=="enter"){
                $suppliersData['applyStatus'] = 1;
                $suppliersData['applyTime'] = date('Y-m-d H:i:s');
                $suppliersData['isPay'] = 1;
                $suppliersData['payAnnualFee'] = $payMoney;
                $suppliersData['isRefund'] = 0;
                $logContent = '入驻';
            }
            if($scene=="enter"){
                $this->where(['userId'=>$targetId])->update($suppliersData);
            }else{
                $this->where(['supplierId'=>$targetId])->update($suppliersData);
            }
            //创建一条充值流水记录
            $lm = [];
            $lm['targetType'] = $targetType;
            $lm['targetId'] = $targetId;
            $lm['dataId'] = $orderNo;
            $lm['dataSrc'] = 6;
            $lm['remark'] = '供货商'.$logContent.'缴纳年费 充值¥'.$payMoney."元";
            $lm['moneyType'] = 1;
            $lm['money'] = $payMoney;
            $lm['payType'] = $payFrom;
            $lm['tradeNo'] = $trade_no;
            $lm['createTime'] = date('Y-m-d H:i:s');
            model('LogMoneys')->create($lm);
            //创建一条支出流水记录
            $lm = [];
            $lm['targetType'] = $targetType;
            $lm['targetId'] = $targetId;
            $lm['dataId'] = $orderNo;
            $lm['dataSrc'] = 6;
            $lm['remark'] = '供货商'.$logContent.'缴纳年费 支出¥'.$payMoney."元";
            $lm['moneyType'] = 0;
            $lm['money'] = $payMoney;
            $lm['payType'] = 0;
            $lm['createTime'] = date('Y-m-d H:i:s');
            model('LogMoneys')->create($lm);
            // 创建缴费记录
            $logMoneyId = Db::name('log_moneys')->where(['tradeNo'=>$trade_no])->value('id');
            $fee = [];
            $fee['userId'] = $supplier["userId"];
            $fee['supplierId'] = $supplier["supplierId"];
            $fee['money'] = $payMoney;
            $fee['tradeNo'] = $trade_no;
            $fee['logMoneyId'] = $logMoneyId;
            $fee['remark'] = "供货商".$logContent."缴纳年费";
            $fee['startDate'] = $supplierExpireDate;
            $fee['endDate'] = date('Y-m-d',strtotime("$supplierExpireDate +1 year"));;
            $fee['createTime'] = date('Y-m-d H:i:s');
            Db::name('supplier_fees')->insert($fee);
            Db::commit();
            return WSTReturn('缴纳年费成功',1);
        } catch (Exception $e) {
            Db::rollback();
            return WSTReturn('缴纳年费失败',-1);
        }
    }

    /**
     * 入驻、续费余额支付
     */
    public function payByWallet($obj){
        $payPwd = input('payPwd');
        $orderNo = $obj['orderNo'];
        $targetId = (int)$obj["targetId"];
        $targetType = (int)$obj["targetType"];
        $payMoney = (float)$obj["total_fee"];
        $scene = $obj["scene"];
        if(!$payPwd)return WSTReturn('请输入密码',-1);
        $decrypt_data = WSTRSA($payPwd);
        if($decrypt_data['status']==1){
            $payPwd = $decrypt_data['data'];
        }else{
            return WSTReturn('支付失败');
        }

        //判断是否开启余额支付
        $isEnbalePay = model('Payments')->isEnablePayment('wallets');
        if($isEnbalePay==0)return WSTReturn('非法的支付方式',-1);
        $user = [];
        $supplier = [];
        //获取用户钱包/供货商钱包
        if($scene=="enter"){
            $user = Db::name('users')->where(['userId'=>$targetId])->find();
            $supplier = Db::name('suppliers')->where(['userId'=>$targetId])->find();
        }else{
            $supplier = Db::name('suppliers')->where(['supplierId'=>$targetId])->find();
            $user = Db::name('users')->where(['userId'=>$supplier['userId']])->find();
        }

        if($user['payPwd']=='')return WSTReturn('您未设置支付密码，请先设置密码',-1);
        if($user['payPwd']!=md5($payPwd.$user['loginSecret']))return WSTReturn('您的支付密码不正确',-1);
        if($scene=="enter"){
            if($payMoney > $user['userMoney'])return WSTReturn('您的钱包余额不足',-1);
        }else{
            if($payMoney > $supplier['supplierMoney'])return WSTReturn('您的供货商钱包余额不足',-1);
        }

        Db::startTrans();
        try{
            // 更新供货商的到期日期、申请状态、申请日期等
            if($scene=="enter"){
                $supplier = $this->where(['userId'=>$targetId])->find();
            }else{
                $supplier = $this->where(['supplierId'=>$targetId])->find();
            }
            $supplierExpireDate = $supplier["expireDate"];
            $newExpireDate = date('Y-m-d',strtotime("$supplierExpireDate +1 year"));
            $suppliersData = [];
            $suppliersData['expireDate'] = $newExpireDate;
            $logContent = '';
            if($scene=="enter"){
                $suppliersData['applyStatus'] = 1;
                $suppliersData['applyTime'] = date('Y-m-d H:i:s');
                $suppliersData['isPay'] = 1;
                $suppliersData['payAnnualFee'] = $payMoney;
                $suppliersData['isRefund'] = 0;
                $logContent = '入驻';
            }
            if($scene=="enter"){
                $this->where(['userId'=>$targetId])->update($suppliersData);
            }else{
                $this->where(['supplierId'=>$targetId])->update($suppliersData);
            }

            $lm = [];
            $lm['targetType'] = $targetType;
            $lm['targetId'] = $targetId;
            $lm['dataId'] = $orderNo;
            $lm['dataSrc'] = 6;
            $lm['remark'] = '供货商'.$logContent.'缴纳年费 支出¥'.$payMoney."元";
            $lm['moneyType'] = 0;
            $lm['money'] = $payMoney;
            $lm['payType'] = 'wallets';
            $lm['createTime'] = date('Y-m-d H:i:s');
            model('LogMoneys')->add($lm);
            $rechargeMoney = 0;
            if($scene=="enter"){
                //修改用户充值金额
                $rechargeMoney = ((float)$user['rechargeMoney']>(float)$payMoney)?$payMoney:$user['rechargeMoney'];
                model('users')->where(["userId"=>$user['userId']])->setDec("rechargeMoney",$rechargeMoney);
            }else{
                //修改供货商充值金额
                $rechargeMoney = ((float)$supplier['rechargeMoney']>(float)$payMoney)?$payMoney:$supplier['rechargeMoney'];
                model('suppliers')->where(["userId"=>$supplier['userId']])->setDec("rechargeMoney",$rechargeMoney);
            }

            // 创建缴费记录
            $logMoneyId = Db::name('log_moneys')->where(['dataId'=>$orderNo])->value('id');
            $fee = [];
            $fee['userId'] = $supplier["userId"];
            $fee['supplierId'] = $supplier["supplierId"];
            $fee['money'] = $payMoney;
            $fee['tradeNo'] = '';
            $fee['logMoneyId'] = $logMoneyId;
            $fee['remark'] = "供货商".$logContent."缴纳年费";
            $fee['startDate'] = $supplierExpireDate;
            $fee['endDate'] = date('Y-m-d',strtotime("$supplierExpireDate +1 year"));;
            $fee['createTime'] = date('Y-m-d H:i:s');
            $fee['lockCashMoney'] = $rechargeMoney;
            Db::name('supplier_fees')->insert($fee);
            Db::commit();
            return WSTReturn('支付成功',1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('支付失败');
        }
    }
}
