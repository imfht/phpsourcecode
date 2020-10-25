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
class SystemMenuController extends AdminController{
    /**
     * 菜单列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        //搜索
        $keyword = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array($condition, $condition, '_multi'=>true); //搜索条件

        //获取所有菜单
        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('SystemMenu')->where($map)->order('sort asc, id asc')->select();

        //转换成树状列表
        $tree = new Tree();
        $data_list = $tree->toFormatTree($data_list);
        //dump($data_list);
        //使用Builder快速建立列表页面。
        $builder = new ListBuilder();
        $builder->setMetaTitle('菜单列表') //设置页面标题
                ->addTopButton('addnew')  //添加新增按钮
                ->addTopButton('resume')  //添加启用按钮
                ->addTopButton('forbid')  //添加禁用按钮
                ->addTopButton('delete')  //添加删除按钮
                ->setSearch('请输入ID/菜单名称', U('index'))
                ->addTableColumn('id', 'ID')
                ->addTableColumn('title_show', '标题')
                ->addTableColumn('url', '链接')
                ->addTableColumn('icon', '图标', 'icon')
                ->addTableColumn('sort', '排序')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list) //数据列表
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('forbid') //添加禁用/启用按钮
                ->addRightButton('delete') //添加删除按钮
                ->display();
    }

    /**
     * 新增菜单
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $menu_object = D('SystemMenu');
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
                    ->addFormItem('pid', 'select', '上级菜单', '所属的上级菜单', select_list_as_tree('SystemMenu', null, '顶级菜单'))
                    ->addFormItem('title', 'text', '标题', '菜单标题')
                    ->addFormItem('url', 'text', '链接', 'U函数解析的URL或者外链')
                    ->addFormItem('icon', 'icon', '图标', '菜单图标')
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
