<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\Feedbacks as validate;
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
 * 功能反馈业务处理
 */
class Feedbacks extends Base{
	/**
	 * 获取功能反馈列表
	 */
	public function pageQuery(){
		$startDate = input('startDate');
		$endDate = input('endDate');
		$feedbackContent = input('feedbackContent');
        $feedbackType = input('feedbackType');
     	// 搜搜条件
     	$where = [];
     	$where[] = ['dataFlag','=',1];
        if($feedbackContent!=''){
            $where[] = ['feedbackContent','like','%'.$feedbackContent.'%'];
        }
        if($feedbackType>-1){
            $where[] = ['feedbackType','=',$feedbackType];
        }
		if($startDate!='' && $endDate!=''){
			$where[] = ['createTime','between',[$startDate.' 00:00:00',$endDate.' 23:59:59']];
		}else if($startDate!=''){
			$where[] = ['createTime','>=',$startDate.' 00:00:00'];
		}else if($endDate!=''){
			$where[] = ['createTime','<=',$endDate.' 23:59:59'];
		}

		$rs = Db::name('feedbacks')
						      ->where($where)
						      ->order('feedbackId desc')
						      ->paginate(input('limit/d'))
						      ->toArray();
        if(count($rs['data'])>0) {
            foreach ($rs['data'] as $key => $val) {
                $feedbackType = WSTDatas('FEEDBACK_TYPE', $val['feedbackType']);
                $rs['data'][$key]['feedbackType'] = $feedbackType['dataName'];
                $rs['data'][$key]['feedbackStatusName'] = $val['feedbackStatus']==0?'未处理':'已处理';
                if($rs['data'][$key]['userId'] != 0){
                    $rs['data'][$key]['userName'] = Db::name('users')->where('userId','=',$rs['data'][$key]['userId'])->value('userName');
                }else{
                    $rs['data'][$key]['userName'] = '游客';
                }
            }
        }
		return $rs;
	}

    /**
     * 获取单条记录
     */
    public function getById($feedbackId){
        $rs = $this
            ->where(['dataFlag'=>1,'feedbackId'=>$feedbackId])
            ->find();
        if($rs){
            if($rs['userId'] != 0){
                $rs['userName'] = Db::name('users')->where('userId','=',$rs['userId'])->value('userName');
            }else{
                $rs['userName'] = '游客';
            }
            $rs['feedbackType'] = WSTDatas('FEEDBACK_TYPE')[$rs['feedbackType']]['dataName'];
            if($rs['feedbackStatus'] == 1 && $rs['staffId'] != 0){
                $rs['staffName'] = Db::name('staffs')->where('staffId','=',$rs['staffId'])->value('staffName');
            }
            return $rs;
        }
        return [];
    }

    /**
     * 回复反馈
     */
    public function edit(){
        $id = input('post.feedbackId/d');
        $data = input('post.');
        $data['handleTime'] = date('Y-m-d H:i:s');
        $data['staffId'] = (int)session('WST_STAFF.staffId');
        $data['feedbackStatus'] = 1;
        $rs = $this->where(['feedbackId'=>$id,'dataFlag'=>1])->find()->toArray();
        Db::startTrans();
        try{
            $validate = new validate();
            if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
            $result = $this->allowField(true)->save($data,['feedbackId'=>$id]);
            if(false !== $result){
                //发送一条用户信息
                if((int)$rs['userId'] > 0){
                    $tpl = WSTMsgTemplates('FEEDBACK_REPLY');
                    if( $tpl['tplContent']!='' && $tpl['status']=='1'){
                        $find = ['${CONTENT}','${HANDLE_CONTENT}'];
                        $replace = [WSTMSubstr($rs['feedbackContent'],0,50,'utf-8',true),$data['handleContent']];
                        WSTSendMsg($rs['userId'],str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>$rs['feedbackId']]);
                    }
                }
                Db::commit();
                return WSTReturn("回复成功", 1);
            }else{
                return WSTReturn($this->getError(),-1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('回复失败',-1);
        }
    }

    /**
     * 删除工单
     */
    public function del(){
        $id = (int)input('post.feedbackId/d');
        $data = [];
        $data['dataFlag'] = -1;
        Db::startTrans();
        try{
            $result = $this->update($data,['feedbackId'=>$id]);
            if(false !== $result){
                Db::commit();
                return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('删除失败',-1);
        }
    }
}
