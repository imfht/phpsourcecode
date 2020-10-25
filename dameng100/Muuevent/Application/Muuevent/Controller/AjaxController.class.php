<?php
namespace Muuevent\Controller;
use Common\Controller\CommonController;

class AjaxController extends CommonController
{
    protected $muueventModel;
    protected $muueventTypeModel;
    protected $muueventAttendModel;

    public function _initialize()
    {
        //基础公共控制器
        parent::_initialize();
        $this->muueventModel = D('Muuevent/Muuevent');
        $this->muueventTypeModel = D('Muuevent/MuueventType');
        $this->muueventAttendModel = D('Muuevent/MuueventAttend');
    }


/**
     * 活动报名用户
     * @param  [type] $event_id [description]
     * @return [type]           [description]
     */
    public function attend_user($page=1,$r=12){

        $event_id = I('event_id',0,'intval');
        $event = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event) {

            $this->error('错误的活动ID');
        }
        $map['event_id'] = $event_id;
        list($list,$totalCount) = D('MuueventAttend')->getListByPage($map,$page,'create_time desc','*',$r);
        foreach ($list as &$v) {
            $v['user'] = query_user(array('uid', 'nickname', 'avatar64'),$v['uid']);
        }
        unset($v);

        //组装JSON返回数据
        if(isset($list)){
            $result['status']=1;
            $result['info'] = 'success';
            $result['data'] = array('list'=>$list,'totalCount'=>$totalCount);
        }else{
            $result['status']=0;
            $result['info'] = 'error';
        }
        $this->ajaxReturn($result,'JSON');
    
    }

    /**
     * 取消报名
     */
    public function unSign($event_id)
    {
        if(!_need_login()){
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
        $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        if($event_content['uid']==is_login()){
            $check = D('MuueventAttend')->where(array('uid' => is_login(), 'event_id' => $event_id))->find();

            $res = D('MuueventAttend')->where(array('uid' => is_login(), 'event_id' => $event_id))->delete();
            if ($res) {
                if ($check['status']) {
                    $this->muueventModel->where(array('id' => $event_id))->setDec('attentionCount');
                }
                $this->muueventModel->where(array('id' => $event_id))->setDec('signCount');

                $this->success('取消报名成功');
            } else {
                $this->error('操作失败');
            }
        }
    }
    /**
     * ajax删除活动
     */
    public function doDel($event_id)
    {
        if(!_need_login()){
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
        $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        if($event_content['uid']==is_login()){
            $res = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->setField('status', 0);
            if ($res) {
                $this->success('删除成功！', U('Muuevent/Index/index'));
            } else {
                $this->error('操作失败！');
            }
        }
    }

    /**
     * ajax提前结束活动
     */
    public function doEnd($event_id)
    {
        if(!_need_login()){
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
        $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error('活动不存在！');
        }
        if($event_content['uid']==is_login()){
            $data['eTime'] = time();
            $data['deadline'] = time();
            $res = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->setField($data);
            if ($res) {
                $this->success('操作成功！');
            } else {
                $this->error('操作失败！');
            }
        }else{
            $this->error('未授权操作！');
        }
        
    }


    /**===============MY==================**/
    /**
     * 我发布的活动
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function my_add($page=1,$r=20){
        
        if(!_need_login()){
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
        //获取列表
        $map['uid'] = is_login();
        
        $order = 'create_time desc';
        $norh == 'hot' && $order = 'signCount desc';
        $content = $this->muueventModel->where($map)->order($order)->page($page, $r)->select();
        
        $totalCount = $this->muueventModel->where($map)->count();
        foreach ($content as &$v) {

            $v['type'] = $this->muueventTypeModel->getType($v['type_id']);

            $v['province'] = D('district')->where(array('id' => $v['province']))->getField('name');
            $v['city'] = D('district')->where(array('id' => $v['city']))->getField('name');
            $v['district'] = D('district')->where(array('id' => $v['district']))->getField('name');

            if($v['city']=='市辖区' || $v['city']==$v['province']){
                $v['city']='';
            }
        }
        unset($v);

        //组装JSON返回数据
        if(isset($content)){
            $result['status']=1;
            $result['info'] = 'success';
            $result['data'] = array('list'=>$content,'totalCount'=>$totalCount);
        }else{
            $result['status']=0;
            $result['info'] = 'error';
        }
        $this->ajaxReturn($result,'JSON');  
    }
    /**
     * 我参与的活动
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function my_attend($page=1,$r=20){

        if(!_need_login()){
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
        $map['uid'] = is_login();
        $order = 'create_time desc';
        list($attend,$totalCount) = $this->muueventAttendModel->getListByPage($map,$page,'create_time desc','*',$r);

        $event_id = array();
        array_walk($attend, function($value, $key) use (&$event_id ){
            $event_id [] = $value['event_id'];
        });
        $event_id = implode(',',$event_id);
        
        $e_map['id']  = array('in',$event_id);
        $list = $this->muueventModel->where($e_map)->order('create_time desc')->select();
        foreach ($list as &$v) {

            $v['type'] = $this->muueventTypeModel->getType($v['type_id']);

            $v['province'] = D('district')->where(array('id' => $v['province']))->getField('name');
            $v['city'] = D('district')->where(array('id' => $v['city']))->getField('name');
            $v['district'] = D('district')->where(array('id' => $v['district']))->getField('name');

            if($v['city']=='市辖区' || $v['city']==$v['province']){
                $v['city']='';
            }
        }
        unset($v);

        //组装JSON返回数据
        if(isset($list)){
            $result['status']=1;
            $result['info'] = 'success';
            $result['data'] = array('list'=>$list,'totalCount'=>$totalCount);
        }else{
            $result['status']=0;
            $result['info'] = 'error';
        }
        $this->ajaxReturn($result,'JSON');  
    }
}
