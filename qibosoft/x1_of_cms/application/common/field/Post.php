<?php
namespace app\common\field;

/**
 * 自定义字段 POST数据的转义
 */
class Post
{
    /**
     * 对POST数据进行筛选转义处理         注意:这里是针对于数据表的字段,即有qb_xxx_field字段的数据表
     * @param array $data POST数据
     * @param number $mid
     * @return \app\common\field\NULL|number
     */
    public static function format_all_field($data=[],$mid=0){
        $field_array = get_field($mid);
        foreach ($field_array as $rs) {
            $value = self::format($rs,$data);
            if($value!==null){     //这里要做个判断,MYSQL高版本,不能任意字段随意插入null
                $data[$rs['name']] = $value;
            }
        }
        return $data;
    }
    
    /**
     * 对POST数据进行筛选转义处理         注意:这里是针对于程序中定义的字段 数组下标是数字的情况,比如 ['uid', '用户名', 'username'],
     * @param array $data
     * @param array $field_array
     * @return \app\common\field\NULL|number
     */
    public static function format_php_all_field($data=[],$field_array=[]){
        $field_array = Format::form_fields($field_array);
        foreach ($field_array as $rs) {
            $value = self::format($rs,$data);
            if($value!==null){     //这里要做个判断,MYSQL高版本,不能任意字段随意插入null
                $data[$rs['name']] = $value;
            }
        }
        return $data;
    }
    
    /**
     * 对提交的数据某个字段分别处理
     * @param array $field
     * @param array $data
     * @return NULL|number
     */
    public static function format($field=[],$data=[]){
        $name = $field['name'];
        $type = $field['type'];
        if (!isset($data[$name])) {
            switch ($type) {
                // 开关
                case 'switch':
                    $data[$name] = 0;
                    break;
                case 'checkbox':
                    $data[$name] = '';
                    break;
            }
        } else {
            // 如果值是数组则转换成字符串，适用于复选框等类型
            if (is_array($data[$name])) {
                $data[$name] = implode(',', $data[$name]);
                $type == 'checkbox' && $data[$name] = ','.$data[$name] .',';   //方便搜索 like %,$value,%
            }
            switch ($type) {
                // 开关
                case 'switch':
                    $data[$name] = 1;
                    break;
                case 'images2':
                    //$data[$name] = json_encode(array_values($data['images2'][$name]));
                    break;
                    // 日期时间
                case 'date':
                //case 'time':
                case 'datetime':
                    $data[$name] = strtotime($data[$name]);
                    break;
            }
        }
        return isset($data[$name])?$data[$name]:null;   //这里要做个判断,MYSQL高版本,不能任意字段随意插入null
    }
}
