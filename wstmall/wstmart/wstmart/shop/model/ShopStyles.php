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
 * 风格设置类
 */
class ShopStyles extends Base{
    /**
     * 获取分类
     */
    public function getCats(){
        return $this->distinct(true)->field('styleSys')->select();
    }
    /**
     * 获取风格列表
     */
    public function listQuery(){
        $shopId = (int)session('WST_USER.shopId');
        $styleSys = input('styleSys');
        $styleCat = input('styleCat',-1);
        $where = [];
        $where[] = ['styleSys','=',$styleSys];
        $where[] = ['isShow','=',1];
        if($styleCat > -1){
            $where[] = ['styleCat','=',$styleCat];
        }
        $field = "";
        if($styleSys == "home"){
            $field = "shopHomeTheme";
        }elseif($styleSys == "mobile"){
            $field = "mobileShopHomeTheme";
        }elseif($styleSys == "wechat"){
            $field = "wechatShopHomeTheme";
        }elseif($styleSys == "weapp"){
            $field = "weappShopHomeTheme";
        }elseif($styleSys == "app"){
            $field = "appShopHomeTheme";
        }
        $field .= " as theme";
        $shopConfigs = Db::name("shop_configs")->field($field)->where("shopId","=",$shopId)->find();
        $rs = $this->where($where)->paginate(14,false,['page'=>input('p/d')])->toArray();
        if(count($rs['data'])>0) {
            foreach ($rs['data'] as $k => $v) {
                if (strpos($v['stylePath'], '/') == false) {
                    $rs['data'][$k]['styleImgPath'] = 'default';
                } else {
                    $stylePath = explode("/", $v["stylePath"]);
                    unset($stylePath[count($stylePath) - 1]);
                    $stylePath = implode("/", $stylePath);
                    $rs['data'][$k]['styleImgPath'] = 'default' . DS . $stylePath;
                }
            }
        }
        $cats = WSTDatas('SHOPSTYLES_CAT');
        return ['sys'=>$styleSys,'list'=>$rs,'theme'=>$shopConfigs['theme'],'cats'=>$cats,'cat'=>$styleCat];
    }

    /**
     * 编辑
     */
    public function changeStyle(){
        $shopId = (int)session('WST_USER.shopId');
        $id = (int)input('post.id');
        $object = $this->get($id);
        $shopConfigs = [];
        Db::startTrans();
        try{
            if($object["styleSys"] == "home"){
                $shopConfigs = ["shopHomeTheme"=>$object["stylePath"]];
            }elseif($object["styleSys"] == "mobile"){
                $shopConfigs = ["mobileShopHomeTheme"=>$object["stylePath"]];
            }elseif($object["styleSys"] == "wechat"){
                $shopConfigs = ["wechatShopHomeTheme"=>$object["stylePath"]];
            }elseif($object["styleSys"] == "weapp"){
                $shopConfigs = ["weappShopHomeTheme"=>$object["stylePath"]];
            }elseif($object["styleSys"] == "app"){
                $shopConfigs = ["appShopHomeTheme"=>$object["stylePath"]];
            }
            $rs = Db::name("shop_configs")->where("shopId","=",$shopId)->update($shopConfigs);
            if(false !== $rs){
                cache('WST_CONF',null);
                Db::commit();
                WSTClearAllCache();
                return WSTReturn('操作成功',1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn("操作失败");
        }
    }

    /**
     * 执行sql
     */
    private function excute($sql,$db_prefix=''){
        if(!isset($sql) || empty($sql)) return;
        $sql = str_replace("\r", " ", str_replace('`wst_', '`'.$db_prefix, $sql));// 替换表前缀
        $ret = array();
        foreach(explode(";\n", trim($sql)) as $query) {
            Db::execute(trim($query));
        }
    }
}
