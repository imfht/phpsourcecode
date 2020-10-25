<?php
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller
{
    public function configRole($id)
    {
        $role = M('sys_role')->select();
        $this->assign('roles', $role);
        $this->assign('id', $id);

        $Model = new \Think\Model();
        $roleids = $Model->query("select GROUP_CONCAT(roleid) as id from sys_role_user WHERE userid='" . $id . "' GROUP BY userid");
        $this->assign('roleids', $roleids[0]['id']);
        $this->display();
    }

    public function configRoleEntity($id, $roleids)
    {
        $condition['userid'] = $id;
        M('sys_role_user')->where($condition)->delete();
        $arr = explode(",", $roleids);
        for ($i = 0; $i < count($arr); $i++) {
            if (!empty($arr[$i])) {
                $data['roleid'] = $arr[$i];
                $data['userid'] = $id;
                M('sys_role_user')->add($data);
            }
        }
    }

    public function index()
    {
        $this->assign('apps',M('sys_app')->select());

        if(!empty($_GET['name'])){
            $con['sys_user.name']=$_GET['name'];
        }
        if(!empty($_GET['appid'])){
            $con['appid']=$_GET['appid'];
        }

        $model = M('sys_user');
        $count = $model->where($con)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $model
            ->field('c.name as appname,sys_user.*')
            ->join('left join sys_role_user as a on a.userid=sys_user.id')
            ->join('left join sys_role as b on a.roleid=b.id')
            ->join('left join sys_app as c on c.id=b.appid')
            ->where($con)->
            limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function add()
    {
        $this->assign('action', 'addEntity');
        $this->display('form');
    }

    public function addEntity()
    {
        M('sys_user')->add($_POST);
    }

    public function deleteEntity($id)
    {
        M('sys_user')->delete($id);
    }

    public function edit($id)
    {
        $this->assign('action', 'editEntity');
        $rs = M('sys_user')->where('id='.$id)->find();
        $this->assign('entity', $rs);
        $this->display('form');
    }

    public function editEntity($id)
    {
        M('sys_user')->where('id=' . $id)->save(array_filter($_POST));
    }
}