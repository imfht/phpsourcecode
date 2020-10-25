<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 推荐位管理
 */

class AdminPositionController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '推荐位管理',
                'description' => '管理网站调用自定义变量',
                ),
            'menu' => array(
                    array(
                        'name' => '推荐位列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '添加推荐位',
                        'url' => url('add'),
                    ),
                ),
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('推荐位列表'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',target('Position')->loadList());
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('推荐位列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->adminDisplay('info');
        }else{
            $model = target('Position');
            if($model->saveData('add')){
                $this->success('推荐位添加成功！',url('index'));
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('推荐位添加失败');
                }else{
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 修改
     */
    public function edit(){
        $model = target('Position');
        if(!IS_POST){
            $positionId = request('get.position_id','','intval');
            if(empty($positionId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $info = $model->getInfo($positionId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('推荐位列表'=>url('index'),'修改'=>url('',array('position_id'=>$positionId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            if($model->saveData('edit')){
                $this->success('推荐位修改成功！',url('index'));
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('推荐位修改失败');
                }else{
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $positionId = request('post.data');
        if(empty($positionId)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['A.position_id'] = $positionId;
        if(target('Position')->delData($positionId)){
            $this->success('推荐位删除成功！');
        }else{
            $this->error('推荐位删除失败！');
        }
    }


}

