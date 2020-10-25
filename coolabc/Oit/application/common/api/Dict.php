<?php
namespace app\common\api;

// 词典
use app\common\logic\EbaLogic;
use app\common\model\app\AppDictDef;
use app\common\model\app\AppDictSql;
use think\Db;
use think\Log;

/**
 * Class Dict
 * @package app\common\api
 */
class Dict {
    /**
     * 批量静态字典缓存
     */
    public static function init_dict_static(){
        $app_dict_def = new AppDictDef();
        $dict_data = $app_dict_def->order('dict_id,order_id')->select()->toArray();
        $dict_list = array_column($dict_data, 'dict_id');
        foreach($dict_list as $val){
            cache('dict.' . $val, array_column_equal_arr($dict_data, 'dict_id', [$val]));
        }
        return;
    }

    /**
     * 批量动态字典缓存
     * 在某些功能开始的时候，批量设置sql字典
     * @param $dict_list
     */
    public static function init_dict_sql($dict_list) {
        foreach($dict_list as $val) {
            self::refresh($val);
        }
        return;
    }

    /**
     * 刷新字典的缓存
     * @param null $dict_id
     * @return array|bool|mixed
     */
    public static function refresh($dict_id = null) {
        if ($dict_id == null) {
            return false;
        }
        $list = [];
        if (self::in_static($dict_id)) {
            // 0 静态字典，直接返回f静态字典内容
            $where['dict_id'] = $dict_id;
            $field = ['code', 'name'];
            $app_dict_def = new AppDictDef();
            $list = $app_dict_def->where($where)->field($field)->select()->toArray();
        } else {
            // 1 动态字典，查询动态字典数据
            // 注意 自定义的sql语句，把 id 与 name 写在最前面2列
            // 系统默认的 app_dict_sql 也是遵循这个规则的
            $app_dict_sql = new AppDictSql();
            $sql_info = $app_dict_sql->where(['dict_id' => $dict_id])->select()->toArray();
            $list = Db::query($sql_info[0]['sqlcmd']);
        }

        cache('dict.' . $dict_id, $list);
        return $list;
    }

    /**
     * 读取字典
     * @param        $dict_id
     * @return array
     */
    public static function get_dict($dict_id = null) {
        if ($dict_id == null) {
            return false;
        }
        $list = cache('dict.' . $dict_id);
        if(empty($list)) {
            $list = self::refresh($dict_id);
        }
        // 0 静态字典，直接返回f静态字典内容
        // 1 动态字典，再进行逻辑处理再返回
        if (false == self::in_static($dict_id)) {
            switch ($dict_id) {
                // 特殊有逻辑的动态字典
                case 'emp':
                    $list = EmpLogic::user_emp($list);
                    break;
                case 'emp_company':
                    $list = EmpLogic::user_emp_company($list);
                    break;
                case 'emp_dept':
                    break;
                case 'app_emp':
                    break;
                //case 'eba':
                //    $list = $list;
                //    break;
                case 'eba_service':
                    $list = EbaLogic::user_eba_service();
                    break;
                case 'sup':
                    break;
                case 'sup_service':
                    break;
                case 'edt':
                    break;
                case 'res':
                    break;
                case 'res_catalog':
                    break;
                default:
                    // 其他的动态字典
            }
        }
        return $list;
    }

    /**
     * 初始化静态字典列表并缓存
     * @return mixed
     */
    public static function init_static_list() {
        $list = Db::query("select dict_id from app_dict where dict_id not in(select dict_id from app_dict_sql) order by dict_id");
        cache('dict.static_list', array_column($list, 'dict_id'));
        return $list;
    }

    /**
     * 判断是否在静态字典列表中
     * @param $dict_id
     * @return bool
     */
    public static function in_static($dict_id) {
        $static_dict_list = cache('dict.static_list');
        return in_array($dict_id, $static_dict_list);
    }

    /**
     * 根据字典,字典项，获得选项名称
     * 所有字典都必须返回2列以上
     * @param $dict_id
     * @param $code
     * @return mixed
     */
    public static function get_name($dict_id, $code) {
        if (empty($dict_id) || empty($code)) {
            return lang('参数错误' . ' $dict_id, $code');
        }
        $dict_result = self::get_dict($dict_id);
        if (!empty($dict_result)) {
            $key = array_keys($dict_result[0]);
            foreach ($dict_result as $dict) {
                if ($code == $dict[$key[0]]) {
                    return $dict[$key[1]];
                }
            }
        }
        return false;
    }

