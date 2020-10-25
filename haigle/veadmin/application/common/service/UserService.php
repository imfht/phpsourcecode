<?php
namespace app\common\service;

use app\common\model\RoleModel;
use app\common\model\UserModel;
use app\common\model\UserRoleModel;
use think\Db;
use utils\JWTUtils;

class UserService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userRoleModel = new UserRoleModel();
    }

    /**
     * [getUser 获取用户列表（危险操作，慎用）]
     * @author [haigle] [991382548@qq.com]
     * @return array
     */
    public function getUser()
    {
        return $this->userModel->getAllList();
    }

    /**
     * [del 删除用户（危险操作，慎用）]
     * @author [haigle] [991382548@qq.com]
     * @return boolean
     */
    public function del($id)
    {
        Db::startTrans();
        try {
            $this->userRoleModel->deleteDateByUserId($id);
            $this->userModel->deleteDate($id);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * [save 添加&修改用户]
     * @role 用户的权限
     * @data 用户信息
     * @author [haigle] [991382548@qq.com]
     * @return boolean
     */
    public function save($role, $data)
    {
        Db::startTrans();
        try {
            if(empty($data['id'])){
                $user_id = $this->userModel->insertDate($data);
                foreach($role as $item){
                    $user_role[] = ["user_id" => $user_id,"role_id" => $item,"created_at" => date("Y-m-d H:i:s",time())];
                }
                $this->userRoleModel->insertDate($user_role);
            }else{
                $this->userModel->saveDate($data);
                $this->userRoleModel->deleteDate($data['id']);
                foreach($role as $item){
                    $user_role[] = ["user_id" => $data['id'],"role_id" => $item,"created_at" => date("Y-m-d H:i:s",time())];
                }
                $this->userRoleModel->insertDate($user_role);
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * [getFind 获取个人信息]
     * @user_id 用户id
     * @author [haigle] [991382548@qq.com]
     * @return boolean & array
     */
    public function getFind($user_id)
    {
        Db::startTrans();
        try {
            $data = $this->userModel->getDetail($user_id);
            $roleModel = new RoleModel();
//            $god_id = session('auth')['id'];
            $god_id = JWTUtils::decode(session('auth'))->id;
            if($god_id == 1){
                $god_data = $roleModel->getAllList();
            }else{
                $god_data = $roleModel->getAllListByDate($god_id);
            }
            $source_data = $roleModel->getAllListByDate($user_id);
            foreach($god_data as $key => $val){
                if(empty($source_data)){
                    $god_data[$key]['checked'] = '';
                }else{
                    foreach($source_data as $item){
                        if($item['id'] == $val['id']){
                            $god_data[$key]['checked'] = 'checked';
                        }
                    }
                }
            }
            $data['role'] = $god_data;
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return $data;
    }
}