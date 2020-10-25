<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\ArctypeMod as ArctypeMods;

class ArctypeMod extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize() {
        parent::initialize();
        $this->cModel = new ArctypeMods;   //别名：避免与控制名冲突
    }

    public function index()
    {
        $where = [];
        if (input('get.search')){
            $where[] = ['like', '%'.input('get.search').'%'];
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

    public function create() {	//添加
        if (request()->isPost()){
            $data = input('post.');
			$result = $this->validate($data,C_NAME.'.add');
			if( true !== $result){
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
            return $this->fetch('edit');
        }
    }

    public function edit($id) {	//编辑
        if (request()->isPost()){
            $data = input('post.');
            if(in_array($data['id'], ['20','21','22'])){
                return ajaxReturn('系统默认文章模型不可操作');
            }
            if (count($data) == 2){
                foreach ($data as $k =>$v){
                    $fv = $k!='id' ? $k : '';
                }
				$result = $this->validate($data,C_NAME.'.'.$fv);
				if( true !== $result){
					return ajaxReturn($result);
				}else{
					$result = $this->cModel->allowField(true)->save($data, $data['id']);
				}
            }else{
            	$result = $this->validate($data,C_NAME.'.edit');
				if( true !== $result){
					return ajaxReturn($result);
				}else{
					$result = $this->cModel->allowField(true)->save($data, $data['id']);
				}
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
                if(in_array(20, $id_arr) || in_array(21, $id_arr) || in_array(22, $id_arr)){
                    return ajaxReturn('系统默认文章模型不可操作');
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