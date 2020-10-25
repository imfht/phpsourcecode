<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/9 9:49
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\model;


use app\admin\validate\RoleValidate;
use think\exception\PDOException;
use think\Model;

class RoleModel extends Model
{
    protected  $name="system_role";

    public function listRole(){
     $list = $this->order('role_id asc')->paginate(10);
     return $list;
    }


    /*
     * 添加角色
     */

    public function add($data){
        try {
            $validate  = new RoleValidate();
            if (!$validate->check($data)) {
                return easymsg(2,'',$validate->getError());
             }
            $this->save($data);
            return easymsg(1,url('role/index'),'添加角色成功');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


    /**
     * 修改
     */
    public function edit($data){
        try{
            $validate  = new RoleValidate();
            if (!$validate->scene('edit')->check($data)) {
                return easymsg(2,'',$validate->getError());
            }
            $this->save($data, ['role_id' => $data['role_id']]);
            return easymsg(1,url('role/index'),'修改角色成功');
        }catch (PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }

    /**
     * 删除
     */
    public function del($id){
        try {
            $this->where('role_id',$id)->delete();
            return easymsg(1,url('role/index'),'删除成功！');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }





    /**
     * 根据ID获得角色
     */
    public function findById($id){
        return $this->where('role_id',$id)->find();
    }
    /**
     * 查询所有角色
     */

    public function findByAll(){
        return $this->order('role_id asc')->select();
    }

}