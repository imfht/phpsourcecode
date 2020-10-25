<?php
namespace app\devtool\controller;

use think\Db;
use app\admin\controller\Admin as MuuAdmin;
use reflectionclass;

class Admin extends MuuAdmin
{
    protected $module;

    function _initialize()
    {
        //cache(['type'=>'File','expire'=>600]);
        $this->refreshS();
        parent::_initialize();
        
    }
    //获取模块信息
    private function refreshS()
    {
        $module = model('Module')->getModule(cache('module'));
        $this->module = $module;
        $this->assign('module', $module);
    }

    public function module()
    {
        $modules = model('common/Module')->getAll();
        foreach ($modules as $key => $v) {
            if ($v['is_setup']) {
                continue;
            }
            unset($modules[$key]);
        }

        $this->assign('modules', $modules);
        cache('guide_menus', null);
        cache('guide_default_rule',null);
        cache('guide_auth_rule',null);
        cache('guide_action',null);
        cache('guide_action_limit',null);
        cache('guide_sql_tables',null);
        cache('guide_sql_rows',null);
        cache('guide_sql_drop_table',null);
        return $this->fetch('devtool@admin/module');
    }

    public function module1()
    {
        if (input('module', '', 'text') != '') {
            cache('module',input('module', '', 'text'));
        }
        $this->refreshS();
        $menus = $this->getSubMenus('0');
        $all_menus = Db::name('menu')->where(['module' => $this->module['name']])->select();
        
        $this->assign('menus', $menus);

        return $this->fetch('devtool@admin/module1');

    }

    public function module2()
    {
        $menus = input('post.menus', '', 'trim');
        if ($menus != ''){
            cache('guide_menus',$menus);
        }

        $rules = Db::name('Auth_rule')->where(['module' => $this->module['name'], 'status' => 1])->select();

        $this->assign('rules', $rules);
        return $this->fetch('devtool@admin/module2');
    }

    public function module3()
    {
        $default = input('post.default', '', 'text');
        if ($default != '') {
            cache('guide_default_rule',json_encode($default));
        }
        $auth_rule = input('post.auth_rule');
        if ($auth_rule != '') {
            cache('guide_auth_rule',$auth_rule);
        }

        $action = Db::name('Action')->where(['module' => $this->module['name'], 'status' => 1])->select();
        $this->assign('action', $action);
        $action_limit = Db::name('ActionLimit')->where(['module' => $this->module['name'], 'status' => 1])->select();
        $this->assign('action_limit', $action_limit);

        return $this->fetch('devtool@admin/module3');
    }

    public function module4()
    {
        $action = input('post.action', '');
        if ($action) {
            cache('guide_action',$action);
        }
        $action_limit = input('post.action_limit', '');
        if ($action_limit) {
            cache('guide_action_limit',$action_limit);
        }

        $list = Db::query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);

