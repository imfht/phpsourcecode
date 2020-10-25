<?php
namespace wstmart\shop\model;
use wstmart\common\validate\GoodsConsult as Validate;
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
 * 商品咨询类
 */
class GoodsConsult extends Base{
    /**
     * 根据店铺id获取商品咨询
     */
    public function pageQuery($sId=0){
        // 查询条件
        $type = (int)input('consultType');
        $consultKey = input('consultKey');
        $shopId = ($sId==0)?(int)session('WST_USER.shopId'):$sId;
        $where = [];
        $where[] = ['g.shopId',"=",$shopId];
        if($type>0){$where[] = ['consultType',"=",$type];}
        if($consultKey!=''){$where[] = ['consultContent','like',"%$consultKey%"];}
        $rs = $this->alias('gc')
            ->join('__USERS__ u','u.userId=gc.userId','left')
            ->join('__GOODS__ g','g.goodsId=gc.goodsId','inner')
            ->field('gc.*,u.loginName,g.goodsName,g.goodsImg')
            ->where($where)
            ->order('gc.replyTime asc,gc.createTime desc')
            ->paginate(input('limit/d',5))->toArray();
        if(!empty($rs['data'])){
            foreach($rs['data'] as $k=>&$v){
                // 解义
                $v['consultContent'] = htmlspecialchars_decode($v['consultContent']);
                $v['reply'] = htmlspecialchars_decode($v['reply']);
                // 处理匿名
                if($v['userId']>0){
                    // 替换中间两个字符
                    $start = floor((strlen($v['loginName'])/2))-1;
                    $v['loginName'] = mb_convert_encoding(substr_replace($v['loginName'],'**',$start,2),'UTF-8');
                }
            }
        }
        return $rs;
    }

}