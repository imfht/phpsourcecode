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
        $modelList = get_all_service('ContentModel', '');
        if (!empty($modelList)) {
            $i = 0;
            foreach ($modelList as $key => $value) {
                $i++;
                $data['add'][$i]['name'] = '添加' . $value['name'] . '栏目';
                $data['add'][$i]['url'] = url($key . '/AdminCategory/add');
                $data['add'][$i]['icon'] = 'plus';
            }
        }
        return $data;
    }
    /**
     * 增加
     */
    public function add(){
        if (input('post.')){
            $validate=validate('CategoryPage');
            if(!$validate->scene('add')->check(input('post.'))){
                $this->error($validate->getError());
            }

            $_POST['app'] = request()->module();
            $model = model('CategoryPage');
            if($model->add()){
                $this->success('页面添加成功！');
            }else{
                $this->error('页面添加失败');
            }
        }else{
            $this->assign('name','添加');
            $this->assign('categoryList',model('kbcms/Category')->loadList());
            $this->assign('tplList',model('admin/Config')->tplList());//模板文件
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if (input('post.')){
            $validate=validate('CategoryPage');
            if(!$validate->scene('edit')->check(input('post.'))){
                $this->error($validate->getError());
            }
            $_POST['app'] = request()->module();
            $model = model('CategoryPage');
            if($model->edit()){
                $this->success('页面修改成功！');
            }else{
                $this->error('页面修改失败');
            }
        }else{
            $classId = input('id');
            if(empty($classId)){
                $this->error('参数不能为空！');
            }
            $model = model('CategoryPage');
            $info = $model->getInfo($classId);
            if(!$info){
                $this->error($model->getError());
            }
            $this->assign('name','修改');
            $this->assign('categoryList',model('kbcms/Category')->loadList());
            $this->assign('tplList',model('admin/Config')->tplList());//模板文件
            $this->assign('info',$info);
            return $this->fetch();
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
        if(model('kbcms/Category')->loadList(array(), $classId)){
            $this->error('请先删除子页面！');
        }
        //删除页面操作
        $model = model('CategoryPage');
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

