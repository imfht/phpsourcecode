<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\ModuleClass as ModuleClasss;

class ModuleClass extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new ModuleClasss;   //别名：避免与控制名冲突
    }

    public function index() {
        $where = [];
        if (input('get.search')){
            $where[] = ['title|action','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'sorts asc,id asc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    public function create() {	//新增
        if (request()->isPost()){
            $data = input('post.');
			$result = $this->validate($data,C_NAME.'.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data);
			}
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
        	$this->assign('data', $data=0);
            return $this->fetch('edit');
        }
    }

    public function edit($id) {	//编辑
        if (request()->isPost()){
            $data = input('post.');
            if (count($data) == 2){
                foreach ($data as $k =>$v){
                    $fv = $k!='id' ? $k : '';
                }
				$result = $this->validate($data,C_NAME.'.'.$fv);
            }else{
            	unset($data['action']);	//禁止编辑模块操作
            	$result = $this->validate($data,C_NAME.'.edit');
            }
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data, $data['id']);
			}
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            $data = $this->cModel->get($id);
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    public function delete() {	//删除
        if (request()->isPost()){
            $id = input('id');
            if (isset($id) && !empty($id)){
                $id_arr = explode(',', $id);
				foreach ($id_arr as $k => $v) {
					if( $v == 1 || $v == 2 || $v == 3 || $v == 4 ){
						return ajaxReturn('系统默认模块,不能删除');
					}
				}
                $where[] = [ 'id','in', $id_arr ];
                $result = $this->cModel->where($where)->delete();
                if ($result){
	                return ajaxReturn('操作成功', url('index'));
	            }else{
	                return ajaxReturn('操作失败');
	            }
            }
        }
    }
}