<?php
namespace Muuevent\Controller;
use Common\Controller\CommonController;

class MyController extends CommonController
{
    protected $muueventModel;
    protected $muueventTypeModel;
    protected $muueventAttendModel;

    public function _initialize()
    {
        //基础公共控制器
        parent::_initialize();

        //调用通用用户授权方法
        if(!_need_login()){
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
        $this->muueventModel = D('Muuevent/Muuevent');
        $this->muueventTypeModel = D('Muuevent/MuueventType');
        $this->muueventAttendModel = D('Muuevent/MuueventAttend');

    }
    /**
     * 我发布的活动
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function index($page=1,$r=20){

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

        $this->assign('list', $content);
        $this->assign('totalPageCount', $totalCount);
        $this->assign('r', $r);
        $this->display();
    }
    /**
     * 我参与的活动
     * @param  integer $page [description]
     * @param  integer $r    [description]
     * @return [type]        [description]
     */
    public function attend($page=1,$r=20){

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
        //dump($list);
        $this->assign('list', $list);
        $this->assign('totalPageCount', $totalCount);
        $this->assign('r', $r);
        $this->display();
    }

    /**
     * 参与活动成员
     */
    public function member($event_id = 0, $page=1, $r=20)
    {
        $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $event_id, 'uid'=>is_login()))->find();
        if (!$event_content) {
            $this->error('404 not found');
        }else{
            $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
            $this->assign('content',$event_content);
        }

        $map['event_id'] = $event_id;
        list($member,$totalCount) = D('MuueventAttend')->getListByPage($map,$page,'create_time desc','*',$r);
        foreach ($member as &$v) {
            $v['user'] = query_user(array('uid', 'nickname', 'avatar64'),$v['uid']);
        }
        unset($v);
        //dump($member);
        $this->assign('member',$member);
        $this->assign('totalCount',$totalCount);
        $this->assign('r',$r);
        $this->display();
    }
}