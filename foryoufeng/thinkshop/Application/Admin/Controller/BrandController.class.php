<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Common\AdminController;
use Common\Builder\FormBuilder;
use Common\Builder\ListBuilder;
use Common\Util\Tree;
use Think\Controller;
/**
 * 后台菜单控制器
 * @author jry <598821125@qq.com>
 */
class BrandController extends AdminController{
    /**
     * 菜单列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $data = D('Brand')->getData();//从数据库中选出来的数据
        $data=$this->pagers($data);//对数据进行分页处理
        $binddata=array(//需要绑定的数据
            'id'=>'ID',
            'name'=>'品牌名称',
            'url'=>'网址',
            'logo'=>'logo',
        );
        //使用Builder快速建立列表页面。
        $this->listBuilder($data,$binddata,'请输入ID/品牌名称');//显示界面
    }

    /**
     * 新增菜单
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            var_dump($_POST);
            return;
            $menu_object = D('Brand');
            $data = $menu_object->create();
            if($data){
                $id = $menu_object->add();
                if($id){
                    $this->success('新增成功', U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($menu_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new FormBuilder();
            $builder->setMetaTitle('新增菜单') //设置页面标题
                    ->setPostUrl(U('add')) //设置表单提交地址
                   // ->addFormItem('pid', 'select', '上级菜单', '所属的上级菜单', select_list_as_tree('SystemMenu', null, '顶级菜单'))
                    ->addFormItem('name', 'text', '品牌', '品牌名称')
                    ->addFormItem('url', 'text', '链接', '品牌的链接地址或者外链')
                    ->addFormItem('logo', 'icon', '图标', '菜单图标')
                    ->addFormItem('content', 'kindeditor', '详细信息', '品牌信息描述')
                    ->addFormItem('sort', 'num', '排序', '用于显示的顺序')
                    ->display();
        }
    }

    /**
     * 编辑菜单
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $menu_object = D('SystemMenu');
            $data = $menu_object->create();
            if($data){
                if($menu_object->save()!== false){
                    $this->success('更新成功', U('index'));
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($menu_object->getError());
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $builder = new FormBuilder();
            $builder->setMetaTitle('新增菜单') //设置页面标题
                    ->setPostUrl(U('edit')) //设置表单提交地址
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->addFormItem('pid', 'select', '上级菜单', '所属的上级菜单', select_list_as_tree('SystemMenu', null, '顶级菜单'))
                    ->addFormItem('title', 'text', '标题', '菜单标题')
                    ->addFormItem('url', 'text', '链接', 'U函数解析的URL或者外链')
                    ->addFormItem('icon', 'icon', '图标', '菜单图标')
                    ->addFormItem('sort', 'num', '排序', '用于显示的顺序')
                    ->setFormData(D('SystemMenu')->find($id))
                    ->display();
        }
    }
}