        $db_prefix = config('DB_PREFIX');
        $p = $db_prefix . strtolower($this->module['name']);
        $sql_table = '';
        $sql_drop_table = '';
        $sql_rows = '';
        $has_data = '';
        foreach ($list as $key => $v) {
            if (stripos(trim($v['name']), trim($p)) === false) {
                unset($list[$key]);
            } else {
                $this->sql = '';
                $this->backup_table($v['name'], 1);

                $sql_table .= $this->sql;
                $sql_drop_table .= $this->backup_drop_table($v['name']);
                if ($v['rows'] > 0) {
                    $this->sql = '';
                    $this->backup_table($v['name'], 2);
                    $sql_rows .= $this->sql;
                    $has_data[] = $v;
                }
            }
        }
        $this->assign('tables', $list);
        $this->assign('sql_tables', $sql_table);
        $this->assign('sql_drop_tables', $sql_drop_table);
        $this->assign('sql_rows', $sql_rows);
        $this->assign('has_data', $has_data);
        return $this->fetch('devtool@admin/module4');
    }

    public function module5()
    {
        $sql_table = input('post.sql_tables', '');
        $sql_drop_table = input('post.sql_drop_table', '');
        $sql_rows = input('post.sql_rows', '');

        if ($sql_table) {
            cache('guide_sql_tables',$sql_table);
        }
        if ($sql_drop_table) {
            cache('guide_sql_drop_table',$sql_drop_table);
        }
        if ($sql_rows) {
            cache('guide_sql_rows',$sql_rows);
        }
        $guide = $this->getGuideContent();
        
        $this->assign('guide', $guide);

        $install = $this->getInstallContent();
        $this->assign('install', $install);
        $this->assign('cleanData', $sql_drop_table);
        return $this->fetch('devtool@admin/module5');
    }
    /**
     * 替换安装文件
     * @return [type] [description]
     */
    public function replace()
    {
        if (is_writable(APP_PATH . $this->module['name'] . '/info')) {
            $dir = '../application/' . $this->module['name'] . '/info';
            $info = lang('_PACK_REPLACE_INSTALL_FILE_').lang('_SUCCESS_');

            if(file_exists($dir . '/install.sql')) {
                if (!rename($dir . '/install.sql', $dir . '/install.sql.bk')) {
                    $info = lang('_FAIL_BACKUP_WITH_BR_',['file'=>'install.sql']);
                    $this->error($info);
                }
            }

            if(file_exists($dir . '/guide.json')) {
                if (!rename($dir . '/guide.json', $dir . '/guide.json.bk')) {
                    $info = lang('_FAIL_BACKUP_WITH_BR_',['file'=>'guide_json']);
                    $this->error($info);
                }
            }

            if(file_exists($dir . '/cleanData.sql')) {
                if (!rename($dir . '/cleanData.sql', $dir . '/cleanData.sql.bk')) {
                    $info = lang('_FAIL_BACKUP_WITH_BR_',['file'=>'cleanData.sql']);
                    $this->error($info);
                }
            }

            if (!file_put_contents($dir . '/guide.json', json_encode($this->getGuideContent()))) {
                $info = lang('_FAIL_REPLACE_WITH_BR_',['file'=>'guide.json']);
                $this->error($info);
            }

            if ($this->getInstallContent()) {
                
                if(!file_put_contents($dir . '/install.sql', $this->getInstallContent())){
                    $info =lang('_FAIL_REPLACE_WITH_BR_',['file'=>'install.sql']);
                    $this->error($info);
                }
            }
            if (cache('guide_sql_drop_table')) {
                if (!file_put_contents($dir . '/cleanData.sql', cache('guide_sql_drop_table'))) {
                    $info = lang('_FAIL_REPLACE_WITH_BR_',['file'=>'cleanData.sql']);
                    $this->error($info);
                }
            }

        } else {
            $this->error(lang('_ERROR_FAIL_REPLACE_'));
        };
        $this->success($info);
    }

    /**
     * 打包下载zip
     * @return [type] [description]
     */
    public function download()
    {
        $zip = '../runtime/temp/' . $this->module['name'] . '.zip';
        $file_name = $this->module['name'] . '.zip';
        $archive = new \PclZip($zip);
        file_put_contents('../runtime/temp/guide.json', json_encode($this->getGuideContent()));
        file_put_contents('../runtime/temp/install.sql', $this->getInstallContent());
        file_put_contents('../runtime/temp/cleanData.sql', cache('guide_sql_drop_table'));

        $v_list = $archive->create('../runtime/temp/guide.json,../runtime/temp/install.sql,../runtime/temp/cleanData.sql',
            PCLZIP_OPT_REMOVE_PATH, '../runtime/temp',
            PCLZIP_OPT_ADD_PATH, 'application/' . $this->module['name'] . '/info');
        if ($v_list == 0) {
            die("Error : " . $archive->errorInfo(true));
        }
        header("Content-Description: File Transfer");
        header('Content-type: ' . 'zip');
        header('Content-Length:' . filesize($zip));
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
            header('Content-Disposition: attachment; filename="' . rawurlencode($file_name) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }
        readfile($zip);

    }

    public function backup_rows()
    {
        $tables = input('post.tables');
        foreach ($tables as $v) {
            $this->backup_table($v, 2);
        }
        echo $this->sql;
    }

    private function write($sql)
    {
        $this->sql .= $sql;
    }

    private function backup_drop_table($name)
    {
        return "DROP TABLE IF EXISTS `{$name}`;\n";
    }

    /**
     * @param int $type 备份类型，1:table,2:row,3:all
     */
    private function backup_table($table, $type = 1, $start = 0)
    {
        //$db = Db::getInstance();
        switch ($type) {
            case 1:
                if (0 == $start) {
                    $result = Db::query("SHOW CREATE TABLE `{$table}`");
                    $sql = "\n";
                    $sql .= "-- -----------------------------\n";
                    $sql .= "-- ".lang('_TABLE_SCHEME_')." `{$table}`\n";
                    $sql .= "-- -----------------------------\n";
                    //$sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', trim($result[0]['Create Table']) . ";\n\n");
                    if (false === $this->write($sql)) {
                        return false;
                    }
                }

                //数据总数
                $result = Db::query("SELECT COUNT(*) AS count FROM `{$table}`");
                $count = $result['0']['count'];


                break;
            case 2:
                //写入数据注释
                if (0 == $start) {
                    $sql = "-- -----------------------------\n";
                    $sql .= "-- ".lang('_TABLE_RECORDS_')." `{$table}`\n";
                    $sql .= "-- -----------------------------\n";
                    $this->write($sql);
                }

                //备份数据记录
                $result = Db::query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
                foreach ($result as $row) {
                    $row = array_map('addslashes', $row);
                    $sql = "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\r', '\n'), implode("', '", $row)) . "');\n";
                    if (false === $this->write($sql)) {
                        return false;
                    }
                }

                break;
            case 3:
                if (0 == $start) {
                    $result = Db::query("SHOW CREATE TABLE `{$table}`");
                    $sql = "\n";
                    $sql .= "-- -----------------------------\n";
                    $sql .= "-- Table structure for `{$table}`\n";
                    $sql .= "-- -----------------------------\n";
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= trim($result[0]['Create Table']) . ";\n\n";
                    if (false === $this->write($sql)) {
                        return false;
                    }
                }

                //数据总数
                $result = Db::query("SELECT COUNT(*) AS count FROM `{$table}`");
                $count = $result['0']['count'];

                //备份表数据
                if ($count) {
                    //写入数据注释
                    if (0 == $start) {
                        $sql = "-- -----------------------------\n";
                        $sql .= "-- Records of `{$table}`\n";
                        $sql .= "-- -----------------------------\n";
                        $this->write($sql);
                    }

                    //备份数据记录
                    $result = Db::query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
                    foreach ($result as $row) {
                        $row = array_map('addslashes', $row);
                        $sql = "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\r', '\n'), implode("', '", $row)) . "');\n";
                        if (false === $this->write($sql)) {
                            return false;
                        }
                    }

                    //还有更多数据
                    if ($count > $start + 1000) {
                        return array($start + 1000, $count);
                    }
                }

                break;
        }
    }

    private function get_class_all_methods($class)
    {
        $r = new reflectionclass($class);
        foreach ($r->getmethods() as $key => $methodobj) {
            if ($methodobj->isPublic() && $methodobj->class == $r->getName() && !in_array($methodobj->getName(), array('_initialize'))) {
                $methods[$key]['type'] = 'public';
                $methods[$key]['name'] = $methodobj->name;
                $methods[$key]['class'] = $methodobj->class;
            }
        }
        return $methods;
    }

    private function getSubMenus($pid='0')
    {
        $menus = Db::name('menu')->where(['module' => $this->module['name'], 'pid' => $pid])->select();
        if ($menus == null) {
            return;
        } else {
            foreach ($menus as &$m) {
                $m['_'] = $this->getSubMenus($m['id']);
            }

        }
        return $menus;
    }

    /**
     * @param $guide
     * @return mixed
     */
    private function getGuideContent($guide = '')
    {
        $guide['menu'] =cache('guide_menus');
        $guide['default_rule'] = cache('guide_default_rule');
        $guide['auth_rule'] = cache('guide_auth_rule');
        $guide['action'] = cache('guide_action');
        $guide['action_limit'] = cache('guide_action_limit');
        return $guide;
    }

    /**
     * @return string
     */
    private function getInstallContent()
    {
        $install = cache('guide_sql_tables') . cache('guide_sql_rows');
        return $install;
    }


}