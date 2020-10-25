<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/25
 * Time: 15:45
 */

namespace Admin\Common;
use Common\Builder\FormBuilder;
use Common\Builder\ListBuilder;
use Common\Controller\CommonController;
use Common\Model\CommonModel;
use Common\Util\Tree;

class AdminController extends  CommonController
{
    protected $model;
    /**
     * 首页界面初始化，涉及到了用户登录和权控制
     */
    protected function _initialize(){
        //登录检测
        if(!admin_login()){ //还没登录跳转到网站页面
            $this->redirect('/');
        }
        $user=session('user_auth');
        if('administrator'!=$user['roles']){// 访问权限控制

        }
        if(!D('ManagerGroup')->checkMenuAuth()){
            $this->error('权限不足！');
        }
        $map['status'] = array('eq', 1);
        if(!C('DEVELOP_MODE')){ //是否开启开发者模式
            $map['dev'] = array('neq', 1);
        }
        $tree=new Tree();
        $all_admin_menu_list = $tree->list_to_tree(D('SystemMenu')->where($map)->select()); //所有系统菜单
        //设置数组key为菜单ID
        foreach($all_admin_menu_list as $key => $val){
            $all_menu_list[$val['id']] = $val;
        }
        $current_menu = D('SystemMenu')->getCurrentMenu(); //当前菜单
        if($current_menu){
            $parent_menu = D('SystemMenu')->getParentMenu($current_menu); //获取面包屑导航
            foreach($parent_menu as $key => $val){
                $parent_menu_id[] = $val['id'];
            }
            $side_menu_list = $all_menu_list[$parent_menu[0]['id']]['_child']; //左侧菜单
        }
        $this->assign('current_active',dirname($current_menu['url']));//当前激活组件
        $this->assign('__ALL_MENU_LIST__', $all_menu_list); //所有菜单
        $this->assign('__SIDE_MENU_LIST__', $side_menu_list); //左侧菜单
        $this->assign('__PARENT_MENU__', $parent_menu); //当前菜单的所有父级菜单
        $this->assign('__PARENT_MENU_ID__', $parent_menu_id); //当前菜单的所有父级菜单的ID
        $this->assign('__CURRENT_ROOTMENU__', $parent_menu[0]['id']); //当前主菜单
        $this->assign('__USER__', session('user_auth')); //用户登录信息
        $this->assign('__CONTROLLER_NAME__', strtolower(CONTROLLER_NAME)); //当前控制器名称
        $this->assign('__ACTION_NAME__', strtolower(ACTION_NAME)); //当前方法名称
    }

    /**
     * 构建显示的数据
     * @param array $data 从数据库中选出来的数据
     * @param array $binddata  需要绑定的数据
     * @param string $search   显示搜索的提示
     * @param bool|false $status  是否有禁用  默认没有
     */
    public function listBuilder($data=array(),$binddata=array(),$search="",$status=false){
        $builder = new ListBuilder();
        $builder->setMetaTitle('thinkshop')->addTopButton('addnew');  //添加新增按钮
        if($status){
           $builder->addTopButton('resume');
        }
             //添加启用按钮
         $builder->addTopButton('delete')//添加删除按钮
            ->setSearch($search, U('index')); //添加搜索信息
        if($binddata){//绑定栏目数据
            foreach($binddata as $k=>$v){
                $builder->addTableColumn($k, $v);
            }
        }
        //绑定栏目显示的数据
            $builder->addTableColumn('right_button', 'operate', 'btn')
                ->setTableDataList($data) //数据列表;
                ->addRightButton('edit')   //添加编辑按钮
                ->addRightButton('delete') //添加删除按钮
                ->display();
    }

    /**
     * @param $form需要添加的表单数据
     */
    protected function addBuilder($form){
        $builder = new FormBuilder();
        $builder->setMetaTitle('add')->setPostUrl(U('add'));
        foreach($form as $v){
            if(count($v)==5){
                $builder->addFormItem($v[0],$v[1],$v[2],$v[3],$v[4]);
            }else{
                $builder->addFormItem($v[0],$v[1],$v[2],$v[3]);
            }
        }
        $builder->display();
    }

