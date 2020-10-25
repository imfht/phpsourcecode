<?php

/*
 * 内容类型
 */

class Nodetype extends Eloquent {

    protected $table = 'node_type';
    protected $primaryKey = 'type';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('name', 'description', 'type');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = false;

    public static function get_node_type_fields($type) {
        //读取该内容类型中相关字段
        $node_type_fields = Fieldconfig::where('node_type', '=', $type)->orderBy('weight', 'asc')->get()->toArray();
        //处理数据
        foreach ($node_type_fields as $key => $field) {
            if ($field['field_type'] == 'category') {
                $field_data = json_decode($field['config_data']);
                //读取分类栏目内容
                $field_data->category;
                $category_info = Categorytype::where('machine_name', '=', $field_data->category)->firstOrFail()->toArray();
                $category_list = Category::where('tid', '=', $category_info['id'])->get()->toArray();
                $node_type_fields[$key]['category_list'] = Base::get_tree($category_list);
            }
            if ($field['field_type'] == 'checkbox') {
                $node_type_fields[$key]['config_data'] = self::get_field_checkbox($field['config_data']);
            }
            if ($field['field_type'] == 'radio') {
                $node_type_fields[$key]['config_data'] = self::get_field_checkbox($field['config_data']);
            }
            if ($field['field_type'] == 'select') {
                $node_type_fields[$key]['config_data'] = self::get_field_checkbox($field['config_data']);
            }
            if ($field['field_type'] == 'image') {
                $node_type_fields[$key]['config_data'] = self::get_field_image($field['config_data']);
            }
        }
        return $node_type_fields;
    }

    /**
     * checkbox复选框,radio单选框，select下拉列表
     */
    public static function get_field_checkbox($field_config_data) {
        //对checkbox类型进行处理
        $config_data = json_decode($field_config_data, true);
        //转义字符转化成空格方便处理
        $config_data['value'] = str_replace("\r\n", " ", $config_data['value']);
        $config_arr = explode(' ', $config_data['value']);

        //进行再处理生成数组
        $item_arr = array();
        foreach ($config_arr as $item) {
            //如果没有进行配置或者没有值则不能进行操作
            if ($item) {
                $item_value = explode('|', $item);
                $item_arr[$item_value[0]] = $item_value[1];
            }
        }
        return $item_arr;
    }

    /**
     * image图片上传
     */
    public static function get_field_image($field_config_data) {
        //对checkbox类型进行处理
        $config_data = json_decode($field_config_data, true);
        //print_r($config_data);die('hello');
        return $config_data;
    }

    /**
     * 获取所有内容类型，用于node内容筛选
     */
    public static function get_all_type_to_filter() {
        $node_type = Nodetype::all();
        $types = array();
        $types['0'] = '任意';
        foreach ($node_type as $row) {
            $types[$row['type']] = $row['name'];
        }
        return $types;
    }

}
