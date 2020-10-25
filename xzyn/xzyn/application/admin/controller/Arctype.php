<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Arctype as Arctypes;
use app\common\model\ArctypeMod;

class Arctype extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize() {
        parent::initialize();
        $this->cModel = new Arctypes;   //别名：避免与控制名冲突

    }

    public function index() {
        $dataList = $this->cModel->treeList();
        foreach ($dataList as $k=>$v){
            if ($v['arctypeMod']['mod'] == 'addonjump' && !empty($v['jumplink'])){
                $dataList[$k]['typelink'] = $v['jumplink'];
            }else{
                $dataList[$k]['typelink'] = url('@category/'.$v['dirs']);
            }
        }
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
	            if ($result){
	            	cache('DB_TREE_ARETYPE', null);   //删除栏目缓存
	            	cache('DB_COMMIN_ARCTYPE', null);	//删除导航缓存
	                return ajaxReturn('操作成功', url('index'));
	            }else{
	                return ajaxReturn('操作失败');
	            }
			}
        }else{
            $arctypeList = $this->cModel->treeList();
            $this->assign('arctypeList', $arctypeList);
            $this->assign('listTemp', $this->cModel->listTemp);	//列表页模板
            $this->assign('contentTemp', $this->cModel->contentTemp);//内容页模板
            $amModel = new ArctypeMod();
            $modList = $amModel->where(['status' => 1])->order('sorts ASC,id ASC')->select();
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
            cache('DB_TREE_ARETYPE', null);   //删除栏目缓存
            cache('DB_COMMIN_ARCTYPE', null);	//删除导航缓存
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            $arctypeList = $this->cModel->treeList();
            $this->assign('arctypeList', $arctypeList);
            $this->assign('listTemp', $this->cModel->listTemp);	//列表页模板
            $this->assign('contentTemp', $this->cModel->contentTemp);//内容页模板
            $amModel = new ArctypeMod();
            $modList = $amModel->where(['status' => 1])->order('sorts ASC,id ASC')->select();
            $this->assign('modList', $modList);

            $data = $this->cModel->get($id);
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    public function delete() {	//删除
        if (request()->isPost()){
            $id = input('id');
            if (!empty($id)){
                $id_arr = explode(',', $id);

				foreach ($id_arr as $k => $v) {
					$num = $this->cModel->where(['pid'=>$v])->count();
					if( $num > 0 ){
						return ajaxReturn('存在子分类,不能删除');
					}
					$Archive = new \app\common\model\Archive;
					$count = $Archive->where(['typeid'=>$v])->count();
					if( $count > 0 ){
						return ajaxReturn('分类有文章,不能删除');
					}
				}
                $where[] = [ 'id','in', $id_arr ];
                $result = $this->cModel->where($where)->delete();
                cache('DB_TREE_ARETYPE', null);   //删除栏目缓存
                if ($result){
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
            }
        }
    }
}