    /**
     * @param $form需要编辑的表单数据
     */
    protected function editBuilder($form){
        $id=I('get.id',0);
        $builder = new FormBuilder();
        $builder->setMetaTitle('edit')->setPostUrl(U('edit'));
        foreach($form as $v){
            if(count($v)==5){
                $builder->addFormItem($v[0],$v[1],$v[2],$v[3],$v[4]);
            }else{
                $builder->addFormItem($v[0],$v[1],$v[2],$v[3]);
            }
        }
        $builder->setFormData($this->model->find($id))->display();
    }
    /**
     * 提取获取到的分页数据并分配到模板变量中，并将其删除
     * @param $data 获取到的数据
     * @return mixed
     */
    protected function pagers($data){
        $pager=$data['pages'];
        $this->assign("pager",$pager);
        //遍历出所有页
        for($i=1;$i<=$pager['pagers'];$i++){
            $spage[$i]=$i;
        }
        $this->assign("spage",$spage);//显示所有页
        unset($data['pages']);//删除掉
        return $data;
    }
    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids    = I('request.ids');
        $status = I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        //特殊情况处理
        switch($model){
            case 'User':
                if(in_array(1, $ids, true) || 1 == $ids)
                    $this->error('不允许更改超级管理员状态');
                break;
            case 'UserGroup':
                if(in_array(1, $ids, true) || 1 == $ids)
                    $this->error('不允许更改超级管理员组状态');
                break;
        }
        $model_primary_key = D($model)->getPk();
        $map[$model_primary_key] = array('in',$ids);
        switch($status){
            case 'forbid' : //禁用条目
                $data = array('status' => 0);
                $this->editRow($model, $data, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 'resume' : //启用条目
                $data = array('status' => 1);
                $map  = array_merge(array('status' => 0), $map);
                $this->editRow($model, $data, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            case 'hide' : //隐藏条目
                $data = array('status' => 2);
                $map  = array_merge(array('status' => 1), $map);
                $this->editRow($model, $data, $map, array('success'=>'隐藏成功','error'=>'隐藏失败'));
                break;
            case 'show' : //显示条目
                $data = array('status' => 1);
                $map  = array_merge(array('status' => 2), $map);
                $this->editRow($model, $data, $map, array('success'=>'显示成功','error'=>'显示失败'));
                break;
            case 'recycle' : //移动至回收站
                $data['status'] = -1;
                $this->editRow($model, $data, $map, array('success'=>'成功移至回收站','error'=>'删除失败'));
                break;
            case 'restore' : //从回收站还原
                $data = array('status' => 1);
                $map  = array_merge(array('status' => -1), $map);
                $this->editRow($model, $data, $map, array('success'=>'恢复成功','error'=>'恢复失败'));
                break;
            case 'delete'  : //删除条目
                $result = D($model)->where($map)->delete();
                if($result){
                   return true;
                }else{
                    $this->error('删除失败');
                }
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $map   查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                      url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * @author jry <598821125@qq.com>
     */
    final protected function editRow($model, $data, $map, $msg){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        //如存在id字段，则加入该条件
        $fields = M($model)->getDbFields();
        if(in_array('id',$fields) && !empty($id)){
            $where = array_merge(array('id' => array('in', $id )) ,(array)$where);
        }
        $msg = array_merge(array('success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg);
        if(M($model)->where($map)->save($data) !== false){
            $this->success($msg['success'], $msg['url'], $msg['ajax']);
        }else{
            $this->error($msg['error'], $msg['url'], $msg['ajax']);
        }
    }
    protected function addData(){
        $data=$this->model->addData();
        if($data){
            if($data==CommonModel::MFAIL){
                $this->error('新增失败');
            }else{
                $this->error($data);
            }
        }else{
           return true;
        }
    }
    protected function editData(){
        $data=$this->model->editData();
        if($data){
            if($data==CommonModel::MFAIL){
                $this->error('更新失败');
            }else{
                $this->error($data);
            }
        }else{
            return true;
        }
    }
}