<?php
namespace app\admin\controller;

/**
 * Class Article 文章菜单控制器类
 * hongkai.wang 20161203  QQ：529988248
 */
class NavMenu extends Admin{
    /**
     * 当前模块参数
     */
    public function _infoModule() {
        $navId = input('nav_id');
        $data = array('info' => array('name' => '菜单管理',
            'description' => '管理网站全部菜单',
        ),
            'menu' => array(
                array(
                    'name' => '导航列表',
                    'url' => url('nav/index'),
                    'icon' => 'list',
                ),
                array('name' => '菜单列表',
                    'url' => url('index', array('nav_id' => $navId)),
                    'icon' => 'list',
                ),
            ),
            '_info' => array(
                array(
                    'name' => '添加菜单',
                    'url' => url('info', array('nav_id' => $navId)),
                ),
            )
        );
        return $data;
    }
	//文章菜单列表
	public function index(){
		$where=array();
        $where['nm.nav_id']=input('nav_id');
		$list=model('NavMenu')->loadList($where);
		$this->assign('list',$list);
        $this->assign('type',array('0'=>'频道','1'=>'列表'));
		$this->assign('count',count($list));
		return $this->fetch();
	}
    //信息
    public function info(){
        $navId = input('nav_id');
        $id=input('id');
        $nav_model=model('Nav');
        $model = model('NavMenu');
        if (input('post.')){
            $type=input('type');
            if ($type==1){
                $_POST['href']=$_POST['href1'];
                unset($_POST['href1']);
                unset($_POST['href2']);
            }else{
                $_POST['href']=$_POST['href2'];
                unset($_POST['href1']);
                unset($_POST['href2']);
            }
            if ($id){
                $check_status=$this->parentCheck($id,input('post.parent_id'),'admin/NavMenu');
                if ($check_status!==true){
                    return ajaxReturn(0,$check_status);
                }
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index',array('nav_id'=>$navId)));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $where_nav['nm.nav_id']=$navId;
            $this->assign('navList',model('NavMenu')->loadList($where_nav));//导航菜单分类
            $this->assign('categoryList',model('kbcms/Category')->loadList());//栏目分类
            $this->assign('info',$model->getInfo($id));
            $this->assign('navInfo',$nav_model->getInfo($navId));
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        if(empty($id)){
            return ajaxReturn(0,'参数不能为空');
        }
        //判断子栏目
        if(model('admin/NavMenu')->loadList(array(), $id)){
            return ajaxReturn(0,'请先删除子菜单！');
        }
        $map = array();
        $map['id'] = $id;
        if(model('NavMenu')->del($id)){
            return ajaxReturn(200,'删除成功！');
        }else{
            return ajaxReturn(0,'删除失败！');
        }
    }
}
