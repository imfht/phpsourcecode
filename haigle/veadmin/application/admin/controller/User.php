<?php
namespace app\admin\controller;


use app\common\service\RoleService;
use utils\JsonUtils;
use app\common\service\UserService;
use utils\JWTUtils;

class User extends Base
{
    protected $userService;
    protected $jsonUtils;

    public function _we()
    {
        $this->jsonUtils = new JsonUtils();
        $this->userService = new UserService();
    }

    public function index()
    {
        $data = $this->userService->getUser();
        $this->assign('list',$data);

        $roleService = new RoleService();
        $this->assign('role',$roleService->getRole());
        return $this->fetch();
    }

    public function list_user()
    {
        $data = $this->userService->getUser();
        return $this->jsonUtils->msgSuccess($data);
    }

    public function del()
    {
        $id = input('get.id');
        $request = $this->userService->del($id);
        if($request){
            return $this->jsonUtils->success();
        }
        return $this->jsonUtils->error();
    }

    public function save()
    {
//        $role = input('post.roles/a');  // 接受数组值加"/a"
        $role = input('post.role/a');
        $data['id'] = input('post.id')?input('post.id'):null;
        $data['name'] = input('post.name');
        $data['email'] = input('post.email');
        $data['phone'] = input('post.phone');
        $data['password'] = JWTUtils::encode(input('post.password'));
//        $data['password'] = input('post.password');
        $data['birth_at'] = input('post.birth_at');
        $result = $this->userService->save($role, $data);
        if($result == false){
            return $this->jsonUtils->error();
        }
        return $this->jsonUtils->success();
    }

    public function get_find()
    {
        $id = input('get.id');
        $data = $this->userService->getFind($id);
//        dump($data);
        return $this->jsonUtils->msgSuccess($data);
    }
}
