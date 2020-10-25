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
 * 自提点
 */
class Stores extends Base{
    
    protected $pk = 'storeId';

    public function checkSupportStores($userId){
      $addressId = input("addressId");
      $address = Db::name("user_address")->where(["userId"=>$userId,"addressId"=>$addressId])->field("areaId")->find();
      $areaId = (int)$address["areaId"];
      $list = Db::name("carts c")->join("goods g","c.goodsId=g.goodsId")
                ->where(["userId"=>$userId,"isCheck"=>1])
                ->field("g.shopId")
                ->group("g.shopId")
                ->select();
      $shopIds = [];
      foreach ($list as $k => $v) {
        $shopIds[] = $v["shopId"];
      }
      $where = [];
      $where[] = ["areaId","=",$areaId];
      $where[] = ["shopId","in",$shopIds];
      $where[] = ["dataFlag","=",1];
      $where[] = ["storeStatus","=",1];
      $rs = Db::name("stores")->where($where)->field("shopId")->group("shopId")->select();
      $storeMap = [];
      foreach ($rs as $k => $v) {
        $storeMap[$v["shopId"]] = 1;
      }
      return $storeMap;
    }

    /**
    * 获取列表
    */
    public function shopStores($userId){
      $addressId = input("addressId");
      $address = Db::name("user_address")->where(["userId"=>$userId,"addressId"=>$addressId])->field("areaId")->find();
     
      $rs = [];
      if(!empty($address)){
        $where = [];
        $shopId = (int)input("shopId");
        $areaId = (int)$address['areaId'];
        $where[] = ["areaId","=",$areaId];
        $where[] = ["shopId","=",$shopId];
        $where[] = ["dataFlag","=",1];
        $where[] = ["storeStatus","=",1];
        $rs = Db::name("stores")->where($where)->field("storeId,shopId,areaIdPath,storeName,storeTel,storeAddress")->limit(100)->select();
      }
      return $rs;
    }

    /**
    * 获取列表
    */
    public function listQuery($userId){
      $where = [];
      $shopId = (int)input("shopId");
      $areaId = (int)input("areaId");
      $where[] = ["areaId","=",$areaId];
      $where[] = ["shopId","=",$shopId];
      $where[] = ["dataFlag","=",1];
      $where[] = ["storeStatus","=",1];
      $rs = Db::name("stores")->where($where)->field("storeId,shopId,areaIdPath,storeName,storeTel,storeAddress")->limit(100)->select();
      return $rs;
    }
    
}
