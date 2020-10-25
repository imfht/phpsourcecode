<?php
namespace app\admin\controller;


use utils\JsonUtils;
use app\common\service\AbilitiesService;

class Menu extends Base
{
    protected $abilitiesService;
    protected $jsonUtils;

    public function _we()
    {
        $this->jsonUtils = new JsonUtils();
        $this->abilitiesService = new AbilitiesService();
    }

    public function index()
    {
        $data = $this->abilitiesService->getLeftMenu();
        $this->assign('list',$data);
        return $this->fetch();
    }

    public function listMenu()
    {
        $data = $this->abilitiesService->getLeftMenu();
        return $this->jsonUtils->msgSuccess($data);
    }

    public function save()
    {
        $data['id']  = input('post.id');
        $data['name']  = input('post.name');
        $data['href']  = input('post.href');
        $data['parent_id'] = input('post.parent_id');
        $data['icon'] = input('post.icon');
        $data['sort'] = input('post.sort');
        $data['is_show'] = input('post.is_show');

        $request = $this->abilitiesService->save($data);
        if($request){
            return $this->jsonUtils->success();
        }
        return $this->jsonUtils->error();
    }

    public function get_find()
    {
        $id = input('get.id');

        $request = $this->abilitiesService->getFind($id);
        if($request){
            return $this->jsonUtils->msgSuccess($request);
        }
        return $this->jsonUtils->msgError();
    }

    public function del()
    {
        $id = input('get.id');
        $request = $this->abilitiesService->del($id);
        if($request){
            return $this->jsonUtils->success();
        }
        return $this->jsonUtils->error();
    }

    public function tree()
    {
        return $this->abilitiesService->roleAbilitiesTree();
    }

}
