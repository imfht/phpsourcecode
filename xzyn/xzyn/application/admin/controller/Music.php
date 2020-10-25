<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Music as Musics;

class Music extends Common {
	private $cModel;
	//当前控制器关联模型

	public function initialize() {
		parent::initialize();
		$this->cModel = new Musics;
		//别名：避免与控制名冲突
	}

	public function create() {//添加
		if (request()->isPost()) {
			$data = input('post.');
			$result = $this->validate($data,C_NAME . '.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data);
			}
			if ($result) {
				return ajaxReturn('操作成功', url('Config/music'));
			} else {
				return ajaxReturn('操作失败');
			}
		} else {
			$this->assign('data', $data=0);
			return $this->fetch('edit');
		}
	}

	public function edit($id) {//编辑
		if (request()->isPost()) {
			$data = input('post.');
            if (count($data) == 2){
                foreach ($data as $k =>$v){
                    $fv = $k!='id' ? $k : '';
                }
				$result = $this->validate($data,C_NAME.'.'.$fv);
            }else{
            	$result = $this->validate($data,C_NAME . '.edit');
            }
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data, $data['id']);
			}
			if ($result) {
				return ajaxReturn('操作成功', url('Config/music'));
			} else {
				return ajaxReturn('操作失败');
			}
		} else {
			$data = $this->cModel->get($id);
			$this->assign('data', $data);
			return $this->fetch();
		}

	}

	public function delete() {//删除
		if (request()->isPost()) {
			$id = input('id');
			if (isset($id) && !empty($id)) {
				$id_arr = explode(',', $id);
				$where[] = ['id','in', $id_arr ];
				$result = $this->cModel->where($where)->delete();
				if ($result) {
					return ajaxReturn('操作成功', url('Config/music'));
				} else {
					return ajaxReturn('操作失败');
				}
			}
		}
	}

}
