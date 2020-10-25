<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Banner as Banners;
use app\common\model\ModuleClass;

class Banner extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new Banners;   //别名：避免与控制名冲突
    }

    public function index()
    {
        $where = [];
        if (input('get.search')){
            $where[] = ['title|url','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'sorts asc,id asc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
        foreach ($dataList as $k=>$v){
            $v->moduleClass;
        }
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
            $moduleClass = new ModuleClass();
            $modList = $moduleClass->where(['status' => 1, 'action' => 'banner'])->order('sorts ASC,id ASC')->select();
            $this->assign('modList', $modList);
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
            $moduleClass = new ModuleClass();
            $modList = $moduleClass->where(['status' => 1, 'action' => 'banner'])->order('sorts ASC,id ASC')->select();
            $this->assign('modList', $modList);

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
                $where[] = [ 'id','in', $id_arr ];
				$data = $this->cModel->where($where)->select();
				foreach ($data as $k => $v) {
					if( !empty($v['litpic']) && $v['litpic'] != '/static/common/img/logo.jpg' ){
						unlink(WEB_PATH.$v['litpic']);	//删除图片
					}
				}
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