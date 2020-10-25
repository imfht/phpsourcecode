<?php
namespace wstmart\common\model;
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
 * 插件类
 */
class Addons extends Base{
   
	public function getAddonsMaps(){
		$addons = cache('WST_ADDONS_MAPS');
		if(!$addons){
			$list = $this->where(["dataFlag"=>1])->field("name,title")->select();
			$addons = array();
			for($i=0,$j=count($list);$i<$j;$i++){
				$addon = $list[$i];
				$addons[strtolower($addon["name"])] = $addon["title"];
			}
			cache('WST_ADDONS_MAPS',$addons,86400);
		}
		return $addons;
	}

}
