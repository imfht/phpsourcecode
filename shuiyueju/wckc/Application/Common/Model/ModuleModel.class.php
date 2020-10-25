<?php
/**
 * 所属项目 110.
 * 开发者: 陈一枭
 * 创建日期: 2014-11-18
 * 创建时间: 10:27
 * 版权所有 想天软件工作室(www.ourstu.com)
 */

namespace Common\Model;


use Think\Model;

class ModuleModel extends Model
{

    protected $tableName = 'module';

    public function getAll()
    {

        $module = S('module_all');
        if (empty($module)) {
            $dir = $this->getFile(APP_PATH);
            foreach ($dir as $subdir) {
                if (file_exists(APP_PATH . '/' . $subdir . '/Info/info.php')) {
                    $info = $this->getInfo($subdir);
                    $info = $this->getModule($subdir);
                    $info['can_uninstall'] = file_exists(APP_PATH . '/' . $info['name'] . '/Info/uninstall.sql');
                    $module[] = $info;
                }
            }

            S('module_all', $module);
        }

        return $module;
    }

    public function checkCanVisit($name)
    {
        $modules = $this->getAll();

        foreach ($modules as $m) {
            if (isset($m['is_setup']) && $m['is_setup'] == 0 && $m['name'] == ucfirst($name)) {
                header("Content-Type: text/html; charset=utf-8");
                exit('您所访问的模块未安装，禁止访问。');
            }
        }

    }

    private function  cleanModulesCache()
    {
        S('module_all', null);
    }

    public function uninstall($id)
    {
        $module = $this->find($id);
        if ($module['is_setup'] == 0) {
            return array('error_code' => '模块未安装。');
        }
        $uninstallSql = APP_PATH . '/' . $module['name'] . '/Info/uninstall.sql';
        $res = D()->executeSqlFile($uninstallSql);

        if ($res === true) {
            $module['is_setup'] = 0;
            $this->save($module);
        }
        $this->cleanModulesCache();
        return $res;
    }

    public function install($id)
    {
        $module = $this->find($id);
        if ($module['is_setup'] == 1) {
            return array('error_code' => '模块已安装。');
        }
        $uninstallSql = APP_PATH . '/' . $module['name'] . '/Info/install.sql';
        $res = D()->executeSqlFile($uninstallSql);

        if ($res === true) {
            $module['is_setup'] = 1;
            $this->save($module);
        }
        clean_all_cache();//清除全站缓存
        return $res;
    }

    /**检查模块是否已安装
     * @param $name
     * @auth 陈一枭
     */
    public function getModule($name)
    {
        $module = $this->where(array('name' => $name))->find();
        if (!$module) {
            $m = $this->getInfo($name);
            $m['is_setup'] = 1;//默认设为已安装，防止已安装的模块反复安装。
            $m['id'] = $this->add($m);
            return $m;
        } else {
            return $module;
        }
    }

    private function getInfo($name)
    {
        $module = require(APP_PATH . '/' . $name . '/Info/info.php');
        return $module;
    }

    /**
     * 获取文件列表
     */
    private function getFile($folder)
    {
        //打开目录
        $fp = opendir($folder);
        //阅读目录
        while (false != $file = readdir($fp)) {
            //列出所有文件并去掉'.'和'..'
            if ($file != '.' && $file != '..') {
                //$file="$folder/$file";
                $file = "$file";

                //赋值给数组
                $arr_file[] = $file;

            }
        }
        //输出结果
        if (is_array($arr_file)) {
            while (list($key, $value) = each($arr_file)) {
                $files[] = $value;
            }
        }
        //关闭目录
        closedir($fp);
        return $files;


    }
} 