<?php
namespace app\ebcms\behavior;
\think\Loader::import('controller/Jump', TRAIT_PATH, EXT);

class Auth
{
    use \traits\controller\Jump;

    public function run(&$params)
    {
        $prefix = \think\Config::get('database.prefix');
        $config = [
            'AUTH_GROUP' => $prefix . 'auth_group',
            'AUTH_ACCESS' => $prefix . 'auth_access',
            'AUTH_RULE' => $prefix . 'auth_rule',
            'AUTH_USER' => $prefix . 'manager',
            'AUTH_ON' => true,
            'AUTH_TYPE' => \ebcms\Config::get('system.base.auth_type'),
        ];
        $auth = new \ebcms\Auth($config);
        if (!\think\Session::get('super_admin')) {
            $node = request()->module() . '_' . request()->controller() . '_' . request()->action();
            if (!$auth->check($node, \think\Session::get('manager_id'))) {
                $this->error('没有权限！');
            }
        }
    }
}