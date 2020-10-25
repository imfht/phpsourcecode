<?php
namespace wstmart\shop\behavior;
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
 * 记录用户的访问日志
 */
class ListenOperate 
{
    public function run($params){
        if(session("WST_USER.shopId")>0){
            $urls = session("WST_USER.shopMenuMaps");
            $request = request();
            $visit = strtolower($request->module()."/".$request->controller()."/".$request->action());
            $expurls = ['shop/logshopoperates/index','shop/logshopoperates/pagequery'];
            if(array_key_exists($visit,$urls) && !in_array($visit,$expurls)){

                $privilege = $urls[$visit];
                $data = [];
                $data['menuId'] = $privilege['menuId'];
                $data['operateUrl'] = $_SERVER['REQUEST_URI'];
                $data['operateDesc'] = $privilege['menuName'];
                $data['content'] = !empty($_REQUEST)?json_encode($_REQUEST):'';
                $data['operateIP'] = $request->ip();
                $data['operateSrc'] = 1;
                model('shop/LogShopOperates')->add($data);
            }
        }
    }
}