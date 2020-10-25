<?php

/*
 * 内容
 */

class Node extends Eloquent {

    protected $table = 'node';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('type', 'uid', 'title', 'body', 'status', 'comment', 'view', 'promote', 'sticky', 'plusfine');

    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    //public $timestamps = false;

    /**
     * -----------------------------------------------------------------------
     * 1对1关系模型
     * 关联内容类型
     * @return type
     */
    public function nodeType() {
        return $this->hasOne('Nodetype', 'type', 'type');
    }

    /**
     * 1对1关系模型
     * 关联用户表
     * @return type
     */
    public function user() {
        return $this->hasOne('User', 'id', 'uid');
    }

    /**
     * 通过nid获取所有node相关信息
     * @param int $nid
     * @return array
     */
    public static function load_single($nid) {
        //读取主内容
        $node = Node::find($nid);
        //读取该内容类型中相关字段
        //$node_type_fields = Fieldconfig::where('node_type', '=', $node['type'])->orderBy('weight', 'asc')->get()->toArray();

        $node_fields = Nodefield::where('nid', '=', $node['id'])->orderBy('weight', 'asc')->get()->toArray();
        if (empty($node_fields)) {
            //还没有找到错误原因，暂时放到这里
        }

        foreach ($node_fields as $key => $item) {
            //如果已经删除了该字段，则不再关联
            try {
                $single_field = Fieldconfig::where('field_name', '=', $item['field_name'])->firstOrFail()->toArray();
                $node_fields[$key]['label'] = $single_field['label'];
                $node_fields[$key]['field_type'] = $single_field['field_type'];
            } catch (Exception $exc) {
                unset($node_fields[$key]);
            }

            //分类类型特殊处理
            if ($node_fields[$key]['field_type'] == 'category') {
                $category_info = Category::find($item['value'])->get()->toArray();
                $node_fields[$key]['category_list'] = $category_info;
            }
            //checkbox复选框特殊处理
            if ($node_fields[$key]['field_type'] == 'checkbox') {
                $field_config = Fieldconfig::where('field_name', '=', $item['field_name'])->firstOrFail()->toArray();
                $config_data = json_decode($field_config['config_data'], true);
                //转义字符转化成空格方便处理
                $config_data['value'] = str_replace("\r\n", " ", $config_data['value']);
                $config_arr = explode(' ', $config_data['value']);
                //进行再处理生成数组
                foreach ($config_arr as $item) {
                    //如果没有进行配置或者没有值则不能进行操作
                    if ($item) {
                        $item_value = explode('|', $item);
                        $item_arr[$item_value[0]] = $item_value[1];
                    }
                }
                $node_fields[$key]['config_data'] = $item_arr;
            }
        }
        $node['fields'] = $node_fields;
        return $node;
    }

    /**
     * 获取前端所有需要显示的内容
     * @param int $nid  内容ID
     * @return array
     */
    public static function load_all($nid) {
        $node = self::load_single($nid);
        $category = array();
        //获取作者信息
        $user = User::find($node['uid'])->toArray();

        //常用变量组装
        //作者
        $author = '<a href="/user/' . $user['id'] . '">' . $user['username'] . '</a>';
        //内容
        $content = null;
        $content .= $node['body'];
        foreach ($node['fields'] as $field) {
            //1、分类类型特殊处理
            if ($field['field_type'] == 'category') {
                $category_info = Category::where('id', $field['value'])->first()->toArray();
                $category_info['url'] = '/category/' . $category_info['id'];
                $category[] = $category_info;
                continue;
                //$field['value'] = '<a href="/category/' . $category_info['id'] . '">' . $category_info['title'] . '</a>';
            }
            //2、图片类型特殊处理
            if ($field['field_type'] == 'image') {
                $new_vale = explode(',', $field['value']);
                $field['value'] = '';
                foreach ($new_vale as $row) {
                    $field['value'] .= '<img src="' . $row . '" class="image">';
                }
            }
            //3、title和body为默认字段，默认不输出内容
            if ($field['field_name'] != 'body') {
                $content .= '<label>' . $field['label'] . '</label>';
            }

            $content .= '<p>' . $field['value'] . '</p>';
        }

        $node['category'] = $category;

        //数据组装
        $data = array(
            'node' => $node,
            'id' => $node['id'],
            'title' => $node['title'],
            'content' => $content, //Base::substr_cut($content, 500), //字符串截取，默认只取500个字符
            'url' => '/node/' . $node['id'],
            'author' => $author,
            'promote' => $node['promote'], //首页
            'sticky' => $node['sticky'], //置顶
            'plusfine' => $node['plusfine'], //精华
            'view' => $node['view'], //浏览数
            'comment' => $node['comment'], //评论数
            'created' => $node['created_at'], //创建时间
            'category' => $node['category']     //分类信息
        );


        /**
         * 钩子
         * hook_node_load
         */
        $data = Hook_node::node_load($data);

        return $data;
    }

