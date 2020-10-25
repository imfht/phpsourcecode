<?php
namespace wstmart\shop\model;
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
     * 获取已结算的结算单列表
     */
    public function pageQuery(){
        $shopId = (int)session('WST_USER.shopId');
        $where = [];
        $where[] = ['shopId',"=",$shopId];
        if(input('settlementNo')!='')$where[] = ['settlementNo','like','%'.input('settlementNo').'%'];
        if((int)input('isFinish')>=0)$where[] = ['settlementStatus',"=",(int)input('isFinish')];
        return Db::name('settlements')->alias('s')->where($where)->order('settlementId', 'desc')
            ->paginate(input('limit/d'))->toArray();
    }
    /**
     *  获取未结算订单列表
     */
    public function pageUnSettledQuery(){
        $where = [];
        if(input('orderNo')!='')$where[] = ['orderNo','like','%'.input('orderNo').'%'];
        $where[] = ['dataFlag',"=",1];
        $where[] = ['orderStatus',"=",2];
        $where[] = ['settlementId',"=",0];
        $where[] = ['shopId',"=",(int)session('WST_USER.shopId')];
        $page =  Db::name('orders')->where($where)->order('orderId', 'desc')
                   ->field('orderId,orderNo,createTime,payType,goodsMoney,deliverMoney,totalMoney,commissionFee,realTotalMoney,
                            refundedPayMoney,refundedScoreMoney,refundedGetScoreMoney,afterSaleEndTime')
                   ->paginate(input('limit/d'))->toArray();
        if(count($page['data'])){
            foreach ($page['data'] as $key => $v) {
                $page['data'][$key]['payTypeNames'] = WSTLangPayType($v['payType']);
                $page['data'][$key]['afterSaleEndTime'] = date('Y-m-d',strtotime($v['afterSaleEndTime']));
            }
        }
        return $page;
    }
    

    /**
     * 获取已结算订单
     */
    public function pageSettledQuery(){
        $where = [];
        if(input('settlementNo')!='')$where[] = ['settlementNo','like','%'.input('settlementNo').'%'];
        if(input('orderNo')!='')$where[] = ['orderNo','like','%'.input('orderNo').'%'];
        if((int)input('isFinish')>=0)$where[] = ['settlementStatus',"=",(int)input('isFinish')];
        $where[] = ['dataFlag',"=",1];
        $where[] = ['orderStatus',"=",2];
        $where[] = ['o.shopId',"=",(int)session('WST_USER.shopId')];
        $page = Db::name('orders')->alias('o')->join('__SETTLEMENTS__ s','o.settlementId=s.settlementId')
        ->where($where)
        ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,totalMoney,o.commissionFee,realTotalMoney,
                 s.settlementTime,s.settlementNo,refundedPayMoney,refundedScoreMoney,refundedGetScoreMoney')
        ->order('s.settlementTime desc')
        ->paginate(input('limit/d'))->toArray();
        if(count($page['data'])){
            foreach ($page['data'] as $key => $v) {
                $page['data'][$key]['commissionFee'] = abs($v['commissionFee']);
                $page['data'][$key]['payTypeNames'] = WSTLangPayType($v['payType']);
            }
        }
        return $page;
    }

    /**
     * 获取结算订单详情
     */
    public function getById(){
        $shopId = (int)session('WST_USER.shopId');
        $settlementId = (int)input('id');
        $object =  Db::name('settlements')->alias('st')->where(['settlementId'=>$settlementId,'st.shopId'=>$shopId])->join('__SHOPS__ s','s.shopId=st.shopId','left')->field('s.shopName,st.*')->find();
        if(!empty($object)){
            $object['list'] = Db::name('orders')->where(['settlementId'=>$settlementId])
                      ->field('orderId,orderNo,payType,goodsMoney,deliverMoney,realTotalMoney,totalMoney,scoreMoney,commissionFee,createTime')
                      ->order('payType desc,orderId desc')->select();
        }
        return $object;
    }
}