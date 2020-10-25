<?php
namespace wstmart\common\model;
use think\Db;
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
 * 结算类
 */
class Settlements extends Base{
	protected $pk = 'settlementId';
	/**
     * 即时计算
     */
    public function speedySettlement($orderId){
        hook('beforeShopSettlement',['orderId'=>$orderId]);
        $order = model('common/orders')->get(['orderId'=>$orderId]);
        $shops = model('common/shops')->get(['shopId'=>$order->shopId]);
        if(empty($shops))return WSTReturn('结算失败，商家不存在');
        $backMoney = 0;

        $realTotalMoney = $order["realTotalMoney"];
        $commissionFee = $order["commissionFee"];
        $payType = $order["payType"];
        $refundedPayMoney = $order["refundedPayMoney"];
        $refundedScoreMoney = $order["refundedScoreMoney"];
        $refundedScore = $order["refundedScore"];
        $refundedGetScore = $order["refundedGetScore"];
        $refundedGetScoreMoney = $order["refundedGetScoreMoney"];
        $orderScore = $order["orderScore"];
        $useScore = $order["useScore"];
        $scoreMoney = $order['scoreMoney'];
        $settlementMoney = 0;
        
        if($payType==1){//线上支付
            if($realTotalMoney<=0 ){//线上支付，纯积分支付
                $scoreRat = 0;
                $surplusMoney = 0;
                if($scoreMoney>0){
                    $scoreRat = $scoreMoney/$useScore;
                    // 失效积分抵扣金额 = 失效的获得积分数 * 比例
                    $surplusMoney = $refundedGetScore * $scoreRat;
                    // 退还积分抵扣金额 = $refundedScore * 比例
                    $refundedScoreMoney =  $refundedScore * $scoreRat;
                }
                $settlementMoney = $scoreMoney - $refundedPayMoney - $refundedScoreMoney - $surplusMoney;
                //在线支付的返还金额=实付金额+积分抵扣金额-已退款支付金额-已退款积分抵扣金额-下单获得积分抵扣金额-佣金
                $backMoney = $scoreMoney - $refundedPayMoney - $refundedScoreMoney - $surplusMoney - $commissionFee;
            }else{
                $settlementMoney = $scoreMoney + $realTotalMoney - $refundedPayMoney - $refundedScoreMoney - $refundedGetScoreMoney;
                //在线支付的返还金额=实付金额+积分抵扣金额-已退款支付金额-已退款积分抵扣金额 - 失效获得积分换算的金额 -佣金
                $backMoney = $realTotalMoney + $scoreMoney - $refundedPayMoney - $refundedScoreMoney - $refundedGetScoreMoney - $commissionFee;
            }

        }else{//货到付款
            $settlementMoney = $scoreMoney;
            //货到付款的返还金额=积分抵扣金额-佣金
            $backMoney = $scoreMoney - $commissionFee;
        }
        $tmpBackMoney = WSTBCMoney($backMoney, $commissionFee);
        $settlementMoney = WSTBCMoney($settlementMoney, 0);
        $backMoney = WSTBCMoney($backMoney, 0);

        $data = [];
        $data['settlementType'] = 1;
        $data['shopId'] = $order->shopId;
        $data['settlementMoney'] = $settlementMoney;
        $data['commissionFee'] = $order->commissionFee;
        $data['backMoney'] = $backMoney;
        $data['settlementStatus'] = 1;
        $data['settlementTime'] = date('Y-m-d H:i:s');
        $data['createTime'] = date('Y-m-d H:i:s');
        $data['settlementNo'] = '';
        $settlementId = Db::name('settlements')->insertGetId($data);
        if($settlementId>0){
            $settlementNo =$settlementId.(fmod($settlementId,7));
            Db::name('settlements')->where('settlementId',$settlementId)->update(['settlementNo'=>$settlementNo]);
            $order->settlementId = $settlementId;
            $order->isClosed = 1;
            $order->save();
            //修改商家钱包
            $shops->noSettledOrderFee = $shops['noSettledOrderFee']+$order->commissionFee;//减少未结算佣金
            $shops->noSettledOrderNum = $shops['noSettledOrderNum']-1;//减少未结算订单数
            $shops->shopMoney = $shops['shopMoney']+$backMoney;
            $shops->save();
            //返还金额
            $lmarr = [];
            //货到付款处理
            if($order->payType==0){
                //如果有积分支付的话，还要补上一个积分支付的资金流水记录，不然流水上金额不对。
                if($order->scoreMoney >0){
                    $lm = [];
                    $lm['targetType'] = 1;
                    $lm['targetId'] = $order->shopId;
                    $lm['dataId'] = $settlementId;
                    $lm['dataSrc'] = 2;
                    $lm['remark'] = '结算订单申请【'.$settlementNo.'】返还积分支付金额¥'.$order->scoreMoney;
                    $lm['moneyType'] = 1;
                    $lm['money'] =$order->scoreMoney;
                    $lm['payType'] = 0;
                    $lm['createTime'] = date('Y-m-d H:i:s');
                    $lmarr[] = $lm;
                }
            }else{
                //在线支付的话，记录商家应得的钱的流水
                if($tmpBackMoney>0){
                    $lm = [];
                    $lm['targetType'] = 1;
                    $lm['targetId'] = $order->shopId;
                    $lm['dataId'] = $settlementId;
                    $lm['dataSrc'] = 2;
                    $lm['remark'] = '结算订单申请【'.$settlementNo.'】返还金额¥'.$tmpBackMoney;
                    $lm['moneyType'] = 1;
                    $lm['money'] =$tmpBackMoney;
                    $lm['payType'] = 0;
                    $lm['createTime'] = date('Y-m-d H:i:s');
                    $lmarr[] = $lm;
                } 
            }
            //收取佣金
            if($order->commissionFee>0){
                $lm = [];
                $lm['targetType'] = 1;
                $lm['targetId'] = $order->shopId;
                $lm['dataId'] = $settlementId;
                $lm['dataSrc'] = 2;
                $lm['remark'] = '结算订单申请【'.$settlementNo.'】收取订单佣金¥'.$order->commissionFee;
                $lm['moneyType'] = 0;
                $lm['money'] = $order->commissionFee;
                $lm['payType'] = 0;
                $lm['createTime'] = date('Y-m-d H:i:s');
                $lmarr[] = $lm;
            }
            model('common/LogMoneys')->saveAll($lmarr);
        }
        return WSTReturn('结算失败');
    }
}
