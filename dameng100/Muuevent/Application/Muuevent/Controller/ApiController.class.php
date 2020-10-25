<?php
namespace Muuevent\Controller;

use Think\Controller\RestController;

use Restful\Controller\BaseController;


class ApiController extends BaseController {
    protected $allowMethod    = array('get','post','put'); // REST允许的请求类型列表
    protected $allowType      = array('html','xml','json'); // REST允许请求的资源类型列表

    protected $muueventModel;
    protected $muueventTypeModel;
    protected $codeModel;

    function _initialize()
    {
        parent::_initialize();

        //判断Restful模块是否安装
        $map['name'] = 'Restful';
        $map['is_setup'] = 1;
        $res = M('Module')->where($map)->find();
        if(!$res){
            echo 'Restful模块未安装，请到应用商店下载并安装该模块。';exit;
        }
        $this->codeModel = D('Restful/Code');

        $this->muueventModel = D('Muuevent/Muuevent');
        $this->muueventTypeModel = D('Muuevent/MuueventType');

    }
    /**
     * 活动报名用户
     * @param  [type] $event_id [description]
     * @return [type]           [description]
     */
    public function attend_user($event_id,$page=1,$r=10){

        $event_id = I('event_id',0,'intval');
        $event = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event) {
            $result = $this->codeModel->code(400);
            $this->response($result,$this->type);
        }
        $map['event_id'] = $event_id;
        list($list,$totalCount) = D('MuueventAttend')->getListByPage($map,$page,'create_time desc','*',$r);
        foreach ($list as &$v) {
            $v['user'] = query_user(array('uid', 'nickname', 'avatar64'),$v['uid']);
        }
        unset($v);
        $result = $this->codeModel->code(200);
        $result['data'] = array('list'=>$list,'totalCount'=>$totalCount);
        $this->response($result,$this->type);

    }

    /**
     * 取消报名
     */
    public function unSign($event_id)
    {

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


}