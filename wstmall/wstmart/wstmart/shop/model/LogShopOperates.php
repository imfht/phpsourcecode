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
 * 操作日志业务处理
 */
class LogShopOperates extends Base{
	protected $pk = "operateId";
    /**
	 * 分页
	 */
	public function pageQuery(){

        $shopId = session('WST_USER.shopId');
		$startDate = input('startDate');
		$endDate = input('endDate');
        $loginName = input('loginName');
        $operateUrl = input('operateUrl');
		$where = [];
		$where[] = [' l.shopId','=',$shopId];
		if($startDate!='')$where[] = ['l.operateTime','>=',$startDate." 00:00:00"];
		if($endDate!='')$where[] = [' l.operateTime','<=',$endDate." 23:59:59"];
        if($loginName!='')$where[] = [' u.loginName','like',"%".$loginName."%"];
        if($operateUrl!='')$where[] = [' l.operateUrl','like',"%".$operateUrl."%"];
		return $mrs = Db::name('log_shop_operates l')
			->join('shop_users su',' su.userId=l.userId','inner')
			->join('shops s',' s.shopId=su.shopId','inner')
		    ->join('users u',' l.userId=u.userId','inner')
			->where($where)
			->field('l.*,u.loginName,s.shopName')
			->order('l.operateId', 'desc')->paginate(input('limit/d'));
			
	}
	
	/**
	 * 新增操作权限
	 */
	public function add($param){
		$data = [];
		$data['userId'] = (int)session('WST_USER.userId');
		$data['operateTime'] = date('Y-m-d H:i:s');
		$data['menuId'] = $param['menuId'];
		$data['operateDesc'] = $param['operateDesc'];
		$data['content'] = $param['content'];
		$data['operateUrl'] = $param['operateUrl'];
		$data['operateIP'] = $param['operateIP'];
		$data['shopId'] = (int)session('WST_USER.shopId');
		$this->create($data);
	}
	
	/**
	 *  获取指定的操作记录
	 */
	public function getById($id){
		$rs = $this->get($id);
		if(!empty($rs)){
			return WSTReturn('', 1,$rs);
		}
		return WSTReturn('对不起，没有找到该记录', -1);
	}
}
