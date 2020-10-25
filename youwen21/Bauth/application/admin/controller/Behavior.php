<?php
namespace app\admin\controller;

use app\admin\model\AdminBehaviorLog as Alog;
/**
 * 用户行为
 */
class behavior extends Base
{
    /**
     * 行为列表
     * @author EchoEasy
     */
    public function index()
    {
        $map = $this->_initMap();
        $this->lists('admin_behavior_log', $map, 'id desc');
        // $list = (new Blog())->where($map)->order('id desc')->paginate(10);
        // $this->assign('_list', $list);
        return $this->fetch('index');
    }

    /**
     * 行为详情
     * @author EchoEasy
     */
    public function detail($id)
    {
        $info = Alog::get($id)->toArray();
        $this->assign('_info', $info);
        return $this->fetch();
    }

    // 查询条件组装
    private function _initMap()
    {
        $map = [];
        if($this->request->param('user_id')){
            $map['user_id'] = $this->request->param('user_id');
        }
        if($this->request->param('ip')){
            $map['ip'] = $this->request->param('ip');
        }
        if($this->request->param('begin_time')){
            $map['create_time'] = ['EGT', $this->request->param('begin_time')];
        }
        if($this->request->param('end_time')){
            $map['create_time'] = ['ELT', $this->request->param('begin_time')];
        }
        if($this->request->param('begin_time') && $this->request->param('end_time')){
            $map['create_time'] = ['BETWEEN', $this->request->param('begin_time').','.$this->request->param('end_time')];
        }
        return $map;
    }
}
