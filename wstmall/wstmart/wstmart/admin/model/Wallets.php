<?php
namespace wstmart\admin\model;
use think\Loader;
use think\Db;
use Env;
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
 * 余额支付控制器
 */
class Wallets extends Base{
    /**
     * 店铺入驻不通过，退款年费
     */
    public function enterRefund($obj){
        $orderNo = $obj['orderNo'];
        $money = $obj['money'];
        Db::startTrans();
        try{
            //创建用户资金流水记录
            if($money>0) {
                $lm = [];
                $lm['targetType'] = 0;
                $lm['targetId'] = $obj['userId'];
                $lm['dataId'] = $orderNo;
                $lm['dataSrc'] = 5;
                $lm['remark'] = '店铺入驻审核不通过，退回店铺缴纳年费';
                $lm['moneyType'] = 1;
                $lm['money'] = $money;
                $lm['payType'] = 0;
                $lm['createTime'] = date('Y-m-d H:i:s');
                model('common/LogMoneys')->add($lm);
            }
            // 更新店铺的到期日期、退款状态
            $shop = Db::name('shops')->where(['userId'=>$obj['userId']])->find();
            $shopExpireDate = $shop["expireDate"];
            $newExpireDate = date('Y-m-d',strtotime("$shopExpireDate -1 year"));
            $shopsData['expireDate'] = $newExpireDate;
            $shopsData['isRefund'] = 1;
            Db::name('shops')->where(['userId'=>$obj['userId']])->update($shopsData);
            //修改用户充值金额
            $shopFee = Db::name('shop_fees')->where(['shopId'=>$shop['shopId'],'dataFlag'=>1,'isRefund'=>0])->find();
            model('users')->where(["userId"=>$obj['userId']])->setInc("rechargeMoney",$shopFee['lockCashMoney']);
            // 更新缴费记录
            Db::name('shop_fees')->where(['shopId'=>$shop['shopId'],'dataFlag'=>1,'isRefund'=>0])->update(['isRefund'=>1]);
            Db::commit();
            return WSTReturn("退款成功",1);
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn("退款失败");
    }

    /**
     * 供货商入驻不通过，退款年费
     */
    public function supplierEnterRefund($obj){
        $orderNo = $obj['orderNo'];
        $money = $obj['money'];

        Db::startTrans();
        try{
            //创建用户资金流水记录
            if($money>0) {
                $lm = [];
                $lm['targetType'] = 0;
                $lm['targetId'] = $obj['userId'];
                $lm['dataId'] = $orderNo;
                $lm['dataSrc'] = 6;
                $lm['remark'] = '供货商入驻审核不通过，退回供货商缴纳年费';
                $lm['moneyType'] = 1;
                $lm['money'] = $money;
                $lm['payType'] = 0;
                $lm['createTime'] = date('Y-m-d H:i:s');
                model('common/LogMoneys')->add($lm);
            }
            // 更新供货商的到期日期、退款状态
            $supplier = Db::name('suppliers')->where(['userId'=>$obj['userId']])->find();
            $supplierExpireDate = $supplier["expireDate"];
            $newExpireDate = date('Y-m-d',strtotime("$supplierExpireDate -1 year"));
            $suppliersData['expireDate'] = $newExpireDate;
            $suppliersData['isRefund'] = 1;
            Db::name('suppliers')->where(['userId'=>$obj['userId']])->update($suppliersData);
            //修改用户充值金额
            $supplierFee = Db::name('supplier_fees')->where(['supplierId'=>$supplier['supplierId'],'dataFlag'=>1,'isRefund'=>0])->find();
            model('users')->where(["userId"=>$obj['userId']])->setInc("rechargeMoney",$supplierFee['lockCashMoney']);
            // 更新缴费记录
            Db::name('supplier_fees')->where(['supplierId'=>$supplier['supplierId'],'dataFlag'=>1,'isRefund'=>0])->update(['isRefund'=>1]);
            Db::commit();
            return WSTReturn("退款成功",1);
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn("退款失败");
    }
}
