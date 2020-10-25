<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/11/2
 * Time: 11:27
 * ----------------------------------------------------------------------
 */

namespace app\install\controller;

use think\Controller;

/**
 * Class Install
 * 系统安装模块
 * @package app\install\controller
 */
class Install extends Controller
{
    public function initialize()
    {
        // 判断是否安装OCenter
        if (is_file(APP_PATH . 'admin/command/Install/install.lock')) {
            $this->redirect(url('admin/Index/index'));
        }
    }

    /**
     * 系统安装
     * @return mixed
     * @author:wdx(wdx@ourstu.com)
     */
    public function index()
    {
        $app = config('app.app_info');
        $alias = $app['alias'];
        if ($this->request->isPost()) {
            //环境监测
            //错误信息
            $errInfo = '';
            //数据库配置文件
            $dbConfigFile = ROOT_PATH . DS . 'config' . DS . 'database.php';
            // 安装包目录
            define('INSTALL_PATH', APP_PATH . 'admin' . DS . 'command' . DS . 'Install' . DS);
            //网站名
            $siteName = 'OCenter V3';
            // 锁定的文件
            $lockFile = INSTALL_PATH . 'install.lock';
            if (is_file($lockFile)) {
                $errInfo = "当前已经安装系统，如果需要重新安装，请手动移除application/admin/command/Install/install.lock文件";
            } else if (version_compare(PHP_VERSION, '5.6.0', '<')) {
                $errInfo = "当前版本(" . PHP_VERSION . ")过低，请使用PHP5.6以上版本";
            } else if (!extension_loaded("PDO")) {
                $errInfo = "当前未开启PDO，无法进行安装";
            } else if (!is_really_writable($dbConfigFile)) {
                $open_basedir = ini_get('open_basedir');
                if ($open_basedir) {
                    $dirArr = explode(PATH_SEPARATOR, $open_basedir);
                    if ($dirArr && in_array(__DIR__, $dirArr)) {
                        $errInfo = '当前服务器因配置了open_basedir，导致无法读取父目录<br>';
                    }
                }
                if (!$errInfo) {
                    $errInfo = '当前权限不足，无法写入配置文件config/database.php<br>';
                }
            }
            if ($errInfo) {
                $this->error($errInfo);
            }

            //接收数据
            $data = input('post.data/a', []);
            $err = '';
            $mysqlHostname = isset($data['mysqlHost']) ? $data['mysqlHost'] : '127.0.0.1';
            $mysqlHostport = 3306;
            $hostArr = explode(':', $mysqlHostname);
            if (count($hostArr) > 1) {
                $mysqlHostname = $hostArr[0];
                $mysqlHostport = $hostArr[1];
            }
            $mysqlUsername = isset($data['mysqlUsername']) ? $data['mysqlUsername'] : 'root';
            $mysqlPassword = isset($data['mysqlPassword']) ? $data['mysqlPassword'] : '';
            $mysqlDatabase = isset($data['mysqlDatabase']) ? $data['mysqlDatabase'] : 'ocenter';
            $mysqlPrefix = isset($data['mysqlPrefix']) ? $data['mysqlPrefix'] : 'oc_';
            $adminUsername = isset($data['adminUsername']) ? $data['adminUsername'] : 'admin';
            $adminPassword = isset($data['adminPassword']) ? $data['adminPassword'] : '123456';
            $adminPasswordConfirmation = isset($data['adminPasswordConfirmation']) ? $data['adminPasswordConfirmation'] : '123456';
            $adminMobile = isset($data['adminMobile']) ? $data['adminMobile'] : '12345678910';
            $adminEmail = isset($data['adminEmail']) ? $data['adminEmail'] : 'admin@admin.com';

            if ($adminPassword !== $adminPasswordConfirmation) {
                $err = "两次输入的密码不一致";
            } else if (!preg_match("/^\w+$/", $adminUsername)) {
                $err = "用户名只能输入字母、数字、下划线";
            } else if (!preg_match("/^[\S]+$/", $adminPassword)) {
                $err = "密码不能包含空格";
            } else if (strlen($adminUsername) < 3 || strlen($adminUsername) > 12) {
                $err = "用户名请输入3~12位字符";
            } else if (strlen($adminPassword) < 6 || strlen($adminPassword) > 16) {
                $err = "密码请输入6~16位字符";
            }
            if ($err) {
                $this->error($err);
            }
            try {
                //检测能否读取安装文件
                $sql = @file_get_contents(INSTALL_PATH . 'install.sql');
                if (!$sql) {
                    throw new \Exception("无法读取application/admin/command/Install/install.sql文件，请检查是否有读权限");
                }

                $sql = str_replace("`oc_", "`{$mysqlPrefix}", $sql);
                $pdo = new \PDO("mysql:host={$mysqlHostname};port={$mysqlHostport}", $mysqlUsername, $mysqlPassword, array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));

                //检测是否支持innodb存储引擎
                $pdoStatement = $pdo->query("SHOW VARIABLES LIKE 'innodb_version'");
                $result = $pdoStatement->fetch();
                if (!$result) {
                    throw new \Exception("当前数据库不支持innodb存储引擎，请开启后再重新尝试安装");
                }

                $pdo->query("CREATE DATABASE IF NOT EXISTS `{$mysqlDatabase}` CHARACTER SET utf8 COLLATE utf8_general_ci;");

                $pdo->query("USE `{$mysqlDatabase}`");

                $pdo->exec($sql);

                $config = @file_get_contents($dbConfigFile);
                $callback = function ($matches) use ($mysqlHostname, $mysqlHostport, $mysqlUsername, $mysqlPassword, $mysqlDatabase, $mysqlPrefix) {
                    $field = ucfirst($matches[1]);
                    $replace = ${"mysql{$field}"};
                    if ($matches[1] == 'hostport' && $mysqlHostport == 3306) {
                        $replace = '';
                    }
                    return "'{$matches[1]}'{$matches[2]}=>{$matches[3]}Env::get('database.{$matches[1]}', '{$replace}'),";
                };
                $config = preg_replace_callback("/'(hostname|database|username|password|hostport|prefix)'(\s+)=>(\s+)Env::get\((.*)\)\,/", $callback, $config);

                //检测能否成功写入数据库配置
                $result = @file_put_contents($dbConfigFile, $config);
                if (!$result) {
                    throw new \Exception("无法写入数据库信息到config/database.php文件，请检查是否有写权限");
                }

                //检测能否成功写入lock文件
                $result = @file_put_contents($lockFile, 1);
                if (!$result) {
                    throw new \Exception("无法写入安装锁定到application/admin/command/Install/install.lock文件，请检查是否有写权限");
                }
                $time = time();
                $newPassword = md5(sha1($adminPassword) . 'ThinkUCenter');
                $pdo->query("UPDATE {$mysqlPrefix}admin SET username = '{$adminUsername}', email = '{$adminEmail}',password = '{$newPassword}', mobile = '{$adminMobile}', update_time = '{$time}' WHERE username = 'admin'");
                $pdo->query("UPDATE {$mysqlPrefix}admin_auth_group SET create_time = '{$time}', update_time = '{$time}' WHERE id = 1");
                $pdo->query("UPDATE {$mysqlPrefix}admin_auth_group SET create_time = '{$time}', update_time = '{$time}' WHERE id = 2");
                $pdo->query("UPDATE {$mysqlPrefix}user_role SET create_time = '{$time}', update_time = '{$time}' WHERE id = 1");
                $pdo->query("UPDATE {$mysqlPrefix}user_role SET create_time = '{$time}', update_time = '{$time}' WHERE id = 2");
            } catch (\PDOException $e) {
                $err = $e->getMessage();
            } catch (\Exception $e) {
                $err = $e->getMessage();
            }
            if ($err) {
                $this->error($err);
            } else {
                $this->success('安装成功');
            }
        } else {

            $this->assign('app_info', $app);
            return $this->fetch();
        }
    }
}