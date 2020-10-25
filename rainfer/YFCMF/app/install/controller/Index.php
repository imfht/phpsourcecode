<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\install\controller;

use think\Controller;
use think\Db;
use think\facade\Env;

/*
 * 安装控制器
 * @Author: rainfer <rainfer520@qq.com>
 */

class Index extends Controller
{
    /**
     * 初始化
     *
     */
    protected function initialize()
    {
        parent::initialize();
        if (!defined('__ROOT__')) {
            define('__ROOT__', $this->request->rootUrl());
        }
        if (is_file(Env::get('root_path') . 'data/install.lock')) {
            header('Location: ' . url('home/Index/index'));
            exit();
        }
        $staticPath = __ROOT__ . '/public';
        $this->assign('static_path', $staticPath);
    }

    /**
     * 首页
     *
     */
    public function index()
    {
        session('step', 1);
        session('error', false);
        return $this->fetch(':index');
    }

    /**
     * 第2步
     *
     */
    public function step2()
    {
        if (!in_array(session('step'), [1, 2, 3])) {
            $this->error('请按顺序安装！', 'install/Index/index');
        }
        $data         = [];
        $icon_correct = '<i class="fa fa-check correct"></i> ';
        $icon_error   = '<i class="fa fa-close error"></i> ';
        //php版本、操作系统版本
        $data['phpversion'] = @phpversion();
        $data['os']         = PHP_OS;
        //环境检测
        $err = 0;
        if (class_exists('pdo')) {
            $data['pdo'] = $icon_correct . '已开启';
        } else {
            $data['pdo'] = $icon_error . '未开启';
            $err++;
        }
        //扩展检测
        if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = $icon_correct . '已开启';
        } else {
            $data['pdo_mysql'] = $icon_error . '未开启';
            $err++;
        }
        if (extension_loaded('curl')) {
            $data['curl'] = $icon_correct . '已开启';
        } else {
            $data['curl'] = $icon_error . '未开启';
            $err++;
        }
        if (extension_loaded('mbstring')) {
            $data['mbstring'] = $icon_correct . '已开启';
        } else {
            $data['mbstring'] = $icon_error . '未开启';
            $err++;
        }
        //设置获取
        if (ini_get('file_uploads')) {
            $data['upload_size'] = $icon_correct . ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = $icon_error . '禁止上传';
        }
        if (ini_get('allow_url_fopen')) {
            $data['allow_url_fopen'] = $icon_correct . '已开启';
        } else {
            $data['allow_url_fopen'] = $icon_error . '未开启';
            $err++;
        }
        //函数检测
        if (function_exists('file_get_contents')) {
            $data['file_get_contents'] = $icon_correct . '已开启';
        } else {
            $data['file_get_contents'] = $icon_error . '未开启';
            $err++;
        }
        if (function_exists('session_start')) {
            $data['session'] = $icon_correct . '已开启';
        } else {
            $data['session'] = $icon_error . '未开启';
            $err++;
        }
        //检测文件夹属性
        $checklist     = [
            'config/database.php',
            'config/yfcmf.php',
            'data',
            'runtime'
        ];
        $new_checklist = [];
        foreach ($checklist as $dir) {
            if (is_dir($dir)) {
                $testdir = "./" . $dir;
                create_dir($testdir);
                if (testwrite($testdir)) {
                    $new_checklist[$dir]['w'] = true;
                } else {
                    $new_checklist[$dir]['w'] = false;
                    $err++;
                }
                if (is_readable($testdir)) {
                    $new_checklist[$dir]['r'] = true;
                } else {
                    $new_checklist[$dir]['r'] = false;
                    $err++;
                }
            } else {
                if (is_writable($dir)) {
                    $new_checklist[$dir]['w'] = true;
                } else {
                    $new_checklist[$dir]['w'] = false;
                    $err++;
                }
                if (is_readable($dir)) {
                    $new_checklist[$dir]['r'] = true;
                } else {
                    $new_checklist[$dir]['r'] = false;
                    $err++;
                }
            }
        }
        session('step', 2);
        $data['checklist'] = $new_checklist;
        $this->assign($data);
        return $this->fetch(':step2');
    }

    /**
     * 第3步
     *
     */
    public function step3()
    {
        if (session('step') !== 2) {
            session('step', 1);
            $this->error('请按顺序安装！', 'install/Index/step2');
        } else {
            session('step', 3);
            return $this->fetch(':step3');
        }
    }

    /**
     * 第4步
     * @throws
     */
    public function step4()
    {
        if (session('step') !== 3) {
            $this->error('请按顺序安装！', 'install/Index/step3');
        }
        if (request()->isPost()) {
            session("step", 4);
            session('error', false);
            //数据库配置
            $dbconfig['type']     = input('dbtype');
            $dbconfig['hostname'] = input('dbhost');
            $dbconfig['username'] = input('dbuser');
            $dbconfig['password'] = input('dbpw');
            $dbconfig['hostport'] = input('dbport');
            $dbname               = strtolower(input('dbname'));
            // 创建数据库连接
            $db_instance = Db::connect($dbconfig);
            // 检测数据库连接
            try {
                $db_instance->execute('select version()');
            } catch (\Exception $e) {
                $this->error('数据库连接失败，请检查数据库配置！', 'install/Index/step3');
            }
            if (!input('dbcover')) {
                // 检测是否已存在数据库
                $result = $db_instance->execute('SELECT * FROM information_schema.schemata WHERE schema_name="' . $dbname . '"');
                if ($result) {
                    $this->error('该数据库已存在，请更换名称！如需覆盖，请选中覆盖按钮！', 'install/Index/step3');
                }
            }

            //建立数据库
            $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
            $db_instance->execute($sql) || $this->error($db_instance->getError(), 'install/Index/step3');
            //显示模板
            echo $this->fetch(':step4');
            //完整数据库配置
            $dbconfig['database'] = $dbname;
            $table_prefix         = trim(input('dbprefix'));
            $dbconfig['prefix']   = $table_prefix;
            //实例化数据库
            $db_instance = Db::connect($dbconfig);
            //运行sql
            execute_sql($db_instance, "yfcmf.sql", $table_prefix);
            //创建管理员
            create_admin_account($db_instance, $table_prefix);
            //生成网站配置文件
            create_config($dbconfig);
            if (session('error')) {
                session('step', 2);
                $this->error("安装失败", 'install/Index/step3');
            } else {
                showmsg("安装完成");
                return $this->fetch();
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     * 第5步
     *
     */
    public function step5()
    {
        if (session('step') === 4) {
            @touch('./data/install.lock');
            cookie('think_var', 'zh-cn');
            session(null);
            return $this->fetch(':step5');
        } else {
            $this->error("非法安装！", 'install/Index/index');
        }
    }

    /**
     * 测试数据库
     * @throws
     *
     */
    public function testdb()
    {
        if (request()->isPost()) {
            $dbconfig = input("post.");
            // 创建数据库连接
            $db_instance = Db::connect($dbconfig);
            // 检测数据库连接
            try {
                $db_instance->execute('select version()');
            } catch (\Exception $e) {
                $this->error('数据库连接失败，请检查数据库配置！');
            }
            $this->success('配置正确');
        } else {
            $this->error('访问方式不正确');
        }
    }

}
