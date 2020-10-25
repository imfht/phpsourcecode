<?php

/**
 * 应用开始事件处理类
 * Class AppStartEvent
 *
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class AppInitHook
{
    /**
     * 运行钓子
     *
     * @param $options
     */
    public function run(&$options)
    {
        // 检测安装
        if (!file_exists(APP_PATH . 'Install/Lock.php'))
        {
            if (MODULE != 'Install')
            {
                go(__ROOT__.'/index.php?m=Install&c=Index&a=index');
            }
        }

        if (session('user'))
        {
            //登录
            define('IS_LOGIN', true);
            //管理员
            define('IS_ADMIN', $_SESSION['user']['admin'] == 1);
            //超级管理员
            define('IS_SUPER_ADMIN', $_SESSION['user']['rid'] == 1);
            //站长
            define('IS_WEBMASTER', strtoupper($_SESSION['user']['username']) == strtoupper(C('WEB_MASTER')));
        }
        //登录
        define('IS_LOGIN', false);
        //管理员
        define('IS_ADMIN', false);
        //超级管理员
        define('IS_SUPER_ADMIN', false);
        //站长
        define('IS_WEBMASTER', false);

        //加载插件
        $this->loadAddons();
    }

    /**
     * 加载系统插件
     */
    private function loadAddons()
    {
        $data = S('hooks');
        if (!$data || DEBUG)
        {
            $hooks = M('hooks')->where("status=1")->getField('name,addons', true);

            if ($hooks)
            {
                foreach ($hooks as $key => $value)
                {
                    if ($value)
                    {
                        $map['status'] = 1;
                        $names = explode(',', $value);
                        $map['name'] = array('IN', $names);
                        $data = M('addons')->where($map)->getField('id,name');
                        if ($data)
                        {
                            $addons = array_intersect($names, $data);
                            Hook::add($key, $addons);
                        }
                    }
                }
            }
            $data = Hook::get();
            S('hooks', Hook::get());
        }
        Hook::import($data, false);
    }
}