    /** ======================================================
     * 编辑的时候读取数据
      ======================================================= */

    /**
     * 通过nid获取所有node相关信息
     * @param int $nid
     * @return array
     */
    public static function load_for_edit($nid) {
        //读取主内容
        $node = Node::find($nid)->toArray();
        //读取该内容类型中相关字段
        $node_type_fields = Fieldconfig::where('node_type', '=', $node['type'])->get()->toArray();

        foreach ($node_type_fields as $key => $item) {
            //如果已经删除了的字段，则不会在这里出现
            try {
                $node_field = Nodefield::where('nid', '=', $nid)->where('field_name', '=', $item['field_name'])->firstOrFail()->toArray();
            } catch (Exception $exc) {
                $node_field['value'] = null;
            }
            $node_type_fields[$key]['value'] = $item['value'] = $node_field['value'];

            //分类类型特殊处理
            if ($node_type_fields[$key]['field_type'] == 'category' && !empty($item['value'])) {
                //这里应该获取所属分类列表
                $category_info = Categorytype::where('machine_name', '=', $item['field_type'])->firstOrFail()->toArray();
                $category_list = Category::where('tid', '=', $category_info['id'])->get()->toArray();
                $node_type_fields[$key]['category_list'] = $category_list;
            }
            //checkbox复选框特殊处理
            if ($node_type_fields[$key]['field_type'] == 'checkbox') {
                //1、原数据处理
                $node_type_fields[$key]['config_data'] = self::load_for_edit_checkbox($item['field_name']);
                //2、保存的值处理
                if ($item['value']) {
                    $node_type_fields[$key]['value'];
                    $view_arr = explode(',', $node_type_fields[$key]['value']);
                    $node_type_fields[$key]['value'] = $view_arr;
                }
            }
            //radio单选框特殊处理
            if ($node_type_fields[$key]['field_type'] == 'radio') {
                //原数据处理
                $node_type_fields[$key]['config_data'] = self::load_for_edit_checkbox($item['field_name']);
            }
            //select选择列表特殊处理
            if ($node_type_fields[$key]['field_type'] == 'select') {
                //原数据处理
                $node_type_fields[$key]['config_data'] = self::load_for_edit_checkbox($item['field_name']);
            }
            //图片上传特殊处理
            if ($node_type_fields[$key]['field_type'] == 'image') {
                //原数据处理
                $node_type_fields[$key]['config_data'] = self::load_for_edit_image($item['field_name']);
            }
        }

        $node['fields'] = $node_type_fields;
        return $node;
    }

    /**
     * checkbox复选框
     */
    public static function load_for_edit_checkbox($field_name) {
        $field_config = Fieldconfig::where('field_name', '=', $field_name)->firstOrFail()->toArray();
        $config_data = json_decode($field_config['config_data'], true);
        //转义字符转化成空格方便处理
        $config_data['value'] = str_replace("\r\n", " ", $config_data['value']);
        $config_arr = explode(' ', $config_data['value']);
        //进行再处理生成数组
        foreach ($config_arr as $item) {
            $item_value = explode('|', $item);
            $item_arr[$item_value[0]] = $item_value[1];
        }
        return $item_arr;
    }

    /**
     * image图片上传
     */
    public static function load_for_edit_image($field_name) {
        $field_config = Fieldconfig::where('field_name', '=', $field_name)->firstOrFail()->toArray();
        $config_data = json_decode($field_config['config_data'], true);

        return $config_data;
    }

    /** ======================================================
     * 编辑的时候保存
      ======================================================= */

    /**
     * checkbox
     */
    public static function save_checkbox($input_checkbox) {
        $arrange_value = null;
        foreach ($input_checkbox as $key => $value) {
            $arrange_value .= $value . ',';
        }
        $arrange_value = rtrim($arrange_value, ',');
        return $arrange_value;
    }

    /** ======================================================
     * Node图片逻辑处理
      ======================================================= */

