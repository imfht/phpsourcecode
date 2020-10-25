<?php

/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/6 23:59
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */
namespace app\admin\model;


use app\admin\validate\UserValidate;
use think\exception\PDOException;
use think\Model;

class UserModel extends Model
{

    protected $pk='uid';

    protected $name = 'system_user';
    /**
     * 根据用户名查询用户以及用户权限
     * $param username
     * $param role_id
     */
    public function checkUser($UserName){
        return $this->alias('u')
            ->join('system_role r','u.role_id=r.role_id')
            ->where('u.username',$UserName)
            ->find();
    }

    /**
     * 更新管理员状态
     * @param array $param
     */
    public function updateStatus($param = [], $uid)
    {
        try{

            $this->where('uid', $uid)->update($param);
            return easymsg(1, '', 'ok');
        }catch (\Exception $e){
          return easymsg(-1, '', $e->getMessage());
        }
    }


    public function userlist($param){
        return $this->where($param)->paginate(10);
    }



    public function getUsersByWhere($where, $offset, $limit)
    {
        return $this->alias('u')->field( 'u.*,role_name')
            ->join('system_role rol', 'u.role_id = ' . 'rol.role_id')
            ->where($where)->limit($offset, $limit)->order('uid desc')->select();
    }

    public function getAllUsers($where)
    {
        return $this->where($where)->count();
    }


    public function add($data){
        try {
            $validate  = new UserValidate();
            if (!$validate->check($data)) {
                return easymsg(2,'',$validate->getError());
            }
            $data['creattime']=time();
            $data['password']=md5($data['password']);
            $data['login_ip']=request()->ip();
            $this->save($data);
            return easymsg(1,url('user/index'),'添加用户成功');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


    /*
     * 修改
     */
    public function edit($data){
        try {
            $this->save($data, ['uid' => $data['uid']]);
            return easymsg(1,url('user/index'),'修改成功！');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }

    /**
     * 根据ID查询用户相关信息
     */
    public function findById($id){
        return $this->alias('u')
            ->join('system_role r','u.role_id=r.role_id')
            ->join('system_dept s','u.dept_id=s.dept_id')
            ->where('u.uid',$id)
            ->find();
    }


    /**
     * 重置密码
     */

    public function editpwd($data){
        try {
            $data['password'] = md5($data['password']);
            $this->save($data, ['uid' => $data['uid']]);
            return easymsg(1,url('user/index'),'重置成功！');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


    public function del($id){
        try {
        if($id==1){
                return easymsg(-2,'','此用户为超级管理不允许删除！');
            }
            $this->where('uid',$id)->delete();
            return easymsg(1,url('user/index'),'删除成功！');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


    /**
     * 批量删除
     */

    public function batchRemove($data){
        try {
            $this->destroy($data);
            return easymsg(1,url('user/index'),'删除成功！');
        }catch (PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }

}