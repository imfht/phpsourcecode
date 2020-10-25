<?php
namespace app\admin\controller;


use app\common\service\AbilitiesService;
use utils\JsonUtils;
use app\common\service\RoleService;

class Role extends Base
{
    protected $roleService;
    protected $abilitiesService;
    protected $jsonUtils;

    public function _we()
    {
        $this->roleService = new RoleService();
        $this->abilitiesService = new AbilitiesService();
        $this->jsonUtils = new JsonUtils();
    }

    public function index()
    {
        $data = $this->roleService->getRole();
        $this->assign('list',$data);
        return $this->fetch();
    }

    public function list_role()
    {
        $data = $this->roleService->getRole();
        return $this->jsonUtils->msgSuccess($data);
    }

    public function save()
    {
        $data['id']  = input('post.id');
        $data['name']  = input('post.name');
        $data['ename']  = input('post.ename');
        $data['usable'] = input('post.usable') == "on"?1:0;
        $data['role_type'] = input('post.role_type');
        $data['display_name'] = input('post.display_name');
        $data['description'] = input('post.description');

        $request = $this->roleService->save($data);
        if($request){
            return $this->jsonUtils->success();
        }
        return $this->jsonUtils->error();
    }

    public function get_find()
    {
        $id = input('get.id');

        $request = $this->roleService->getFind($id);
        if($request){
            return $this->jsonUtils->msgSuccess($request);
        }
        return $this->jsonUtils->msgError();
    }

    public function del()
    {
        $id = input('get.id');
        $request = $this->roleService->del($id);
        if($request){
            return $this->jsonUtils->success();
        }
        return $this->jsonUtils->error();
    }

    public function get_power()
    {
        $id = input('get.role_id');
//        $abilitiesService = new AbilitiesService();
        return $this->jsonUtils->msgSuccess($this->abilitiesService->roleAbilitiesTree($id));
//        return $abilitiesService->roleAbilitiesTree();
    }

    public function post_power()
    {
        $id = input('post.id');
        $type = input('post.type');
        $rule = input('post.rule');
        if($type == 'power'){
            $result = $this->abilitiesService->changeRoleAbilities($id, $rule);
            if($result == false){
                return $this->jsonUtils->error();
            }
            return $this->jsonUtils->success();
        }
        return $this->jsonUtils->error();
    }
}
