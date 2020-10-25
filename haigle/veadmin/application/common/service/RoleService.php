<?php
namespace app\common\service;


use app\common\model\RoleAbilitiesModel;
use app\common\model\RoleModel;
use app\common\model\UserRoleModel;
use think\Db;
use utils\JWTUtils;

class RoleService
{
    protected $roleModel;
    protected $userRoleModel;
    protected $roleAbilitiesModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
        $this->roleAbilitiesModel = new RoleAbilitiesModel();
    }

    /**
     * [getRole 获取角色]
     * @author [haigle] [991382548@qq.com]
     * @return array
     */
    public function getRole()
    {
        $user_id = JWTUtils::decode(session('auth'))->id;
        if($user_id == 1){
            return $this->roleModel->getAllList();
        }else{
            $source_data = $this->roleModel->getAllListByDate($user_id);
            return $source_data;
        }
    }

    /**
     * [save 添加 & 修改角色]
     * @author [haigle] [991382548@qq.com]
     * @return array
     */
    public function save($data)
    {
        if(!empty($data['id'])){
            return $this->roleModel->saveDate($data);
        }
        return $this->roleModel->insertDate($data);
    }

    /**
     * [getFind 查询角色]
     * @author [haigle] [991382548@qq.com]
     * @return array
     */
    public function getFind($id)
    {
        return $this->roleModel->getFind($id);
    }

    /**
     * [del 删除角色]
     * @author [haigle] [991382548@qq.com]
     * @return boolean
     */
    public function del($id)
    {
        Db::startTrans();
        try {
            $this->roleModel->deleteDate($id);
            $this->userRoleModel->deleteDateByRoleId($id);
            $this->roleAbilitiesModel->deleteDateByRoleId($id);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }

}