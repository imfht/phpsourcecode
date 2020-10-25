<?php
namespace Home\Controller;

use Think\Controller;
use Think\Exception;

class RoleController extends Controller
{
    public function configMenu($id)
    {
        $menu = M('sys_menu')->select();
        $this->assign('menulist', $menu);
        $this->assign('id', $id);

        $Model = new \Think\Model();
        $menuids = $Model->query("select GROUP_CONCAT(menuid) as id from sys_menu_role as a
          LEFT JOIN sys_menu as b on a.menuid=b.id
          WHERE b.parentid!=0 and roleid='" . $id . "' GROUP BY roleid");

        $this->assign('menuids', $menuids[0]['id']);
        $this->display();
    }

    public function configMenuEntity($id, $menuids)
    {
        $condition['roleid'] = $id;
        $model=M('sys_menu_role');
        $model->where($condition)->delete();
        $arr = explode(",", $menuids);

        $superMenu=array();

        for ($i = 0; $i < count($arr); $i++) {
            if (!empty($arr[$i])) {
                $data['roleid'] = $id;
                $data['menuid'] = $arr[$i];
                $model->add($data);

                $superMenuEntity=M('sys_menu')->where('id='.$arr[$i])->find();
                if(!in_array($superMenuEntity['parentid'],$superMenu)){
                    array_push($superMenu,$superMenuEntity['parentid']);

                    $data['roleid'] = $id;
                    $data['menuid'] = $superMenuEntity['parentid'];
                    $model->add($data);
                }
            }
        }
    }

    public function configUser($id)
    {
        $User = M('sys_user')->select();
        $this->assign('users', $User);
        $this->assign('id', $id);

        $Model = new \Think\Model();
        $userids = $Model->query("select GROUP_CONCAT(userid) as id from sys_role_user WHERE roleid='" . $id . "' GROUP BY roleid");
        $this->assign('userids', $userids[0]['id']);
        $this->display();
    }

    public function configUserEntity($id, $userids)
    {
        $condition['roleid'] = $id;
        M('sys_role_user')->where($condition)->delete();
        $arr = explode(",", $userids);
        for ($i = 0; $i < count($arr); $i++) {
            if (!empty($arr[$i])) {
                $data['roleid'] = $id;
                $data['userid'] = $arr[$i];
                M('sys_role_user')->add($data);
            }
        }
    }

    public function index()
    {
        $this->assign('apps',M('sys_app')->select());

        if(!empty($_GET['name'])){
            $con['sys_role.name']=$_GET['name'];
        }
        if(!empty($_GET['appid'])){
            $con['appid']=$_GET['appid'];
        }

        $model = M('sys_role');
        $count = $model->where($con)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $model
            ->field("c.name as appname,sys_role.*")
            ->join('left join sys_app as c on c.id=sys_role.appid')
            ->where($con)->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function add()
    {
        $this->assign('apps',M('sys_app')->select());
        $this->assign('action', 'addEntity');
        $this->display('form');
    }

    public function addEntity()
    {
        $model=M('sys_role');
        $con['name']=$_REQUEST['name'];
        if($model->where($con)->count()>0) {
            echo('角色名称不可重复');
            return;
        }
        $model->add($_REQUEST);
    }

    public function deleteEntity($id)
    {
        M('sys_role')->delete($id);
    }

    public function edit($id)
    {
        $this->assign('apps',M('sys_app')->select());
        $this->assign('action', 'editEntity');
        $rs = M('sys_role')->where('id=' . $id)->find();
        $this->assign('entity', $rs);
        $this->display('form');
    }

    public function editEntity($id)
    {

        $model=M('sys_role');
        $con['name']=$_POST['name'];
        if($model->where($con)->count()>1) {
            echo('角色名称不可重复');
            return;
        }
        $model->where('id=' . $id)->save(array_filter($_POST));
    }
}