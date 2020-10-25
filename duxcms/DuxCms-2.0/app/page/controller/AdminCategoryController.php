<?php
namespace app\page\controller;
use app\admin\controller\AdminController;
/**
 * 页面管理
 */
class AdminCategoryController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        $menu = target('duxcms/AdminCategory','controller');
        return $menu->infoModule;
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('页面列表'=>url('duxcms/AdminCategory/index'),'页面添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('categoryList',target('duxcms/Category')->loadList());
            $this->assign('tplList',target('admin/Config')->tplList());
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = APP_NAME;
            $model = target('CategoryPage');
            if($model->saveData('add')){
                $this->success('页面添加成功！',url('duxcms/AdminCategory/index'));
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('页面添加失败');
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
        if(!IS_POST){
            $classId = request('get.class_id','','intval');
            if(empty($classId)){
                $this->error('参数不能为空！');
            }
            $model = target('CategoryPage');
            $info = $model->getInfo($classId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('页面列表'=>url('duxcms/AdminCategory/index'),'页面修改'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('categoryList',target('duxcms/Category')->loadList());
            $this->assign('tplList',target('admin/Config')->tplList());
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = APP_NAME;
            $model = target('CategoryPage');
            if($model->saveData('edit')){
                $this->success('页面修改成功！',url('duxcms/AdminCategory/index'));
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('页面修改失败');
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
        $classId = request('post.data');
        if(empty($classId)){
            $this->error('参数不能为空！');
        }
        //判断子页面
        if(target('duxcms/Category')->loadList(array(), $classId)){
            $this->error('请先删除子页面！');
        }
        //删除页面操作
        $model = target('CategoryPage');
        if($model->delData($classId)){
            $this->success('页面删除成功！');
        }else{
            $msg = $model->getError();
            if(empty($msg)){
                $this->error('页面删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }

}

