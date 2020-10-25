<?php

namespace app\install\controller;

use think\Session;
use think\View;
use think\Config;
use Storage\Storage;
use think\Request;
use think\Db;
use think\Url;

class Install {

    //引入jump类
    use \traits\controller\Jump;

    protected $view;

    public function __construct() {
        if (Storage::instance()->has(APP_PATH . 'install/data/install.lock')) {
            return $this->error('已经成功安装了本系统，请不要重复安装!');
        }
        $this->view = View::instance([], Config::get('replace_str'));
    }

    /**
     * 安装第一步，检测运行所需的环境设置
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function step1() {
        Session::set('error', false, 'install');
        //环境检测
        $env = check_env();
        //函数检测
        $func = check_func();
        Session::set('step', 1, 'install');
        $value = [
            'env' => $env,
            'func' => $func,
            'dirfile' => check_dirfile() ?? null//目录文件读写检测
        ];
        return $this->view->assign($value)->fetch();
    }

    /**
     * 安装第二步，创建数据库
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function step2($db = null, $admin = null) {
        if (Request::instance()->post()) {
            //检测管理员信息
            if (!is_array($admin) || empty($admin[0]) || empty($admin[1]) || empty($admin[3])) {
                return $this->error('请填写完整管理员信息');
            } else if ($admin[1] != $admin[2]) {
                return $this->error('确认密码和密码不一致');
            } else {
                $info = [];
                list($info['username'], $info['password'], $info['repassword'], $info['email']) = $admin;
                Session::set('admin_info', $info, 'install'); //缓存管理员信息
            }

            //检测数据库配置
            if (!is_array($db) || empty($db[0]) || empty($db[1]) || empty($db[2]) || empty($db[3])) {
                return $this->error('请填写完整的数据库配置');
            } else {
                $DB = [];
                list($DB['type'], $DB['hostname'], $DB['database'], $DB['username'], $DB['password'],
                        $DB['hostport'], $DB['prefix']) = $db;
                //缓存数据库配置
                Session::set('db_config', $DB, 'install');

                //创建数据库
                $dbname = $DB['database'];
                unset($DB['database']);
                $db = Db::connect($DB);
                $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
                $db->execute($sql) || $this->error($db->getError());
            }
            //跳转到数据库安装页面
            return $this->redirect('step3');
        } else {
            if (Session::get('update', 'install')) {
                Session::set('step', 2, 'install');
                return $this->view->fetch('update');
            } else {
                Session::get('error', 'install') && $this->error('环境检测没有通过，请调整环境后重试！');
                $step = Session::get('step', 'install');
                if ($step != 1 && $step != 2) {
                    return $this->redirect('step1');
                }
                Session::set('step', 2, 'install');
                return $this->view->fetch();
            }
        }
    }

    /**
     * 安装第三步，安装数据表，创建配置文件
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function step3() {
        if (Session::get('step', 'install') != 2) {
            return $this->redirect('step2');
            
        }
        echo $this->view->fetch();
        if (Session::get('update', 'install')===1) {
            show_msg('暂未开发!','error','javascript:history.go(-4);');
//            update_tables(Db::connect(), Config::get('database.prefix'));//更新数据表
        } else {
            //连接数据库
            $dbconfig = Session::get('db_config', 'install');
            $db = Db::connect($dbconfig);
            //创建数据表
            create_tables($db, $dbconfig['prefix']);
            //注册创始人帐号
            $auth = build_auth_key();
            $admin = Session::get('admin_info', 'install');
            register_administrator($db, $dbconfig['prefix'], $admin, $auth);
            //创建配置文件
            $conf = write_config($dbconfig, $auth);
            Session::set('config_file', $conf, 'install');
            $status = Session::get('error', 'install');
            if ($status === true) {
                show_msg('安装失败,请检查运行环境', 'error');
            } else {
            Session::set('step', 3, 'install');
            show_msg('安装成功,正在跳转', '', Url::build('Index/complete'));
            }
        }
    }

    /**
     * ajax执行安装步骤
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function runData() {
        if (Session::get('update', 'install')) {
            $db = Db::connect();
            //更新数据表
            update_tables($db, Config::get('database.prefix'));
        } else {
            //连接数据库
            $dbconfig = Session::get('db_config', 'install');
            $db = Db::connect($dbconfig);
            //创建数据表
            create_tables($db, $dbconfig['prefix']);
            //注册创始人帐号
            $auth = build_auth_key();
            $admin = Session::get('admin_info', 'install');
            register_administrator($db, $dbconfig['prefix'], $admin, $auth);
            //创建配置文件
            $conf = write_config($dbconfig, $auth);
            Session::set('config_file', $conf, 'install');
        }
        $status = Session::get('error', 'install');
        if ($status === true) {
            dump(Session::get('error', 'install'));
        }
        Session::get('step', 3, 'install');
        $this->redirect('Index/complete');
    }

}
