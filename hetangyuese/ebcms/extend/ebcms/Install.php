<?php
namespace ebcms;

class Install
{

    // 执行SQL文件
    public static function exec_sql_file($file)
    {
        $sqls = str_replace('{prefix}', \think\Config::get('database.prefix'), file_get_contents($file));
        $sqls = explode(PHP_EOL, \ebcms\Func::streol($sqls));
        $sql = '';
        foreach ($sqls as $key => $value) {
            $sql .= $value . PHP_EOL;
            if (preg_match('/.*;$/', trim($sql))) {
                if (false === \think\Db::execute($sql)) {
                    return false;
                }
                $sql = '';
            }
        }
        return true;
    }

    // 导入配置数据
    public static function imports($app)
    {
        $data = include APP_PATH.$app.'/install/config.php';
        $types = ['config','form','rule','menu','extend','nav'];
        foreach ($types as $key => $type) {
            if (isset($data[$type]) && $data[$type]) {
                $method = 'import_'.$type;
                $res[$type] = self::$method($app,$data[$type]);
            }
        }
        return true;
    }

    // 导入菜单
    private static function import_menu($app,$data){
        $table = \think\Db::name('menu') -> getTableInfo();
        self::level_insert('menu',$data,$app,'','',array_flip($table['fields']));
    }

    // 导入导航
    private static function import_nav($app,$data){
        $table = \think\Db::name('nav') -> getTableInfo();
        self::level_insert('nav',$data,$app,'','',array_flip($table['fields']));
    }

    // 导入节点规则
    private static function import_rule($app,$data){
        $table = \think\Db::name('auth_rule') -> getTableInfo();
        self::level_insert('auth_rule',$data,$app,'','',array_flip($table['fields']));
    }

    // 导入表单
    private static function import_extend($app,$data){
        $maintable = \think\Db::name('extend') -> getTableInfo();
        $subtable = \think\Db::name('extendfield') -> getTableInfo();
        $subtablefields = array_flip($subtable['fields']);
        foreach ($data as $k => $cate) {
            $ins = array_intersect_key($cate, array_flip($maintable['fields']));
            $ins['app'] = $app;
            unset($ins['id']);
            $cate_id = \think\Db::name('extend') -> insertGetId($ins);
            if (isset($cate['subs']) && $cate['subs']) {
                self::inserts('extendfield',$cate['subs'], '', $cate_id, $subtablefields);
            }
        }
    }

    // 导入表单
    private static function import_form($app,$data){
        $maintable = \think\Db::name('form') -> getTableInfo();
        $subtable = \think\Db::name('formfield') -> getTableInfo();
        $subtablefields = array_flip($subtable['fields']);
        foreach ($data as $k => $cate) {
            $ins = array_intersect_key($cate, array_flip($maintable['fields']));
            $ins['app'] = $app;
            unset($ins['id']);
            $cate_id = \think\Db::name('form') -> insertGetId($ins);
            if (isset($cate['subs']) && $cate['subs']) {
                self::inserts('formfield', $cate['subs'], '', $cate_id, $subtablefields);
            }
        }
    }

    // 导入配置
    private static function import_config($app,$data){
        $maintable = \think\Db::name('configcate') -> getTableInfo();
        $subtable = \think\Db::name('config') -> getTableInfo();
        $subtablefields = array_flip($subtable['fields']);
        foreach ($data as $k => $cate) {
            $ins = array_intersect_key($cate, array_flip($maintable['fields']));
            $ins['app'] = $app;
            unset($ins['id']);
            $cate_id = \think\Db::name('configcate') -> insertGetId($ins);
            if (isset($cate['subs']) && $cate['subs']) {
                self::inserts('config', $cate['subs'], '', $cate_id, $subtablefields);
            }
        }
    }

    // 插入数据
    private static function inserts($table, $data, $app='', $cate_id = '',$field=[])
    {
        foreach ($data as $key => $value) {
            $value = array_intersect_key($value, $field);
            if ($cate_id) {
                $value['category_id'] = $cate_id;
            }
            if ($app) {
                $value['app'] = $app;
            }
            unset($value['id']);
            \think\Db::name($table) -> insert($value);
        }
    }

    // 递归插入数据
    private static function level_insert($table, $data, $app='', $pid = '', $cate_id = '', $field=[])
    {
        foreach ($data as $key => $value) {
            $ins = array_intersect_key($value, $field);
            if ($pid) {
                $ins['pid'] = $pid;
            }
            if ($cate_id) {
                $ins['category_id'] = $cate_id;
            }
            if ($app) {
                $ins['app'] = $app;
            }
            unset($ins['id']);
            $insid = \think\Db::name($table) -> insertGetId($ins);
            if (isset($value['rows']) && $value['rows']) {
                self::level_insert($table, $value['rows'], $app, $insid, $cate_id, $field);
            }
        }
    }

