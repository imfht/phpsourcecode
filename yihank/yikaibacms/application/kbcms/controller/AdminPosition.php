<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 推荐位管理
 */

class AdminPosition extends Admin {
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
        $this->assign('list',model('Position')->loadList());
        return $this->fetch();
    }

    /**
     * 增加
     */
    public function add(){
        if (input('post.')){
            $validate=validate('Position');
            if(!$validate->check(input('post.'))){
                $this->error($validate->getError());
            }
            $model = model('Position');
            if($model->add('add')){
                $this->success('推荐位添加成功！',url('index'));
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('推荐位添加失败');
                }else{
                    $this->error($msg);
                }
            }
        }else{
            $breadCrumb = array('推荐位列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        $model = model('Position');
        if (input('post.')){
            $validate=validate('Position');
            if(!$validate->check(input('post.'))){
                $this->error($validate->getError());
            }
            if($model->edit()){
                $this->success('推荐位修改成功！');
            }else{
                $this->error('推荐位修改失败');
            }
        }else{
            $positionId = input('position_id');
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
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $positionId = input('post.id');
        if(empty($positionId)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['A.position_id'] = $positionId;
        if(model('Position')->del($positionId)){
            $this->success('推荐位删除成功！');
        }else{
            $this->error('推荐位删除失败！');
        }
    }


}

