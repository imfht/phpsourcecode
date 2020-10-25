<?php
namespace app\common\api;

use think\Request;

class Common {

    // 定义公共系统常量
    public static function def_system_var() {
        $request = Request::instance();
        // 兼容以前版本的常量
        define('MODULE_NAME', $request->module());
        define('CONTROLLER_NAME', $request->controller());
        define('ACTION_NAME', $request->action());
        define('IS_AJAX', $request->isAjax());
        define('IS_GET', $request->isGet());
        define('IS_POST', $request->isPost());
        return;
    }

    /**
     * 转换日期
     * ui 转 系统, yyyy-mm-dd 转 yyyymmdd
     * @param $data
     * @param $field // 需要转换的列
     * @return mixed
     */
    public static function date_ui_to_sys(&$data, $field) {
        $keys = array_keys($data[0]);
        foreach ($field as $val) {
            if (!in_array($val, $keys)) {
                continue;
            }
            foreach ($data as $k => $v) {
                $data[$k][$val] = date('Ymd', strtotime($data[$k][$val]));
            }
        }
    }

    /**
     * 转换日期
     * 系统 转 ui, yyyy-mm-dd 转 yyyymmdd
     * @param $data
     * @param $field // 需要转换的列
     * @return mixed
     */
    public static function date_sys_to_ui(&$data, $field) {
        $keys = array_keys($data[0]);
        foreach ($field as $val) {
            if (!in_array($val, $keys)) {
                continue;
            }
            foreach ($data as $k => $v) {
                $data[$k][$val] = date('Y-m-d', strtotime($data[$k][$val]));
            }
        }
    }


}
