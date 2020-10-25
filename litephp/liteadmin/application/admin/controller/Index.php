<?php
namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\SystemAdmin;
use app\common\model\SystemMenu;
use Illuminate\Support\Arr;
use LiteAdmin\Tree;
use think\Db;
use think\facade\Cache;
use think\facade\Session;

/**
 * @title 后台主页
 * Class Index
 * @package app\admin\controller
 */
class Index extends BaseAdmin
{
    /**
     * @title 后台框架首页
     * @auth 0
     * @return mixed
     * @throws \throwable
     */
    public function index()
    {
        // 菜单数据 放入缓存
        $menus = Cache::remember("menus_".session('admin.id'),function (){
            $menus = Db::name('SystemMenu')
                ->order('sort', 'asc')
                ->select();
            $menus = Tree::array2tree($menus);
            // 过滤菜单匿名函数
            $func = function ($nodes) use (&$func){
                foreach ($nodes as $key => &$node){
                    if (!empty($node['_child'])){
                        $node['_child'] = $func($node['_child']);
                    }
                    if (strpos($node['url'],'#') === 0 ){
                        if (empty($node['_child'])){
                            unset($nodes[$key]);
                        }
                    }elseif (strpos($node['url'],'http://') === 0 || strpos($node['url'],'https://') === 0 ){
                        continue;
                    }else{
                        if (!auth($node['url'])){
                            unset($nodes[$key]);
                        }else{
                            $node['url'] = url($node['url']);
                        }
                    }
                }
                return $nodes;
            };
            $menus = $func($menus);
            return $menus;
        },1);
        $this->assign("menus",$menus);
        return $this->fetch();
    }

    /**
     * @title 修改密码
     * @auth 1
     * @return array|mixed
     */
    public function password()
    {
        return $this->_form(new SystemAdmin(), 'password');
    }

    /**
     * 设置密码前置
     * @param $data
     */
    protected function _password_form_before(&$data)
    {
        if (\think\facade\Request::isPost()) {
            $data = array_intersect_key($data, array_flip((array) ['id','password','repassword']));

            $data['id'] = session('admin.id');

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
     * @title 修改个人信息
     * @auth 1
     * @return array|mixed
     */
    public function editProfile()
    {
        return $this->_form(new SystemAdmin(), 'form');
    }

    /**
     * 编辑前置
     * @param $data
     */
    protected function _editprofile_form_before(&$data)
    {
        $data['id'] = session('admin.id');
        (intval($data['id']) === 1) && $this->error('超级用户禁止修改！');
        if ($this->request->isPost()){
            $data = array_intersect_key($data, array_flip((array) ['id','name']));
        }
    }

    protected function _editprofile_form_after(&$data)
    {
        unset($data['password']);
        Session::set('admin',$data);
    }

    /**
     * @auth 0
     * @title 仪表盘
     * @return mixed
     */
    public function dashboard()
    {
        return $this->fetch();
    }

    /**
     * @auth 1
     * @title 清理缓存
     */
    public function clearCache()
    {
        Cache::clear();
        return $this->success("操作成功！");
    }
}
