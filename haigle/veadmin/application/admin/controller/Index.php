<?php
namespace app\admin\controller;


use app\common\service\AbilitiesService;
use utils\JsonUtils;

class Index extends Base
{
    protected $abilitiesService;
    protected $jsonUtils;

    public function _we()
    {
        $this->abilitiesService = new AbilitiesService();
        $this->jsonUtils = new JsonUtils();
    }

    public function index()
    {
        $nav_menu = $this->abilitiesService->getLeftNavMenu();
        $this->assign('nav_menu', $nav_menu);
        return $this->fetch('/index');
    }

    public function dashboard()
    {
//        $abilitiesModel = new AbilitiesModel();
//        $abilitiesAuth = $abilitiesModel->trueAbilities(session('auth')['id']);
//        dump($abilitiesAuth);
//
//
//        $nav_menu = $this->abilitiesService->getLeftNavMenu();
//        return $this->jsonUtils->msgSuccess($nav_menu);
        return $this->fetch('/dashboard/dashboard');
    }


}
