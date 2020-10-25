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
class HelpController extends AdminController{
    protected function _initialize(){
        parent::_initialize();
        $this->model=D('Help');
    }
    /**
     * 菜单列表
     * @author jry <598821125@qq.com>
     */
    public function index(){
        $data = D('Help')->getData();//从数据库中选出来的数据
        $data=$this->pagers($data);//对数据进行分页处理
        $binddata=array(//需要绑定的数据
            'id'=>'ID',
            'title'=>'标题',
            'name'=>'栏目',
            'count'=>'浏览量',
            'publish_time'=>'发布时间',
        );
        //使用Builder快速建立列表页面。
        $this->listBuilder($data,$binddata,'请输入ID/文章标题');//显示界面
    }

    /**
     * 新增菜单
     * @author jry <598821125@qq.com>
     */
    public function add(){
        if(IS_POST){
            $data=$this->addData();
            if($data){
                $this->success('新增成功', U('index'));
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $form=[
                ['category_id', 'select', '所属栏目', '隶属于那个栏目', select_to_tree('HelpCategory')],
                ['title', 'text', '标题', '栏目标题'],
                ['content', 'kindeditor', '详细信息', '文章信息描述']
            ];//需要添加表单的数据 ['name'(表单对应的数据), 'text'（对应的文本类型）, '标题'（名称）, '栏目标题'（提示）]
            $this->addBuilder($form);//添加表单
        }
    }

    /**
     * 编辑菜单
     * @author jry <598821125@qq.com>
     */
    public function edit($id){
        if(IS_POST){
            $data=$this->editData();
            if($data){
                $this->success('更新成功', U('index'));
            }
        }else{
            $select=select_to_tree('HelpCategory');
            //dump($select);
            $form=[
                ['id', 'hidden', 'ID', 'ID'],
                ['category_id', 'select', '所属栏目', '隶属于那个栏目',select_to_tree('HelpCategory')],
                ['title', 'text', '标题', '栏目标题'],
                ['content', 'kindeditor', '详细信息', '文章信息描述']
            ];
            $this->editBuilder($form);//构建编辑表单
        }
    }

    /**
     * 编辑状态
     */
    public function setStatus(){
        $flag=parent::setStatus();
        if($flag){
            $this->success('操作成功',U('index'));
        }
    }
}
