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
use Common\Model\CommonModel;
use Common\Util\Tree;
use Think\Controller;
/**
 * 后台菜单控制器
 * @author jry <598821125@qq.com>
 */
class HelpCategoryController extends AdminController{
    protected function _initialize(){
        parent::_initialize();
        $this->model=D('HelpCategory');
    }
    /**
     * 菜单列表
     * @author foryoufeng
     */
    public function index(){
        $data =  $this->model->getData();//从数据库中选出来的数据
        $data=$this->pagers($data);//对数据进行分页处理
        $binddata=array(//需要绑定的数据
            'id'=>'ID',
            'name'=>'栏目标题',
            'sort'=>'排序',
        );
        //使用Builder快速建立列表页面。
        $this->listBuilder($data,$binddata,'请输入ID/栏目标题');//显示界面
    }

    /**
     * 新增菜单
     * @author foryoufeng
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
                ['name', 'text', '标题', '栏目标题'],
                ['sort', 'num', '排序', '用于显示的顺序,越大越靠前']
            ];//需要添加表单的数据 ['name'(表单对应的数据), 'text'（对应的文本类型）, '标题'（名称）, '栏目标题'（提示）]
            $this->addBuilder($form);//添加表单
        }
    }

    /**
     * 编辑菜单
     * @author foryoufeng
     */
    public function edit(){
        if(IS_POST){
            $data=$this->editData();
            if($data){
                $this->success('更新成功', U('index'));
            }
        }else{
            //使用FormBuilder快速建立表单页面。
            $form=[
                ['id', 'hidden', 'ID', 'ID'],
                ['name', 'text', '标题', '栏目标题'],
                ['sort', 'num', '排序', '用于显示的顺序,越大越靠前']
            ];
            $this->editBuilder($form);//构建编辑表单
        }
    }
    public function setStatus()
    {
        $ids = I('request.ids');
        $status = I('request.status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $flag=$this->model->del($ids);
        if($flag){
            if($flag==CommonModel::MFAIL){
                $this->error('栏目下还有文章');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->success('删除成功，不可恢复', U('index'));
        }
    }
}
