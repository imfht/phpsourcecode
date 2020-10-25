<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\LoginLog as LoginLogs;

class LoginLog extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new LoginLogs;   //别名：避免与控制名冲突
    }

    public function index() {
        $where = [];
        if (input('get.search')){
            $where[] = ['ip','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'id desc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    public function delete() {	//删除
        if (request()->isPost()){
            $id = input('id');
            if (isset($id) && !empty($id)){
                $id_arr = explode(',', $id);
                $result = $this->cModel->where([['id','in', $id_arr]])->delete();
                if ($result){
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
            }
        }
    }
}