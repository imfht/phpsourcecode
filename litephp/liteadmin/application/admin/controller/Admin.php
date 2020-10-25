<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/5
 * Time: 11:18
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\SystemAdmin;
use app\common\model\SystemAuthMap;
use app\common\model\SystemRole;
use think\Db;

/**
 * @title 后台管理员
 * Class SystemAdmin
 * @package app\admin\controller
 */
class Admin extends BaseAdmin
{
    /**
     * @title 管理员列表
     * @return mixed
     */
    public function index()
    {
        $db = SystemAdmin::where('is_deleted',0);

        $search = $this->request->get();
        foreach (['username'] as $field){
            if (isset($search[$field]) && $search[$field] !==''){
                $db->where($field, $search[$field]);
            }
        }

        return $this->_list($db);
    }

    /**
     * @title 添加操作
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new SystemAdmin(), 'form');
    }

    protected function _add_form_before(&$data){
        if ($this->request->isPost()){
            empty($data['name'])&&$this->error('请输入姓名');
            (strlen($data[SystemAdmin::username()])<6)&&$this->error('用户名长度必须大于6位');
            $has = SystemAdmin::where(SystemAdmin::username(), $data[SystemAdmin::username()])->count();
            $has&&$this->error("已经存在的用户名");
            !preg_match('/^[a-zA-Z0-9]+$/',$data[SystemAdmin::username()])&&$this->error('只能使用字母数字');
            $data['create_time'] = $this->request->time();
        }
    }

    /**
     * @title 编辑操作
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new SystemAdmin(), 'form');
    }

    protected function _edit_form_before(&$data)
    {
        (intval($data['id']) === 1) && $this->error("超级用户禁止修改！");
    }

    /**
     * @title 删除及批量删除
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $idarr = explode(',',$ids);
        if(in_array('1',$idarr)){
            $this->error("超级用户禁止删除！");
        }
        $this->_del(new SystemAdmin(),$ids);
    }

    /**
     * @title 修改密码
     * @return array|mixed
     */
    public function password()
    {
        return $this->_form(new SystemAdmin(), 'password');
    }

    protected function _password_form_before(&$data)
    {
        if ($this->request->isPost()) {
            $password = $data['password'];
            $repassword = $data['repassword'];

            (strlen($password) < 5 || strlen($password) > 25) && $this->error('密码长度必须5-25位之间');
            !preg_match('/^[a-zA-Z0-9]+$/', $password) && $this->error('只能使用字母数字');
            ($password !== $repassword) && $this->error('两次密码输入不一致');
            unset($data['repassword']);
            $data['password'] = auth_pwd_encrypt($data['password']);
        }
    }

    /**
     * @title 角色授权
     * @return mixed
     * @throws \think\Exception\DbException
     */
    public function role()
    {
        if ($this->request->isGet()) {
            $id = $this->request->route('id');
            $roles = SystemRole::all();
            $access = SystemAuthMap::where('admin_id', $id)
                ->column('role_id');
            $this->assign([
                'roles' => $roles,
                'access' => $access,
                'id' => $id
            ]);
            return $this->fetch();
        } elseif ($this->request->isPost()) {
            $roles = $this->request->post('role/a');
            $id = $this->request->post('id');
            $insert = [];
            if (empty($roles)) {
                SystemAuthMap::where('admin_id', $id)
                    ->delete();
                $this->success("操作成功！", '');
            }
            foreach ($roles as $key => $role) {
                $insert[] = ['admin_id' => $id, 'role_id' => $key];
            }
            Db::startTrans();
            try {
                $res1 = SystemAuthMap::where('admin_id', $id)
                    ->delete();
                $res2 = (new SystemAuthMap)->saveAll($insert);
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error("操作失败！", '', $e);
            }
            $this->success("操作成功！", '');
        }
    }

    /**
     * @title 禁用/启用
     */
    public function change()
    {
        $id = $this->request->post('id');
        (intval($id) === 1) && $this->error("超级用户禁止禁用！");
        $state = $this->request->post('state');
        $this->_change(new SystemAdmin(),$id, ['state' => $state]);
    }
}