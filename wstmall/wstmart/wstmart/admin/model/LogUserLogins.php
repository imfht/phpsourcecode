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
 * 用户登录日志业务处理
 */
class LogUserLogins extends Base{
    /**
	 * 分页[用户登录日志]
	 */
	public function pageQuery(){
		$startDate = input('startDate');
		$endDate = input('endDate');
        $loginName = input('loginName');
        $loginIp = input('loginIp');
        $loginSrc = input('loginSrc');
		$where = [];
		if($startDate!='')$where[] = ['l.loginTime','>=',$startDate." 00:00:00"];
		if($endDate!='')$where[] = [' l.loginTime','<=',$endDate." 23:59:59"];
        if($loginName!='')$where[] = [' u.loginName','like',"%".$loginName."%"];
        if($loginIp!='')$where[] = [' l.loginIp','like',"%".$loginIp."%"];
        if($loginSrc>-1)$where[] = [' l.loginSrc','=',$loginSrc];
        $mrs = Db::name('log_user_logins')->alias('l')->join('__USERS__ u',' l.userId=u.userId','left')
			->where($where)
			->field('l.*,u.loginName')
			->order('l.loginId', 'desc')->paginate(input('limit/d'))->toArray();
        if(count($mrs['data'])>0){
            foreach ($mrs['data'] as $key => $v){
                $mrs['data'][$key]['loginSrc'] = WSTLangLoginSrc($v['loginSrc']);
            }
        }
		return $mrs;
	}

	/**
	 * 分页【商家登录日志】
	 */
	public function shopPageQuery(){
		$startDate = input('startDate');
		$endDate = input('endDate');
        $loginName = input('loginName');
        $loginIp = input('loginIp');
        $loginSrc = input('loginSrc');
		$where = [];
		if($startDate!='')$where[] = ['l.loginTime','>=',$startDate." 00:00:00"];
		if($endDate!='')$where[] = [' l.loginTime','<=',$endDate." 23:59:59"];
        if($loginName!='')$where[] = [' u.loginName','like',"%".$loginName."%"];
        if($loginIp!='')$where[] = [' l.loginIp','like',"%".$loginIp."%"];
        if($loginSrc>-1)$where[] = [' l.loginSrc','=',$loginSrc];
        $mrs = Db::name('log_user_logins l')
	        ->join('__USERS__ u',' l.userId=u.userId','left')
	        ->join("shop_users su","su.userId=u.userId","inner")
	        ->join("shops s","su.shopId=s.shopId","inner")
			->where($where)
			->field('l.*,u.loginName,s.shopName')
			->order('l.loginId', 'desc')->paginate(input('limit/d'))->toArray();
        if(count($mrs['data'])>0){
            foreach ($mrs['data'] as $key => $v){
                $mrs['data'][$key]['loginSrc'] = WSTLangLoginSrc($v['loginSrc']);
            }
        }
		return $mrs;
	}
}
