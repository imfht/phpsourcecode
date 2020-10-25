<?php

/*
 * 内容类型管理
 */

class BackNodeTypeController extends BackBaseController {

    /**
     * 内容类型列表
     */
    public function index() {
        $node_type = Nodetype::all();
        return View::make('BackTheme::templates.node_type/list')->with('node_type', $node_type);
    }

    /**
     * 
     * 添加内容类型
     */
    public function add() {
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'name' => 'required|unique:node_type,name|max:32',
                'type' => 'required|unique:node_type,name|max:64',
            );
            $messages = array(
                'name.required' => '必须填写标题',
                'name.unique' => '该标题已经存在',
                'name.max' => '标题最多只能输入:max个字符',
                'type.required' => '必须填写机器名字',
                'type.unique' => '该机器名字已经存在',
                'type.max' => '机器名字最多只能输入:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //创建用户
            $data = array();
            $data['name'] = $input['name'];
            $data['description'] = $input['description'];
            $data['type'] = $input['type'];
            $result = Nodetype::create($data);
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加内容类型,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加内容类型成功！', 'type' => 'success', 'url' => '/admin/node/type'));
            }
        }
        return View::make('BackTheme::templates.node_type/add');
    }

    /**
     * 编辑内容类型
     */
    public function edit($type) {
        $node_type = Nodetype::find($type);
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'name' => 'required',
                    //'type' => 'required',
            );
            $messages = array(
                'name.required' => '必须填写标题',
                    //'type.required' => '必须填写机器名字',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //创建用户
            $node_type['name'] = $input['name'];
            $node_type['description'] = $input['description'];
            //$node_type['type'] = $input['type'];
            $result = $node_type->save();
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑内容类型,类型为' . $type));
                return View::make('BackTheme::templates.message', array('message' => '编辑内容类型成功！', 'type' => 'success', 'url' => '/admin/node/type'));
            }
        }
        return View::make('BackTheme::templates.node_type/edit')->with('node_type', $node_type);
    }

    /**
     * 删除内容类型
     */
    public function delete($type) {
        //禁止删除系统自带内容类型
        if ($type == 'page' || $type == 'article') {
            return View::make('BackTheme::templates.message', array('message' => '禁止删除系统自带内容类型！', 'type' => 'warning', 'url' => '/admin/node/type'));
        }

        //如果存在
        $is_child = Node::where('type', '=', $type)->get()->toArray();
        if (!$is_child) {
            $nodetype = Nodetype::find($type);
            $nodetype->delete();
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除内容类型,内容类型为' . $type));
            return View::make('BackTheme::templates.message', array('message' => '删除内容类型成功！', 'type' => 'success', 'url' => '/admin/node/type'));
        }
        return View::make('BackTheme::templates.message', array('message' => '删除内容类型失败，该内容类型中存在内容，请删除内容再删除该内容类型！', 'type' => 'error', 'url' => '/admin/node/type'));
    }

    /**
     * 内容类型字段管理
     */
    public function fields($type) {
        $fields = Fieldconfig::where('node_type', '=', $type)->orderBy('weight', 'asc')->get()->toArray();
        foreach ($fields as $key => $item) {
            $field_info = Field::where('type', '=', $item['field_type'])->first()->toArray();
            $fields[$key]['name'] = $field_info['name'];
        }

        return View::make('BackTheme::templates.node_type/field', array('fields' => $fields, 'type' => $type));
    }

    /**
     * 内容类型字段添加
     */
    public function field_add($type) {
        //获取所有字段类型
        $fields = Field::all()->toArray();

        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'label' => 'required|max:32',
                'field_name' => 'required|unique:field_config,field_name|max:64',
                'field_type' => 'required|max:64',
            );
            $messages = array(
                'label.required' => '必须填写标签',
                'label.max' => '标签最多只能输入:max个字符',
                'field_name.required' => '必须填写字段名字',
                'field_name.unique' => '该字段名字已经存在，请更换',
                'field_name.max' => '字段名字最多只能输入:max个字符',
                'field_type.required' => '必须选择字段类型',
                'field_type.max' => '字段类型最多只能输入:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('fields', $fields)->withInput();
            }
            //添加字段
            $Fieldconfig = new Fieldconfig;
            $Fieldconfig->node_type = $type;
            $Fieldconfig->label = $input['label'];
            $Fieldconfig->field_name = $input['field_name'];
            $Fieldconfig->field_type = $input['field_type'];
            if ($Fieldconfig->save()) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加内容字段,字段名字为' . $input['field_name']));
                return View::make('BackTheme::templates.message', array('message' => '添加字段成功！', 'type' => 'success', 'url' => '/admin/node/type/' . $type . '/field'));
            }
        }


        return View::make('BackTheme::templates.node_type/field_add', array('fields' => $fields));
    }

    /**
     * 内容类型字段编辑
     * @param type $type
     * @return type
     */
    public function field_edit($type, $field_name) {
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'label' => 'required',
            );
            $messages = array(
                'label.required' => '必须填写标签',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('fields', $fields)->withInput();
            }
            //保存字段
            $data['label'] = $input['label'];
            $result = Fieldconfig::where('field_name', '=', $field_name)->update($data);

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑内容字段,字段名字为' . $field_name));
            return View::make('BackTheme::templates.message', array('message' => '保存字段成功！', 'type' => 'success', 'url' => '/admin/node/type/' . $type . '/field'));
        }
        //获取当前字段信息
        $current_field = Fieldconfig::where('field_name', '=', $field_name)->first()->toArray();
        //获取字段类型信息
        $field_info = Field::where('type', '=', $current_field['field_type'])->first()->toArray();
        $current_field['name'] = $field_info['name'];
        return View::make('BackTheme::templates.node_type/field_edit', array('current_field' => $current_field));
    }

    /**
     * 内容类型字段配置
     * @param string $type
     * @param string $field_name
     * @return string
     */
    public function field_config($type, $field_name) {
        //请求为FORM提交
        if (Request::method() == 'POST') {
            $input = Input::all();
            switch ($input['type']) {
                case 'category':
                    $this->field_config_category($type, $field_name);
                    break;
                case 'image':
                    $this->field_config_image($type, $field_name, $input['file_path']);
                    break;
                default:
                    $this->field_config_default($type, $field_name);
                    break;
            }
            //跳转到字段管理页面
            return Redirect::to('/admin/node/type/' . $type . '/field');
        }

        //查询$field_name所属字段类型信息
        $field_info = Fieldconfig::where('field_name', '=', $field_name)->first()->toArray();
        //将配置信息json转码
        $config = json_decode($field_info['config_data']);

        //获取分类类型
        if ($field_info['field_type'] == 'category') {
            $category_type = Categorytype::all()->toArray();
            return View::make('BackTheme::templates.field/config_' . $field_info["field_type"], array('field_info' => $field_info, 'config' => $config, 'category_type' => $category_type));
        }

        return View::make('BackTheme::templates.field/config_' . $field_info["field_type"], array('field_info' => $field_info, 'config' => $config));
    }

    /**
     * 删除该内容类型下面的字段
     */
    public function field_delete() {
        $field_name = Input::get('field_name');
        $field_config = Fieldconfig::where('field_name', '=', $field_name)->delete();
        Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除字段，字段名为' . $field_name));
        return '删除成功';
    }

    /**
     * 编辑权重
     */
    public function edit_weight($type) {
        foreach (Input::get('weight') as $key => $value) {
            $data = array();
            $data['weight'] = $value;
            Fieldconfig::where('node_type', '=', $type)->where('field_name', '=', $key)->update($data);
            Nodefield::where('field_name', '=', $key)->update($data);
        }
        Logs::create(array('uid' => Auth::user()->id, 'type' => 'other', 'message' => '编辑字段权重位置'));
        return View::make('BackTheme::templates.message', array('message' => '修改权重成功！', 'type' => 'success', 'url' => '/admin/node/type/' . $type . '/field'));
    }

    /*     * =========================================================
     * 字段配置
      ========================================================== */

    /**
     * 保存到数据库
     */
    public function field_config_save($type, $field_name, $config) {
        //以json的方式保存内容
        $data = array(
            'config_data' => json_encode($config),
        );
        Fieldconfig::where('node_type', '=', $type)->where('field_name', '=', $field_name)->update($data);
    }

    /**
     * 默认配置，可共同调用
     */
    public function field_config_default($type, $field_name) {
        $input = Input::all();
        //删除_token
        array_shift($input);
        $this->field_config_save($type, $field_name, $input);
    }

    /**
     * category分类配置
     */
    public function field_config_category($type, $field_name) {
        $input = Input::all();
        $rules = array(
            'category' => 'required',
        );
        $messages = array(
            'category.required' => '必须选择分类类型',
        );
        //进行字段验证
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->with('fields', $fields);
        }
        $config = array(
            'category' => $input['category'],
            'type' => $input['type'],
        );
        $this->field_config_save($type, $field_name, $config);
    }

    /**
     * image图片上传配置
     */
    public function field_config_image($type, $field_name, $save_path = null) {
        $input = Input::all();
        //删除_token
        array_shift($input);

        //设置默认图片
        if (isset($_FILES['file_default'])) {
            if ($save_path == null) {
                $save_path = 'upload/image/site/default/';
            }
            //检查路径是否存在，不存在则创建
            if (!file_exists($save_path)) {
                mkdir($save_path, '0777', TRUE);
            }
            $file_name = $field_name . '_default.jpg';
            $result = Base::img_upload($_FILES['file_default'], $save_path, $file_name);
            if ($result) {
                $input['file_default'] = $file_name;
            }
        }
        //die();
        $this->field_config_save($type, $field_name, $input);
    }

}
