<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\AuthRule as AuthRules;

class AuthRule extends Common
{
    private $cModel;   //当前控制器关联模型
    private $module = 'admin';

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new AuthRules;   //别名：避免与控制名冲突
    }

    public function index()
    {
        $dataList = $this->cModel->treeList($this->module);
        $this->assign('module', $this->module);
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    public function create() {	//添加
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
            $treeList = $this->cModel->treeList($this->module);
            $this->assign('module', $this->module);
            $this->assign('treeList', $treeList);
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
            $data = $this->cModel->get($id);
            $this->assign('data', $data);

            $this->assign('module', $this->module);

            $treeList = $this->cModel->treeList($this->module);
            $this->assign('treeList', $treeList);
            return $this->fetch();
        }
    }

    public function delete() {	//删除
        if (request()->isPost()){
            $id = input('id');
            $module = $this->module;
            if (isset($id) && !empty($id) && $module){
                $id_arr = explode(',', $id);
				if( !empty($id_arr) ){
					foreach ($id_arr as $k => $v) {
						$count = $this->cModel->where(['pid'=>$v])->count();
						if( $count > 0 ){
							return ajaxReturn('存在子节点,不能删除');
						}
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