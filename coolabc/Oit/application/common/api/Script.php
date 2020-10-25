<?php
namespace app\common\api;

// 脚本
use app\common\model\app\AppObjSource;

class Script {
    /**
     *  查询某个对象的脚本
     *  param $obj_id
     * @param null $obj_id
     * @return array|string
     */
    public static function get_code_to_textarea($obj_id = null) {
        $obj_id ?: $obj_id = input('obj_id');
        if ($obj_id == null) {
            return '';
        }
        $where['obj_id'] = $obj_id;

        $app_obj_source = new AppObjSource();
        $array = $app_obj_source->where($where)->order('line_id')->select()->toArray();
        $textarea_code = array_column($array, 'source');
        /*
        foreach ($array as $key => $val) {
            $textarea_code[] = $val['source'];
        }
        */
        $textarea_code = implode("\r\n", $textarea_code);
        return $textarea_code;
    }

    /**
     *  查询某个对象的脚本
     *  param $obj_id
     * @param $obj_id
     * @return array|string
     */
    public static function get_code_to_js($obj_id = null) {
        $obj_id = $obj_id ? $obj_id : input('request.obj_id');
        if($obj_id == null ){
            return false;
        }
        $where['obj_id'] = $obj_id;

        $app_obj_source = new AppObjSource();
        $array = $app_obj_source->where($where)->order('line_id')->select()->toArray();
        //$js_code = array_column($array, 'source');
        $js_code = [];
        foreach ($array as $key => $val) {
            $js_code[] = addslashes($val['source']);
            //$js_code[] = $val['source'];
        }
        // js 代码中，用\n换行，用换行符,js 代码就可以注释
        // 如果不用换行，用空字符连接，那么运行速度可能会比较快，但不能注释
        $js_code = implode('\n', $js_code);
        // $jscode = implode('', $jscode);
        return $js_code;
    }

    /**
     * 保存代码
     * @param string       $obj_id
     * @param array|string $code
     * @return bool
     * @throws \Exception
     */
    public static function save($obj_id = '', $code = []) {
        $where['obj_id'] = $obj_id;
        $code_model = new AppObjSource();
        $save_data = [];
        foreach ($code as $key => $val) {
            $save_data[$key]['line_id'] = $key;
            $save_data[$key]['source'] = $val;
            $save_data[$key]['obj_id'] = $obj_id;
        }

        $code_model->where($where)->delete();
        $result = $code_model->saveAll($save_data);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}

