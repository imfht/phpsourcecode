<?php

/*
 * 基础默认公共方法
 */

class Base extends Eloquent {

    /**
     * 数组变成无限级分类--传引用思想
     * @param array $items
     * @return array
     */
    public static function get_tree($orig) {
        //解决下标不是1开始的问题
        $items = array();
        foreach ($orig as $key => $value) {
            $items[$value['id']] = $value;
        }
        //开始组装
        $tree = array();
        foreach ($items as $key => $item) {
            if ($item['pid'] == 0) {  //为0，则为1级分类
                $tree[] = &$items[$key];
            } else {
                if (isset($items[$item['pid']])) { //存在值则为二级分类
                    $items[$item['pid']]['child'][] = &$items[$key];  //传引用直接赋值与改变
                } else { //至少三级分类
                    //由于是传引用思想，这里将不会有值
                    $tree[] = &$items[$key];
                }
            }
        }

        return $tree;
    }

    /**
     * 无限级Html option输出
     * @global int $num  输出前的展示
     * @param array $trees  需要输出的数组
     */
    public static function outputOptionTree($trees, $currentId = null, $num = 0) {
        foreach ($trees as $tree) {
            echo '<option ' . self::outputOptionSelect($tree["id"], $currentId) . ' value="' . $tree["id"] . '">' . self::outputOptionLink($num) . $tree['title'] . '</option>';
            if (isset($tree['child'])) {
                $new_num = $num + 1;
                self::outputOptionTree($tree['child'], $currentId, $new_num);
            }
        }
    }

    //根据等级的多少输出条数
    public static function outputOptionLink($num) {
        $link = null;
        for ($i = 0; $i < $num; $i++) {
            $link .= '----';
        }
        return $link;
    }

    //是否选中
    public static function outputOptionSelect($treeId, $currentId) {
        $select = null;
        if ($treeId == $currentId) {
            $select = 'selected=""';
        }
        return $select;
    }

    /**
     * 无限级Html 输出菜单
     */

    /**
     * 
     * @global int $num
     * @param array $trees      当前需要输出的数组
     * @param array $parent     父级信息
     * @param string $type      类型，根据类型输出链接
     */
    public static function outputTrTree($trees, $parent, $type, $num = 0) {
        //global $num;
        foreach ($trees as $tree) {
            echo '<tr ' . self::outputTrClass($num) . '>
                        <td>' . $tree['title'] . '</td>
                        <td>
                            <input type="text" name="weight[' . $tree["id"] . ']" value="' . $tree["weight"] . '" width="50" maxlength="10"/>
                        </td>
                        <td>
                            <a href="/admin/' . $type . '/' . $parent["id"] . '/edit/' . $tree["id"] . '" class="btn btn-default btn-xs">编辑</a>
                            <a href="/admin/' . $type . '/' . $parent["id"] . '/delete/' . $tree["id"] . '" class="btn btn-default btn-xs">删除</a>
                        </td>
                    </tr>';
            if (isset($tree['child'])) {
                $new_num = $num + 1;
                self::outputTrTree($tree['child'], $parent, $type, $new_num);
            }
        }
    }

    //专门为分类写的
    public static function outputCategoryTrTree($trees, $parent, $type, $num = 0) {
        //global $num;
        foreach ($trees as $tree) {
            echo '<tr ' . self::outputTrClass($num) . '>
                        <td>
                            <a target="_blank" href="/category/' . $tree['id'] . '">
                            ' . $tree['title'] . '</a>
                        </td>
                        <td>
                            <input type="text" name="weight[' . $tree["id"] . ']" value="' . $tree["weight"] . '" width="50" maxlength="10"/>
                        </td>
                        <td>
                            <a href="/admin/' . $type . '/' . $parent["id"] . '/edit/' . $tree["id"] . '" class="btn btn-default btn-xs">编辑</a>
                            <a href="/admin/' . $type . '/' . $parent["id"] . '/delete/' . $tree["id"] . '" class="btn btn-default btn-xs">删除</a>
                        </td>
                    </tr>';
            if (isset($tree['child'])) {
                $new_num = $num + 1;
                self::outputTrTree($tree['child'], $parent, $type, $new_num);
            }
        }
    }

    public static function outputTrClass($num) {
        $class = 'class="interval-' . $num . '"';
        return $class;
    }

    /*
     * 中文截取，支持gb2312,gbk,utf-8,big5 
     * 
     * @param string $str 要截取的字串 
     * @param int $start 截取起始位置 
     * @param int $length 截取长度 
     * @param string $charset utf-8|gb2312|gbk|big5 编码 
     * @param $suffix 是否加尾缀 
     */

