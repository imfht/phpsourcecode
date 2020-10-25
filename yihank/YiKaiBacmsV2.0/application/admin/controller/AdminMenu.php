<?php
namespace app\admin\controller;

class AdminMenu extends Admin{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '管理菜单',
                'description' => '站点运行信息',
            ),
            'menu' => array(
                array(
                    'name' => '菜单列表',
                    'url' => url('index'),
                    'icon' => 'list',
                ),
            ),
            '_info' => array(
                array(
                    'name' => '添加菜单',
                    'url' => url('info'),
                ),
            )
        );
    }
    //首页
    public function index(){
        $list = model('admin/AdminMenu')->loadList();
        $this->assign('list',$list);
        return $this->fetch();
    }
    //后台菜单
    public function info(){
        $id = input('id');
        $model = model('admin/AdminMenu');
        if (input('post.')){
            if (!empty($_POST['act'])){
                $_POST['act']=json_encode($_POST['act']);
            }
            if ($id){
                //$check_status=$this->menuCheck();
                $check_status=$this->parentCheck($id,input('post.pid'),'admin/AdminMenu');
                if ($check_status!==true){
                    return ajaxReturn(0,$check_status);
                }
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $info=$model->getInfo($id);
            if ($id){
                $status=$this->checkUrl($id);
                $info['rs']=$status;
            }
            $this->assign('info', $info);
            $this->assign('start_act',count($info['act'])+1);
            $this->assign('menuList',$model->menuLoadlist());
            $this->assign('iconFont',(array)get_all_service('Font','Admin')['data']['list']);
            return $this->fetch();
        }
    }
    //菜单删除
    public function del(){
        $id=input('id');
        if (empty($id)){
            return ajaxReturn(0,'参数不能为空');
        }
        //判断子栏目
        if(model('admin/AdminMenu')->loadList(array(), $id)){
            return ajaxReturn(0,'请先删除子菜单！');
        }
        //删除栏目操作
        if(model('admin/AdminMenu')->del($id)){
            return ajaxReturn(200,'栏目删除成功！');
        }else{
            return ajaxReturn(0,'栏目删除失败！');
        }
    }

    /**
     * 检测url
     * 返回：1需要url 2不需要url
     */
    public function checkUrl($id=''){
        if (empty($id)){
            $id=input('post.id');
        }
        if ($id==0){
            return 2;
        }
        return 1;
    }
}
