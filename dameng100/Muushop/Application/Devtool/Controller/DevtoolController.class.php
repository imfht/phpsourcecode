<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use OT\Database;
use ReflectionClass;
use Think\Db;

class DevtoolController extends AdminController
{
    protected $module;

    function _initialize()
    {
        S(array('type'=>'File','expire'=>600));
        $this->refreshS();
        $meta_title = '开发者工具';
        $this->assign('meta_title',$meta_title);
        parent::_initialize();
        
    }
    //获取模块信息
    private function refreshS()
    {
        $module = D('Module')->getModule(S('module'));
        $this->module = $module;
        $this->assign('module', $module);
    }

    public function module()
    {
        $modules = D('Common/Module')->getAll();
        foreach ($modules as $key => $v) {
            if ($v['is_setup']) {
                continue;
            }
            unset($modules[$key]);
        }

        $this->assign('modules', $modules);
        S('guide_menus', null);
        S('guide_default_rule',null);
        S('guide_auth_rule',null);
        S('guide_action',null);
        S('guide_action_limit',null);
        S('guide_sql_tables',null);
        S('guide_sql_rows',null);
        $this->display(T('Devtool@Admin/module'));
    }

    public function module1()
    {
        if (I('module', '', 'text') != '') {
            S('module',I('module', '', 'text'));
        }
        $this->refreshS();
        $menus = $this->getSubMenus(0);
        $all_menus = M('Menu')->where(array('status' => 1, 'module' => $this->module['name']))->select();
        $this->assign('menus', $menus);
        $controller_name = $this->module['name'];
        $path = APP_PATH . $controller_name . '/' . 'Controller' . '/' . $controller_name . 'Controller.class.php';
        if (file_exists($path)) {
            require_once($path);
            $controller = A('Admin/' . $controller_name);
            $methods = $this->get_class_all_methods($controller);
            foreach ($all_menus as &$v) {
                $v['url'] = strtolower($v['url']);
            }
            unset($v);
            $all_menus_url = getSubByKey($all_menus, 'url');
            foreach ($methods as $m) {
                if (!in_array(strtolower($this->module['name'] . '/' . $m['name']), $all_menus_url)) {
                    $havent_created[] = $m;
                }
            }

            $this->assign('havent_created', $havent_created);
            $this->assign('created', 1);

        }
        $this->display(T('Devtool@Admin/module1'));
    }

    public function module2()
    {
        $menus = I('post.menus', '', 'trim');
        if ($menus != ''){
            S('guide_menus',$menus);
        }

        $rules = M('Auth_rule')->where(array('module' => $this->module['name'], 'status' => 1))->select();

        $this->assign('rules', $rules);
        $this->display(T('Devtool@Admin/module2'));
    }

    public function module3()
    {
        $default = I('post.default', '', 'text');
        if ($default != '') {
            S('guide_default_rule',json_encode($default));
        }
        $auth_rule = I('post.auth_rule');
        if ($auth_rule != '') {
            S('guide_auth_rule',$auth_rule);
        }

        $action = M('Action')->where(array('module' => $this->module['name'], 'status' => 1))->select();
        $this->assign('action', $action);
        $action_limit = M('ActionLimit')->where(array('module' => $this->module['name'], 'status' => 1))->select();
        $this->assign('action_limit', $action_limit);
        $this->display(T('Devtool@Admin/module3'));
    }

    public function module4()
    {
        $action = I('post.action', '');
        if ($action) {
            S('guide_action',$action);
        }
        $action_limit = I('post.action_limit', '');
        if ($action_limit) {
            S('guide_action_limit',$action_limit);
        }

        $Db = Db::getInstance();
        $list = $Db->query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);

