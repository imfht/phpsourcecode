<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Navigation as Navigations;

class Navigation extends Common {
	private $cModel;
	//当前控制器关联模型

	public function initialize() {
		parent::initialize();
		$this->cModel = new Navigations;
		//别名：避免与控制名冲突
	}

	public function index() {
		if(request()->isPost()){
			$type = input('type');
			$where = ['type'=> $type];
			$list_data = $this->cModel->where($where)->order('pid ASC,orderby ASC')->select();
			$treeClass = new \expand\Tree();
			$list = $treeClass->create($list_data);
			return ajaxReturn('获取成功','',1,$list);
		}else{
			$where[] = ['type','<>',''];
			$type = input('type');
			if ($type) {
				$where = ['type' => $type];
			}
			$data = $this->cModel->where($where)->order('pid ASC, type ASC,orderby ASC')->select();
			$treeClass = new \expand\Tree();
			$dataList = $treeClass->create($data);
			$this->assign('fenlei', $this->cModel->fenLei);
			$this->assign('dataList', $dataList);

			return $this->fetch();
		}

	}

	public function create() {//添加
		if (request()->isPost()) {
			$data = input('post.');
			$result = $this->validate($data,C_NAME.'.add');
			if(true !== $result){
			    // 验证失败 输出错误信息
			    return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data);
			}
			if ($result) {
				return ajaxReturn('操作成功', url('index'));
			}else{
				return ajaxReturn('操作失败');
			}
		} else {
			$this->assign('fenlei', $this->cModel->fenLei);
			$this->assign('data', $data=0);
			$this->assign('list', $list='');
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
			if(true !== $result){
			    // 验证失败 输出错误信息
			    return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data, $data['id']);
			}
			if ($result) {
				return ajaxReturn('操作成功', url('index'));
			}else{
				return ajaxReturn('操作失败');
			}
		} else {
			$data = $this->cModel->get($id);
			$list_data = $this->cModel->where( [ ['type','=',$data['type']], ['id','neq',$data['id']] ] )->order('pid ASC,orderby ASC')->select();
			$treeClass = new \expand\Tree();
			$list = $treeClass->create($list_data);
			$this->assign('list', $list);
			$this->assign('data', $data);
			$this->assign('fenlei', $this->cModel->fenLei);
			return $this->fetch();
		}

	}

	public function delete() {//删除
		if (request()->isPost()) {
			$id = input('id');
			$idData = $this->cModel->get(['id'=>$id]);
			$pidData = $this->cModel->where(['pid'=>$id, 'type'=>$idData['type']])->count();
			if( $pidData > 0){
				return ajaxReturn('当前导航存在子导航,不允许删除.');
			}
			if (isset($id) && !empty($id)) {
				$id_arr = explode(',', $id);
				$where[] = ['id','in', $id_arr];
				$result = $this->cModel->where($where)->delete();
				if ($result) {
					return ajaxReturn('操作成功', url('index'));
				}else{
					return ajaxReturn('操作失败');
				}
			}
		}
	}

}