    /**
     * 根据字典,选项名称，获得选项编码
     * 所有字典都必须返回2列以上
     * @param $dict_id
     * @param $name
     * @return mixed
     */
    public static function get_code($dict_id, $name) {
        if (empty($dict_id) || empty($name)) {
            return lang('参数错误' . ' $dict_id, $name');
        }
        $dict_result = self::get_dict($dict_id);
        if (!empty($dict_result)) {
            $key = array_keys($dict_result[0]);
            foreach ($dict_result as $dict) {
                if ($name == $dict[$key[1]]) {
                    return $dict[$key[0]];
                }
            }
        }
        return false;
    }


    /**
     * 在数据中的添加某一列的对应字典名称列
     * 一般用于某数组不需要权限过滤时
     * @param $dict_data
     * @param $data
     * @param $id
     * @param $name
     * @return mixed
     */
    public static function map_name($dict_data, $data, $id, $name) {
        foreach ($data as $k => $v) {
            foreach ($dict_data as $dv) {
                if ($v[$id] == $dv[$id]) {
                    $data[$k][$name] = $dv[$name];
                }
            }
        }
        return $data;
    }

    /**
     * 批量 给数据增加对应的字典名称列 可带权限控制
     * @param $dict_list // 一级数组: 字典列表, 二维数组: 字典数据
     * @param $dict_field_def
     * @param $data
     * @return mixed
     */
    public static function data_add_dict_name($dict_list, $dict_field_def, $data) {
        if (empty($dict_list) || empty($dict_field_def)) {
            return lang("缺少参数" . ' $dict_list || $dict_field_def');
        }

        // 注意返回的Dict::get_dict定义 主键\主键名 为最前面2列
        if (array_depth($dict_list) == 1) {
            $dict_data = [];
            foreach ($dict_list as $v) {
                $dict_data[$v] = self::get_dict($v);
            }
        } else {
            $dict_data = $dict_list;
        }

        $data_field = array_keys($data[0]);
        foreach ($dict_field_def as $v) {
            if (!in_array($v['field_id'], $data_field)) {
                continue;
            }
            $dict = $v['dict_id'];
            $dict_id = $v['field_id'];
            $curr_dict = $dict_data[$dict];
            if (array_key_exists('r_field_id', $v)) {
                $dict_name = $v['r_field_id'];
            } else {
                $dict_name = $v['field_id'] . '_name';
            }

            // 给数据添加字典列名的值
            if (self::in_static($dict)) {
                foreach ($data as $d_k => $d_v) {
                    foreach ($curr_dict as $dict_v) {
                        if ($d_v[$dict_id] == $dict_v['code']) {
                            $data[$d_k][$dict_name] = $dict_v['name'];
                        }
                    }
                }
            } else {
                $keys = array_keys($curr_dict[0]);
                $access_arr = array_column($curr_dict, $keys[0]);
                // 因为要减去权限外的数据
                for ($i = count($data) - 1; $i >= 0; $i--) {
                    $val = $data[$i][$dict_id];
                    if (empty($val) || $val == '-') {
                        continue;
                    }

                    // 不检查权限
                    /*
                    foreach ($curr_dict as $dict_v) {
                        if ($val == $dict_v[$keys[0]]) {
                            $data[$i][$dict_name] = $dict_v[$keys[1]];
                        }
                    }
                    */

                    // 把删除逻辑放置在程序中
                    if (in_array($val, $access_arr)) {
                        foreach ($curr_dict as $dict_v) {
                            if ($val == $dict_v[$keys[0]]) {
                                $data[$i][$dict_name] = $dict_v[$keys[1]];
                            }
                        }
                    } else {
                        if ($dict == 'sup' || $dict == 'eba') {
                            if ($dict == 'sup' && $data[$i]['eba_type'] == 'B') {
                                array_splice($data, $i, 1);
                            }
                            if ($dict == 'eba' && $data[$i]['eba_type'] == 'A') {
                                array_splice($data, $i, 1);
                            }
                        } else {
                            array_splice($data, $i, 1);
                        }
                    }
                }
            }
            array_merge($data, []);
        }

        return $data;
    }

}
