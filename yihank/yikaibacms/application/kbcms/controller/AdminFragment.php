<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 碎片管理
 */

class AdminFragment extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '碎片管理',
                'description' => '管理网站调用自定义变量',
                ),
            'menu' => array(
                    array(
                        'name' => '碎片列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '添加碎片',
                        'url' => url('add'),
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('碎片列表'=>url());
        $list=model('Fragment')->loadList();
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('_page',$list->render());
        return $this->fetch();
    }

    /**
     * 增加
     */
    public function add(){
        if (input('post.')){
            $validate=validate('Fragment');
            if(!$validate->check(input('post.'))){
                $this->error($validate->getError());
            }
            if(model('Fragment')->add()){
                $this->success('碎片添加成功！');
            }else{
                $this->error('碎片添加失败');
            }
        }else{
            $breadCrumb = array('碎片列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        $model = model('Fragment');
        if (input('post.')){
            $validate=validate('Fragment');
            if(!$validate->check(input('post.'))){
                $this->error($validate->getError());
            }
            if($model->edit('edit')){
                $this->success('碎片修改成功！');
            }else{
                $this->error('碎片修改失败');
            }
        }else{
            $fragmentId = input('fragment_id');
            if(empty($fragmentId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $info = $model->getInfo($fragmentId);
            $breadCrumb = array('碎片列表'=>url('index'),'修改'=>url('',array('fragment_id'=>$fragmentId)));
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
        $fragmentId = input('post.id');
        if(empty($fragmentId)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['fragment_id'] = $fragmentId;
        if(model('Fragment')->del($fragmentId)){
            $this->success('碎片删除成功！');
        }else{
            $this->error('碎片删除失败！');
        }
    }
    


}

