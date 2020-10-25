<?php
namespace app\article\controller;
use app\admin\controller\AdminController;
/**
 * 栏目管理
 */
class AdminCategoryController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        $menu = target('duxcms/AdminCategory','controller');
        $menu = $menu->infoModule;
        return $menu;
        
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('栏目列表'=>url('duxcms/AdminCategory/index'),'文章栏目添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('categoryList',target('duxcms/Category')->loadList());
            $this->assign('tplList',target('admin/Config')->tplList());
            $this->assign('expandList',target('duxcms/FieldsetExpand')->loadList());
            $this->assign('default_config',current_config());
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = APP_NAME;
            if(target('CategoryArticle')->saveData('add')){
                $this->success('栏目添加成功！',url('duxcms/AdminCategory/index'));
            }else{
                $msg = target('CategoryArticle')->getError();
                if(empty($msg)){
                    $this->error('栏目添加失败');
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
            $model = target('CategoryArticle');
            $info = $model->getInfo($classId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('栏目列表'=>url('duxcms/AdminCategory/index'),'文章栏目修改'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('categoryList',target('duxcms/Category')->loadList());
            $this->assign('tplList',target('admin/Config')->tplList());
            $this->assign('expandList',target('duxcms/FieldsetExpand')->loadList());
            $this->assign('default_config',current_config());
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = APP_NAME;
            if(target('CategoryArticle')->saveData('edit')){
                $this->success('栏目修改成功！',url('duxcms/AdminCategory/index'));
            }else{
                $msg = target('CategoryArticle')->getError();
                if(empty($msg)){
                    $this->error('栏目修改失败');
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
        //判断子栏目
        if(target('duxcms/Category')->loadList(array(), $classId)){
            $this->error('请先删除子栏目！');
        }
        //判断栏目下内容
        $where = array();
        $where['A.class_id'] = $classId;
        $contentNum = target('ContentArticle')->countList($where);
        if(!empty($contentNum)){
            $this->error('请先删除该栏目下的内容！');
        }
        //删除栏目操作
        if(target('CategoryArticle')->delData($classId)){
            $this->success('栏目删除成功！');
        }else{
            $msg = target('CategoryArticle')->getError();
            if(empty($msg)){
                $this->error('栏目删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }

}

