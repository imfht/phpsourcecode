<?php
namespace wstmart\admin\model;
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
 * 登录日志业务处理
 */
class LogStaffLogins extends Base{
    /**
	 * 分页
	 */
	public function pageQuery(){
		$startDate = input('startDate');
		$endDate = input('endDate');
        $staffName = input('staffName');
        $loginIp = input('loginIp');
		$where = [];
		if($startDate!='')$where[] = ['l.loginTime','>=',$startDate." 00:00:00"];
		if($endDate!='')$where[] = [' l.loginTime','<=',$endDate." 23:59:59"];
        if($staffName!='')$where[] = [' s.staffName','like',"%".$staffName."%"];
        if($loginIp!='')$where[] = [' l.loginIp','like',"%".$loginIp."%"];
		return $mrs = Db::name('log_staff_logins')->alias('l')->join('__STAFFS__ s',' l.staffId=s.staffId','left')
			->where($where)
			->field('l.*,s.staffName')
			->order('l.loginId', 'desc')->paginate(input('limit/d'));
			
	}
}