        $db_prefix = C('DB_PREFIX');
        $p = $db_prefix . strtolower($this->module['name']);
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
        $this->display(T('Devtool@Admin/module4'));
    }

    public function module5()
    {
        $sql_table = I('post.sql_tables', '');
        $sql_drop_table = I('post.sql_drop_table', '');
        $sql_rows = I('post.sql_rows', '');

        if ($sql_table) {
            S('guide_sql_tables',$sql_table);
        }
        if ($sql_table) {
            S('guide_sql_drop_table',$sql_drop_table);
        }
        if ($sql_rows) {
            S('guide_sql_rows',$sql_rows);
        }
        $guide = $this->getGuideContent();
        
        $this->assign('guide', $guide);

        $install = $this->getInstallContent();
        $this->assign('install', $install);
        $this->assign('cleanData', $sql_drop_table);
        $this->display(T('Devtool@Admin/module5'));
    }

    public function replace()
    {
        if (chmod(APP_PATH . $this->module['name'] . '/Info', 0777)) {
            $dir = 'Application/' . $this->module['name'] . '/Info';
            if (!rename($dir . '/install.sql', $dir . '/install.sql.bk')) {
                $info .= L('_FAIL_BACKUP_WITH_BR_',array('file'=>'install.sql'));
            };
            if (!rename($dir . '/guide.json', $dir . '/guide.json.bk')) {
                $info .= L('_FAIL_BACKUP_WITH_BR_',array('file'=>'guide_json'));
            };
            if (!rename($dir . '/cleanData.sql', $dir . '/cleanData.sql.bk')) {
                $info .= L('_FAIL_BACKUP_WITH_BR_',array('file'=>'cleanData.sql'));
            };
            if (!file_put_contents($dir . '/guide.json', json_encode($this->getGuideContent()))) {
                $info .= L('_FAIL_REPLACE_WITH_BR_',array('file'=>'guide.json'));
            };
            if (!file_put_contents($dir . '/install.sql', $this->getInstallContent())) {
                $info .=L('_FAIL_REPLACE_WITH_BR_',array('file'=>'install.sql'));
            };
            if (!file_put_contents($dir . '/cleanData.sql', $_SESSION['guide_sql_drop_table'])) {
                $info .= L('_FAIL_REPLACE_WITH_BR_',array('file'=>'cleanData.sql'));
            };

        } else {
            $this->error(L('_ERROR_FAIL_REPLACE_'));
        };
        $this->success($info);


    }


    public function download()
    {
        require_once("./ThinkPHP/Library/OT/PclZip.class.php");
        $zip = 'Runtime/Temp/' . $this->module['name'] . '.zip';
        $file_name = $this->module['name'] . '.zip';
        $archive = new \PclZip($zip);
        file_put_contents('Runtime/Temp/guide.json', json_encode($this->getGuideContent()));
        file_put_contents('Runtime/Temp/install.sql', $this->getInstallContent());
        file_put_contents('Runtime/Temp/cleanData.sql', $_SESSION['guide_sql_drop_table']);


        $v_list = $archive->create('Runtime/Temp/guide.json,Runtime/Temp/install.sql,Runtime/Temp/cleanData.sql',
            PCLZIP_OPT_REMOVE_PATH, 'Runtime/Temp',
            PCLZIP_OPT_ADD_PATH, 'Application/' . $this->module['name'] . '/Info');
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
        $tables = I('post.tables');
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
        $db = Db::getInstance();
        switch ($type) {
            case 1:
                if (0 == $start) {
                    $result = $db->query("SHOW CREATE TABLE `{$table}`");
                    $sql = "\n";
                    $sql .= "-- -----------------------------\n";
                    $sql .= "-- ".L('_TABLE_SCHEME_')." `{$table}`\n";
                    $sql .= "-- -----------------------------\n";
                    //$sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', trim($result[0]['Create Table']) . ";\n\n");
                    if (false === $this->write($sql)) {
                        return false;
                    }
                }

                //数据总数
                $result = $db->query("SELECT COUNT(*) AS count FROM `{$table}`");
                $count = $result['0']['count'];


                break;
            case 2:
                //写入数据注释
                if (0 == $start) {
                    $sql = "-- -----------------------------\n";
                    $sql .= "-- ".L('_TABLE_RECORDS_')." `{$table}`\n";
                    $sql .= "-- -----------------------------\n";
                    $this->write($sql);
                }

                //备份数据记录
                $result = $db->query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
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
                    $result = $db->query("SHOW CREATE TABLE `{$table}`");
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
                $result = $db->query("SELECT COUNT(*) AS count FROM `{$table}`");
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
                    $result = $db->query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");
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

    private function getSubMenus($pid = 0)
    {
        $menus = M('Menu')->where(array('status' => 1, 'module' => $this->module['name'], 'pid' => $pid))->select();
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
        $guide['menu'] =S('guide_menus');
        $guide['default_rule'] = S('guide_default_rule');
        $guide['auth_rule'] = S('guide_auth_rule');
        $guide['action'] = S('guide_action');
        $guide['action_limit'] = S('guide_action_limit');
        return $guide;
    }

    /**
     * @return string
     */
    private function getInstallContent()
    {
        $install = S('guide_sql_tables') . S('guide_sql_rows');
        return $install;
    }


}