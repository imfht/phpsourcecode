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
 * 积分流水日志业务处理
 */
class UserScores extends Base{
    protected $pk = 'scoreId';

	/**
	 * 获取用户信息
	 */
	public function getUserInfo(){
		$id = (int)input('id');
        return model('users')->where('userId',$id)->field('loginName,userId,userName')->find();
	}

    /**
	 * 分页
	 */
	public function pageQuery(){
		$userId = (int)input('id');
		$startDate = input('startDate');
		$endDate = input('endDate');
        $where = [];
		if($startDate!='')$where[] = ['createTime','>=',$startDate." 00:00:00"];
		if($endDate!='')$where[] = [' createTime','<=',$endDate." 23:59:59"];
		$where[] = ['userId','=',$userId];
		$page = $this->where($where)->order('scoreId', 'desc')->paginate(input('limit/d'))->toArray();
		if(count($page['data'])>0){
			foreach ($page['data'] as $key => $v) {
				$page['data'][$key]['dataSrc'] = WSTLangScore($v['dataSrc']);
			}
		}
		return $page;
	}

	/**
     * 新增记录
     */
    public function addByAdmin(){
    	$data = [];
    	$data['userId'] = (int)input('userId');
    	$data['score'] = (int)input('score');
        $data['dataSrc'] = 10001;
        $data['dataId'] = 0;
        $data['scoreType'] = (int)input('scoreType');
        $data['dataRemarks'] = input('dataRemarks');
        $data['createTime'] = date('Y-m-d H:i:s');
        //判断用户身份
        $user = model('users')->where(['userId'=>$data['userId'],'dataFlag'=>1])->find();
        if(empty($user))return WSTReturn('无效的会员');
        if(!in_array($data['scoreType'],[0,1]))return WSTReturn('无效的调节类型');
        if($data['score']<=0)return WSTReturn('调节积分必须大于0');
        Db::startTrans();
		try{
            $result = $this->insert($data);
            if(false !== $result){
            	if($data['scoreType']==1){
                    $user->userScore = $user->userScore+$data['score'];
                    $user->userTotalScore = $user->userTotalScore+$data['score'];
            	}else{
            		$user->userScore = $user->userScore-$data['score'];
            	}
            	$user->save();
            }
            Db::commit();
			return WSTReturn('操作成功',1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1); 
		}
    }

    /**   
     * 获取签到排行
     */
    public function pageQueryByRanking(){
        $month = input('month',date('Y-m'));
        $where = [];
        $where[] = ['u.dataFlag','=',1];
        $where[] = ['s.dataSrc','=',5];
        $page = $this->alias('s')->join('__USERS__ u','s.userId=u.userId','inner')
                     ->where($where)
			         ->where('left(s.createTime,7)="'.$month.'"')
                     ->field('u.userName,u.loginName,u.userId,u.userPhoto,s.createTime,max(s.dataId) dataId,count(s.scoreId) signCount')
                     ->order('dataId', 'desc')
                     ->group('s.userId')
                     ->paginate(input('limit/d'))->toArray();
        if(count($page['data'])>0){
            foreach ($page['data'] as $key => $v) {
                $page['data'][$key]['userPhoto'] = WSTUserPhoto($v['userPhoto']);
            }
        }
        return $page;
    }

    /**
     * 积分统计分页
     */
    public function statPageQuery(){
        $startDate = input('startDate');
        $endDate = input('endDate');
        $where = [];
        if($startDate!='')$where[] = ['createTime','>=',$startDate." 00:00:00"];
        if($endDate!='')$where[] = [' createTime','<=',$endDate." 23:59:59"];
        $totalInScore = $this->where($where)->where(['scoreType'=>1])->sum("score");
        $totalOutScore = $this->where($where)->where(['scoreType'=>0])->sum("score");
        $page = $this->where($where)->order('scoreId', 'desc')->paginate(input('limit/d'))->toArray();
        if(count($page['data'])>0){
            foreach ($page['data'] as $key => $v) {
                $page['data'][$key]['totalInScore'] = $totalInScore;
                $page['data'][$key]['totalOutScore'] = $totalOutScore;
                $page['data'][$key]['dataSrc'] = WSTLangScore($v['dataSrc']);
            }
        }
        return $page;
    }
}