    /**
     * ------------------------------------------------------------------------
     * 添加内容的时候，保存图片
     * @param type $FILES
     * @param type $node_type_fields
     * @return type
     */
    public static function image_add($FILES, $node_type_fields) {
        if (isset($FILES)) {
            foreach ($node_type_fields as $field) {
                if (!empty($field['config_data']) && $field['field_type'] == 'image') {
                    //获取基础配置属性
                    $max_num = $field['config_data']['file_max_num'];
                    $file_path = isset($field['config_data']['file_path']) ? $field['config_data']['file_path'] : 'upload/node/';
                    $max_size = $field['config_data']['max_size'];
                    $max_len = $field['config_data']['max_len'];
                    $min_len = $field['config_data']['min_len'];
                    $file_default = $field['config_data']['file_default'];

                    $img_name = '';
                    if ($field['field_type'] == 'image') {
                        //file_max_num为0时为无限上传
                        if ($field['config_data']['file_max_num'] == 0) {
                            $field['config_data']['file_max_num'] = 100;
                        }
                        for ($i = 0; $i < $field['config_data']['file_max_num']; $i++) {
                            $name = $field['field_name'] . '_' . $i;
                            if (isset($FILES[$name])) {
                                //获取图片格式
                                $lastdot = strrpos($FILES[$name]['name'], "."); //找到区分文件名与扩展名的标记符“.”最后出现的位置
                                $extended = substr($FILES[$name]['name'], $lastdot + 1); //取出扩展名

                                $file_name = date('YmdHi', time()) . rand(11111111, 99999999) . '.' . $extended;
                                $img_name .= Image::upload($FILES[$name], $file_path, $file_name) . ',';
                            }
                        }
                    }
                    $result = array('field_name' => $field['field_name'], 'value' => rtrim($img_name, ','));
                    return $result;
                }
            }
        }
        return array('field_name' => '', 'value' => '');
    }

    /**
     * ------------------------------------------------------------------------
     * 编辑内容的时候，保存图片
     * @param type $FILES
     * @param type $node_type_fields
     * @return type
     */
    public static function image_edit($FILES, $input, $node) {
        if (isset($_FILES)) {
            foreach ($node['fields'] as $key => $field) {
                if (!empty($field['config_data']) && $field['field_type'] == 'image') {
                    //获取基础配置属性
                    $max_num = $field['config_data']['file_max_num'];
                    $file_path = $field['config_data']['file_path'];
                    $max_size = $field['config_data']['max_size'];
                    $max_len = $field['config_data']['max_len'];
                    $min_len = $field['config_data']['min_len'];
                    $file_default = $field['config_data']['file_default'];

                    $img_name = '';
                    if ($field['field_type'] == 'image') {
                        //file_max_num为0时为无限上传
                        if ($field['config_data']['file_max_num'] == 0) {
                            $field['config_data']['file_max_num'] = 100;
                        }
                        for ($i = 0; $i < $field['config_data']['file_max_num']; $i++) {
                            $name = $field['field_name'] . '_' . $i;
                            if (isset($_FILES[$name]) && $_FILES[$name]['size'] != 0) {
                                //获取图片格式
                                $lastdot = strrpos($_FILES[$name]['name'], "."); //找到区分文件名与扩展名的标记符“.”最后出现的位置
                                $extended = substr($_FILES[$name]['name'], $lastdot + 1); //取出扩展名

                                $file_name = date('YmdHi', time()) . rand(11111111, 99999999) . '.' . $extended;
                                $img_name .= Image::upload($_FILES[$name], $file_path, $file_name) . ',';
                            }
                        }
                    }
                    //如果有删除，则删除
                    $new = '';
                    $delete = isset($input[$field['field_name'] . '_delete']) ? $input[$field['field_name'] . '_delete'] : '';
                    if ($delete) {
                        $delete_array = explode(',', $delete);
                        $orign_array = explode(',', $field['value']);
                        $new = implode(',', array_diff($orign_array, $delete_array));
                        //删除文件
                        Image::delete_array($delete_array);
                    } else {
                        $new = $node['fields'][$key]['value'];
                    }

                    if ($img_name) {
                        if ($new) {
                            $field_value = $new . ',' . rtrim($img_name, ',');
                        } else {
                            $field_value = rtrim($img_name, ',');
                        }
                    } else {
                        if ($new) {
                            $field_value = $new;
                        } else {
                            $field_value = $node['fields'][$key]['value'];
                        }
                    }
                    $result = array('field_name' => $field['field_name'], 'value' => $field_value);
                    return $result;
                }
            }
        }
    }

}