    public static function csubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
        if (function_exists("mb_substr")) {
            if (mb_strlen($str, $charset) <= $length) {
                return $str;
            }
            $slice = mb_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            if (count($match[0]) <= $length) {
                return $str;
            }
            $slice = join("", array_slice($match[0], $start, $length));
        }
        if ($suffix) {
            return $slice . "…";
        }
        return $slice;
    }

    /**
     * ----------------------------------------------------------------------
     * 获取今天、本周、本月的开始和结束时间
     * 
     */
    //今天
    public static function get_today_time() {
        $start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        //return array($start, $end);
        return $start;
    }

    //本周
    public static function get_week_time() {
        $start = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
        $end = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y'));
        //return array($start, $end);
        return $start;
    }

    //本月
    public static function get_month_time() {
        $start = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $end = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        //return array($start, $end);
        return $start;
    }

    /**
     * 
     * 文件夹操作
     * 遍历目录下面的所有文件夹名字
     * -----------------------------------------------------------
     */
    public static function get_file_list($dir) {
        if (is_dir($dir)) {
            $list = '';
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    //排除README文件
                    if ($file == 'README.txt') {
                        continue;
                    }
                    if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {
                        $list[] = $file;
                        //listDir($dir . "/" . $file . "/");
                    } else {
                        if ($file != "." && $file != "..") {
                            $list[] = $file;
                        }
                    }
                }
                closedir($dh);
            }
            return $list;
        }
    }

    /**
     * 
     * 获取前端主题app\views\frontend下面的所有主题
     * ---------------------------------------------
     */
    public static function get_all_themes() {
        $dir = dirname(__DIR__) . '/themes';
        $themes_folder = self::get_file_list($dir);
        $theme_list = array();

        foreach ($themes_folder as $row) {
            $info_dir = self::get_theme_dir($row, 'info');
            if (file_exists($info_dir)) {
                $theme = require_once $info_dir;
                $theme['machine_name'] = $row;
                if (file_exists('themes/' . $row . '/screenshot.png')) {
                    $theme['screenshot'] = '/themes/' . $row . '/screenshot.png';
                } else {
                    $theme['screenshot'] = '/upload/default/default_screenshot.png';
                }

                //两个数组合并
                $theme_list[] = $theme;
            } else {
                continue;
            }
        }

        return $theme_list;
    }

    /**
     * 
     * 获取modules下面的所有模块
     * ---------------------------------------------
     */
    public static function get_all_modules() {
        //获取数据库模块安装状态
        $setting_module_status = Setting::find('module_status');
        $module_status = unserialize($setting_module_status->value);
        //获取所有模块
        $dir = dirname(__DIR__) . '/modules';
        $modules_folder = self::get_file_list($dir);
        $module_list = array();
        foreach ($modules_folder as $row) {
            $model_json_dir = self::get_module_dir($row, 'json');
            $info_dir = self::get_module_dir($row, 'info');
            $install_dir = self::get_module_dir($row, 'install');
            //1、获取模块信息
            $module = require_once $info_dir;
            //2、获取模块开启状态
            $module['enabled'] = self::check_module_status($row);

            $package = $module['package'];
            $module['machine_name'] = $row;
            //追加数据库中的状态
            if (isset($module_status[$row])) {
                $module['install'] = $module_status[$row]['install'];
            } else {
                $module['install'] = false;
            }
            //检查是否具有安装文件
            if (file_exists($install_dir)) {
                $module['install_file'] = true;
            } else {
                $module['install_file'] = false;
            }
            //数组合并
            $module_list[$package][] = $module;
        }
        return $module_list;
    }

    /**
     * 检查哪些模块开启，哪些模块满足条件
     */
    public static function get_active_module() {
        $dir = dirname(__DIR__) . '/modules';
        $modules_folder = Base::get_file_list($dir);
        $module_list = array();
        foreach ($modules_folder as $module) {
            $module_php_dir = self::get_module_dir($module, 'module');

            if (!self::check_module_status($module)) {
                continue;
            }

            //2、检查是否存在module.php文件
            if (file_exists($module_php_dir)) {
                $module_list[] = $module;
            } else {
                continue;
            }
        }
        return $module_list;
    }

    /**
     * 检查单个模块状态
     * @param type $module模块名字
     * @return string 'ture'或者'false'
     */
    public static function check_module_status($module) {
        $model_json_dir = self::get_module_dir($module, 'json');
        if (file_exists($model_json_dir)) {
            $model_json = file_get_contents($model_json_dir);
            //处理获取的json文件
            $model_json = json_decode($model_json, true);
            $status = $model_json['enabled'];
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     * 创建文件
     */
    public static function create_file($file) {
        $fp = fopen("$file", "w+"); //打开文件指针，创建文件
        if (!is_writable($file)) {
            die("文件:" . $file . "不可写，请检查！");
        }
        fclose($fp); //关闭指针
    }

    /**
     * 获取模块信息
     * @param type $name模块名字
     * @param type $type需要获取的模块类型
     * @return string
     */
    public static function get_module_dir($name, $type) {
        switch ($type) {
            case 'info':
                $module_dir = dirname(__DIR__) . '/modules/' . $name . '/info.php';
                break;
            case 'json':
                $module_dir = dirname(__DIR__) . '/modules/' . $name . '/module.json';
                break;
            case 'install':
                $module_dir = dirname(__DIR__) . '/modules/' . $name . '/install.php';
                break;
            case 'module':
                $module_dir = dirname(__DIR__) . '/modules/' . $name . '/module.php';
                break;
            default :
                $module_dir = '';
        }
        return $module_dir;
    }

    /**
     * 获取主题信息
     * @param type $name主题名字
     * @param type $type需要获取的类型
     * @return string
     */
    public static function get_theme_dir($name, $type) {
        switch ($type) {
            case 'info':
                $theme_dir = dirname(__DIR__) . '/themes/' . $name . '/info.php';
                break;
            default :
                $theme_dir = '';
        }
        return $theme_dir;
    }

}
