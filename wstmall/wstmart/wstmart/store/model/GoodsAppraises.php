<?php
namespace wstmart\store\model;
use wstmart\common\validate\GoodsAppraises as Validate;
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
 * 评价类
 */
use think\Db;
class GoodsAppraises extends Base
{
    public function queryByPage($sId = 0)
    {
        $shopId = ($sId == 0) ? (int)session('WST_STORE.shopId') : $sId;

        $where = [];
        $where[] = ['g.goodsStatus', "=", 1];
        $where[] = ['g.dataFlag', "=", 1];
        $where[] = ['g.isSale', "=", 1];
        $c1Id = (int)input('cat1');
        $c2Id = (int)input('cat2');
        $goodsName = input('goodsName');
        if ($goodsName != '') {
            $where[] = ['g.goodsName', 'like', "%$goodsName%"];
        }
        if ($c2Id != 0 && $c1Id != 0) {
            $where[] = ['g.shopCatId2', "=", $c2Id];
        } else if ($c1Id != 0) {
            $where[] = ['g.shopCatId1', "=", $c1Id];
        }
        $where[] = ['g.shopId', "=", $shopId];


        $model = model('goods');
        $data = $model->alias('g')
            ->field('g.goodsId,g.goodsImg,g.goodsName,ga.shopReply,ga.id gaId,ga.replyTime,ga.goodsScore,ga.serviceScore,ga.timeScore,ga.content,ga.images,u.loginName')
            ->join('__GOODS_APPRAISES__ ga', 'g.goodsId=ga.goodsId', 'inner')
            ->join('__USERS__ u', 'u.userId=ga.userId', 'inner')
            ->order('ga.id desc')
            ->where($where)
            ->paginate(input('limit/d'))->toArray();
        return $data;
    }
}