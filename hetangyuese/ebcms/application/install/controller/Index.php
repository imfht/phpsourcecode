<?php
namespace app\install\controller;

use think\Controller;

class Index extends Controller
{
    public function _initialize()
    {
        if (is_file(CONF_PATH . 'install.lock')) {
            $this->error('请不要重复安装！');
        }
    }

    public function index()
    {
        $step = request()->request('step');
        if (is_numeric($step)) {
            $this->assign('steps', ['安装协议', '环境检查', '站点配置', '系统安装', '安装成功']);
            $this->step = $step;
            $step = 'step' . $step;
            $this->tpl = $step;
            $this->success('', '', $this->$step());
        } else {
            return $this->fetch();
        }
    }

    protected function step0()
    {
        if (request()->isGet()) {
            return $this->fetch($this->tpl);
        }
    }

    protected function step1()
    {
        if (request()->isGet()) {
            \think\Session::set('error', 0);
            $this->assign('check_env', check_env());
            $this->assign('check_dirfile', check_dirfile());
            $this->assign('check_func', check_func());
            $this->assign('error', \think\Session::get('error'));
            return $this->fetch($this->tpl);
        }
    }

    protected function step2()
    {
        if (request()->isGet()) {
            return $this->fetch($this->tpl);
        } elseif (request()->isPost()) {
            $input = input();
            $manager_password = $input['manager_password'];
            if ($manager_password != $input['manager_password2']) {
                $this->error('两次密码输入不一样！请确认！');
            }
            if (strlen($manager_password) > 10 || strlen($manager_password) < 5) {
                $this->error('密码长度不符合要求！');
            }
            $dbconfig = array(
                'type' => 'mysql',
                'hostname' => input('hostname','',null),
                'database' => input('database','',null),
                'hostport' => $input['hostport'],
                'username' => input('username','',null),
                'password' => input('password','',null),
                'params' => [],
                'charset' => 'utf8',
                'prefix' => $input['prefix'],
            );
            $db = \think\Db::connect($dbconfig);
            $sql1 = "CREATE TABLE IF NOT EXISTS `" . $dbconfig['prefix'] . 'test' . "` (id int NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),ebcmstest varchar(15));";
            $sql2 = "DROP TABLE IF EXISTS `" . $dbconfig['prefix'] . 'test' . "`;";
            if (0 != $db->execute($sql1) || 0 != $db->execute($sql2)) {
                $this->error('数据库配置错误！');
            }
            $dbdemo = $input['demodatabase'];
            \think\Session::set('dbdemo', $dbdemo);
            \think\Session::set('dbconfig', $dbconfig);
            \think\Session::set('manager_password', $input['manager_password']);
            $input['manager_email'] = $input['manager_email'] ?: '1540837821@qq.com';
            \think\Session::set('manager_email', $input['manager_email']);
            $this->success('配置成功！');
        }
    }

    protected function step3()
    {
        if (request()->isGet()) {
            return $this->fetch($this->tpl);
        } elseif (request()->isPost()) {
            $process = input('process');
            $dbconfig = \think\Session::get('dbconfig');
            switch ($process) {
                case 'createdatabase':
                    if (!$dbconfig) {
                        $this->error('数据库配置错误！');
                    }
                    $tables = '安装数据...<br>';
                    if (\think\Session::get('dbdemo')) {
                        $tables .= '载入演示数据库...<br>';
                        $sql = file_get_contents(APP_PATH . 'install/data/install_demo.sql');
                    } else {
                        $tables .= '载入数据库...<br>';
                        $sql = file_get_contents(APP_PATH . 'install/data/install.sql');
                    }
                    $sql = str_replace('ebcms5_', $dbconfig['prefix'], $sql);
                    $ret = array();
                    $num = 0;
                    $sqls = explode(";" . PHP_EOL, \ebcms\Func::streol(trim($sql)));
                    foreach ($sqls as $query) {
                        $ret[$num] = '';
                        $queries = explode(PHP_EOL, trim($query));
                        foreach ($queries as $v) {
                            $ret[$num] .= (isset($v[0]) && $v[0] == '#') || (isset($v[1]) && isset($v[1]) && $v[0] . $v[1] == '--') ? '' : $v;
                        }
                        $num++;
                    }
                    $tables .= '创建数据表...<br>';
                    $db = \think\Db::connect($dbconfig);
                    foreach ($ret as $key => $value) {
                        if ($value) {
                            $db->execute($value);
                            if (substr($value, 0, 12) == 'CREATE TABLE') {
                                $tables .= '创建数据表...' . preg_replace("/CREATE TABLE `?([a-z0-9_]+)`? .*/is", "\\1", $value) . '<br>';
                            }
                        }
                    }
                    $tables .= '创建数据库...完成!';
                    $result = array(
                        'info' => $tables,
                        'process' => 'updatedbconfig',
                    );
                    return $result;
                    break;

                case 'updatedbconfig':
                    if (!$dbconfig) {
                        $this->error('数据库配置错误！');
                    }
                    $info = '更新系统配置...<br>';
                    $manager_password = \think\Session::get('manager_password');
                    $manager_email = \think\Session::get('manager_email');
                    $sqls = [];
                    $sqls[] = 'update `' . $dbconfig['prefix'] . 'manager` set `email` = "' . $manager_email . '" where `id`=1;';
                    $sqls[] = 'update `' . $dbconfig['prefix'] . 'manager` set `password` = "' . \ebcms\Func::crypt_pwd($manager_password, $manager_email) . '" where `id`=1;';
                    $db = \think\Db::connect($dbconfig);
                    foreach ($sqls as $sql) {
                        $db->execute($sql);
                    }
                    // 更新数据库配置文件
                    $dbconfig_old = file_get_contents(APP_PATH . 'install/data/database.php');
                    $dbconfig = str_preg_parse($dbconfig_old, $dbconfig, true);
                    file_put_contents(CONF_PATH . 'database.php', $dbconfig);
                    // 更新配置文件
                    $config_old = file_get_contents(APP_PATH . 'install/data/config.php');
                    $config = str_preg_parse($config_old, [
                        'super_admin' => $manager_email,
                        'safe_code' => md5(serialize($dbconfig) . rand()),
                        'session_prefix' => md5(serialize($dbconfig) . rand() . '_e'),
                        'cache_prefix' => md5(serialize($dbconfig) . rand() . '_b'),
                        'cookie_prefix' => md5(serialize($dbconfig) . rand() . '_c'),
                    ]);
                    file_put_contents(CONF_PATH . 'config.php', $config);
                    // 更新tags文件
                    $tags = file_get_contents(APP_PATH . 'install/data/tags.php');
                    file_put_contents(CONF_PATH . 'tags.php', $tags);
                    // 更新route文件
                    $tags = file_get_contents(APP_PATH . 'install/data/route.php');
                    file_put_contents(CONF_PATH . 'route.php', $tags);

                    $info .= '更新系统配置...完成！<br>';

                    \ebcms\Func::deldir(TEMP_PATH);
                    \ebcms\Func::deldir(LOG_PATH);
                    \think\Cache::clear();
                    $info .= '删除缓存！<br>';

                    file_put_contents(CONF_PATH . 'install.lock', '#' . date('Y-m-d H:i:s'));
                    $info .= '生成安全文件！<br>';
                    $info .= '安装成功！';

                    $result = array(
                        'info' => $info,
                        'process' => 'end',
                    );
                    return $result;
                    break;
            }
        }
    }

}