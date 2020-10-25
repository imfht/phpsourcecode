<?php
namespace app\page\controller;
use app\admin\controller\Admin;

/**
 * 页面管理
 */
class AdminCategory extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        $data = array('info' => array('name' => '栏目管理',
            'description' => '管理网站全部栏目',
        ),
            'menu' => array(
                array('name' => '栏目列表',
                    'url' => url('kbcms/AdminCategory/index'),
                    'icon' => 'list',
                ),
            ),
        );
        $modelList = get_page_type();
        if (!empty($modelList)) {
            $i = 0;
            foreach ($modelList as $key => $value) {
                $i++;
                $data['_info'][$i]['name'] = '添加' . $value['name'] . '栏目';
                $data['_info'][$i]['url'] = url($key . '/AdminCategory/info');
                $data['_info'][$i]['icon'] = 'plus';
            }
        }
        return $data;
    }
    /**
     * 详情
     */
    public function info(){
        $model = model('CategoryPage');
        $class_id=input('post.class_id');
        if (input('post.')){
            $_POST['app'] = request()->module();
            if ($class_id){
                $check_status=$this->parentCheck();
                if ($check_status!==true){
                    return ajaxReturn(0,$check_status);
                }
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status){
                return ajaxReturn(200,'操作成功',url('kbcms/adminCategory/index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $this->assign('categoryList',model('kbcms/Category')->loadList());//分类
            $this->assign('tplList',model('admin/Config')->tplList());//模板文件
            $this->assign('info',$model->getInfo(input('id')));
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $classId = input('id');
        if(empty($classId)){
            return ajaxReturn(0,'参数不能为空');
        }
        //判断子页面
        if(model('kbcms/Category')->loadList(array(), $classId)){
            return ajaxReturn(0,'请先删除子菜单！');
        }
        //删除页面操作
        $model = model('CategoryPage');
        if($model->del($classId)){
            return ajaxReturn(200,'页面删除成功！');
        }else{
            $msg = $model->getError();
            if(empty($msg)){
                return ajaxReturn(0,'页面删除失败！');
            }else{
                return ajaxReturn(0,$msg);
            }
        }
    }
}