    // 导出数据
    public static function exports($app)
    {
        $types = ['config','form','rule','menu','extend','nav'];
        $res = [];
        foreach ($types as $key => $type) {
            $method = 'export_'.$type;
            $res[$type] = self::$method($app);
        }
        $data = "<?php " . PHP_EOL . "if (!defined('THINK_PATH')) exit();" . PHP_EOL . "return " . var_export($res, true) . ';';
        file_put_contents(APP_PATH.$app.'/install/config.php', $data);
        return true;
    }

    // 导出数据字典
    private static function export_extend($app)
    {
        $where = [
            'app'    =>  $app
        ];
        $extend = \think\Db::name('extend')->where($where)->column(true, 'id');
        $extendfield = \think\Db::name('extendfield')->order('sort desc')->column(true, 'id');
        $temp = array();
        foreach ($extendfield as $key => $value) {
            $temp[$value['category_id']][] = $value;
        }
        foreach ($extend as $k => $v) {
            if (isset($temp[$k])) {
                $extend[$k]['subs'] = $temp[$k];
            }
        }
        return $extend;
    }

    // 导出菜单
    private static function export_menu($app)
    {
        $where = [
            'app'    =>  $app
        ];
        $tmp = \think\Db::name('menu')->where($where)->order('sort desc')->column(true, 'id');
        return \ebcms\Tree::tree($tmp);
    }

    // 导出导航
    private static function export_nav($app)
    {
        $where = [
            'app'    =>  $app
        ];
        $tmp = \think\Db::name('nav')->where($where)->order('sort desc')->column(true, 'id');
        return \ebcms\Tree::tree($tmp);
    }

    // 导出节点
    private static function export_rule($app)
    {
        $where = [
            'app'    =>  $app
        ];
        $tmp = \think\Db::name('auth_rule')->where($where)->order('sort desc')->column(true, 'id');
        return \ebcms\Tree::tree($tmp);
    }

    // 导出表单
    private static function export_form($app)
    {
        $where = [
            'app'    =>  $app
        ];
        $confcates = \think\Db::name('form')->where($where)->column(true, 'id');
        $tmp = \think\Db::name('formfield')->order('sort desc')->column(true, 'id');
        $confs = array();
        foreach ($tmp as $key => $value) {
            $confs[$value['category_id']][] = $value;
        }
        foreach ($confcates as $k => $v) {
            if (isset($confs[$k])) {
                $confcates[$k]['subs'] = $confs[$k];
            }
        }
        return $confcates;
    }

    // 导出配置
    private static function export_config($app)
    {
        $where = [
            'app'    =>  $app
        ];
        $confcates = \think\Db::name('configcate')->where($where)->column(true, 'id');
        $tmp = \think\Db::name('config')->where(array('status' => array('eq', 1)))->order('sort desc')->column(true, 'id');
        $confs = array();
        foreach ($tmp as $key => $value) {
            $confs[$value['category_id']][] = $value;
        }
        foreach ($confcates as $k => $v) {
            if (isset($confs[$k])) {
                $confcates[$k]['subs'] = $confs[$k];
            }
        }
        return $confcates;
    }

    // 卸载数据
    public static function delete($app)
    {
        $tables = [
            'configcate'=>'config',
            'form'=>'formfield',
            'extend'=>'extendfield'
        ];
        $where = [
            'app'    =>  $app
        ];
        foreach ($tables as $cate => $table) {
            if ($cateids = \think\Db::name($cate) -> where($where) -> column('id')) {
                \think\Db::name($table) -> where(['category_id'=>['in',$cateids]]) -> delete();
            }
        }

        $tables = ['configcate','form','extend','auth_rule','menu','nav'];
        $where = [
            'app'    =>  $app
        ];
        foreach ($tables as $key => $table) {
            \think\Db::name($table) -> where($where) -> delete();
        }
        return true;
    }

    public static function export_info($app){
        $where = [
            'name'    =>  $app,
        ];
        $res = \think\Db::name('dev_app') -> where($where) -> find();
        $file = APP_PATH.$app.'/install/info.php';
        $data = "<?php " . PHP_EOL . "if (!defined('THINK_PATH')) exit();" . PHP_EOL . "return " . var_export($res, true) . ';';
        file_put_contents($file, $data);
    }

}