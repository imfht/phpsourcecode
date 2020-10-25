<?php

/*
 * 内容管理
 */

class BackNodeController extends BackBaseController {

    /**
     * 内容列表
     */
    public function index() {
        //获取内容类型，进行初始化
        $types = Nodetype::get_all_type_to_filter();
        $choose = array('status' => '2', 'type' => '0');
        //如果存在筛选，则进行筛选
        if (Request::method() == 'POST') {
            $input = Input::all();
            $status = $input['status'];
            $type = $input['type'];
            $choose = array('status' => $status, 'type' => $type);

            //判断查询
            if (in_array($status, array(0, 1)) && $type != '0') {
                $nodes = Node::where('status', '=', $status)->where('type', '=', $type)->orderBy('id', 'desc')->paginate(15);
            } elseif (in_array($status, array(0, 1))) {
                $nodes = Node::where('status', '=', $status)->orderBy('id', 'desc')->paginate(15);
            } elseif ($type != '0') {
                $nodes = Node::where('type', '=', $type)->orderBy('id', 'desc')->paginate(15);
            } else {
                $nodes = Node::orderBy('id', 'desc')->paginate(15);
            }
        } else {
            $nodes = Node::orderBy('id', 'desc')->paginate(15);
        }

        return View::make('BackTheme::templates.node/index', array('nodes' => $nodes, 'types' => $types, 'choose' => $choose));
    }

    /**
     * 
     * 添加内容
     */
    //1、第一步：选择内容类型
    public function type_list() {
        //读取所有内容类型
        $node_type = Nodetype::all();
        return View::make('BackTheme::templates.node/type_list', array('node_type' => $node_type));
    }

    //2、第二步：创建内容
    public function add($type) {
        //读取该内容类型信息
        $node_type = Nodetype::find($type);
        //根据node-type类型读取所有相关字段
        $node_type_fields = Nodetype::get_node_type_fields($type);

        //如果是POST，则为添加内容
        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:256',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题长度超出了:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //获取当前用户信息
            $user = Auth::user();

            $data = array();
            $data['type'] = $type;
            $data['uid'] = $user->id;
            $data['title'] = $input['title'];
            $data['body'] = $input['body'];
            $data['status'] = isset($input['status']) ? '1' : '0';
            $data['comment'] = 0;
            $data['promote'] = isset($input['promote']) ? '1' : '0';
            $data['sticky'] = isset($input['sticky']) ? '1' : '0';
            $data['plusfine'] = isset($input['plusfine']) ? '1' : '0';
            $data['view'] = isset($input['view']) ? rand(100, 1000) : 0;
            $data['created_at'] = NOW_FORMAT_TIME;
            $data['updated_at'] = NOW_FORMAT_TIME;


            //文件上传
            //判断是否有图片或者文件字段
            $upload_result = Node::image_add($_FILES, $node_type_fields);
            $input[$upload_result['field_name']] = $upload_result['value'];

            try {
                //开始事务
                DB::beginTransaction();

                //1、创建Node
                $node = Node::create($data);
                //2、保存文件
                //3、保存SEO数据
                $seo_data['type'] = 'node';
                $seo_data['nid'] = $node['id'];
                $seo_data['title'] = $seo_data['title'] = isset($input['seo_title']) ? $input['seo_title'] : '';
                $seo_data['description'] = isset($input['seo_description']) ? $input['seo_description'] : '';
                $seo_data['keywords'] = isset($input['seo_keywords']) ? $input['seo_keywords'] : '';
                Seo::create($seo_data);
                //4、字段处理并保存
                foreach ($node_type_fields as $field) {
                    //当涉及到有些字段没有值的情况，则直接跳过,不保存
                    if (!isset($input[$field['field_name']])) {
                        continue;
                    }
                    //checkbox特殊处理
                    if ($field['field_type'] == 'checkbox') {
                        $input[$field['field_name']] = Node::save_checkbox($input[$field['field_name']]);
                    }

                    $field_data = array();
                    $field_data['nid'] = $node['id'];
                    $field_data['field_name'] = $field['field_name'];
                    $field_data['value'] = $input[$field['field_name']];
                    Nodefield::create($field_data);

                    //根据不同的内容类型保存数据
                    //如果是分类
                    if ($field['field_type'] == 'category') {
                        $category_data = array();
                        $category_data['cid'] = $input[$field['field_name']];
                        $category_data['nid'] = $node->id;
                        Categorydata::create($category_data);
                    }
                }
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加新内容,ID为' . $node['id']));
            } catch (Exception $e) {
                //事务回滚
                DB::rollback();
                return View::make('BackTheme::templates.message', array('message' => '添加内容失败！', 'type' => 'error', 'url' => Request::url()));
            }
            //提交事务
            DB::commit();

            //跳转
            return View::make('BackTheme::templates.message', array('message' => '添加内容成功！', 'type' => 'success', 'url' => '/admin/node'));
        }
        return View::make('BackTheme::templates.node/add', array('node_type' => $node_type, 'node_type_fields' => $node_type_fields));
    }

    /**
     * 编辑内容
     */
    public function edit($nid) {
        //读取主内容
        $node = Node::load_for_edit($nid);

        //获取SEO信息
        list($title, $description, $keywords) = Seo::load_node_edit_seo($node);
        $node['seo'] = array(
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords
        );

        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:256',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题长度超出了:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $data['title'] = $input['title'];
            $data['body'] = $input['body'];
            $data['status'] = isset($input['status']) ? '1' : '0';
            $data['promote'] = isset($input['promote']) ? '1' : '0';
            $data['sticky'] = isset($input['sticky']) ? '1' : '0';
            $data['plusfine'] = isset($input['plusfine']) ? '1' : '0';
            $data['view'] = $input['view'];
            $data['updated_at'] = NOW_FORMAT_TIME;

            //SEO数据
            $seo_data['title'] = isset($input['seo_title']) ? $input['seo_title'] : '';
            $seo_data['description'] = isset($input['seo_description']) ? $input['seo_description'] : '';
            $seo_data['keywords'] = isset($input['seo_keywords']) ? $input['seo_keywords'] : '';

            //文件上传
            //判断是否有图片或者文件字段
            $upload_result = Node::image_edit($_FILES, $input, $node);
            $input[$upload_result['field_name']] = $upload_result['value'];

            try {
                //开始事务
                DB::beginTransaction();
                //1、保存Node
                Node::where('id', '=', $node['id'])->update($data);
                //2、保存seo
                Seo::where('nid', '=', $node['id'])->update($seo_data);
                //3、相关字段处理并保存
                foreach ($node['fields'] as $field) {
                    //checkbox特殊处理
                    if ($field['field_type'] == 'checkbox') {
                        $input[$field['field_name']] = Node::save_checkbox($input[$field['field_name']]);
                    }

                    //保存数据,检查是否存在该字段，没有则创建，有则更新
                    $check_field = Nodefield::where('nid', '=', $nid)->where('field_name', '=', $field['field_name'])->get()->toArray();
                    if ($check_field) {
                        $field_data = array();
                        $field_data['value'] = $input[$field['field_name']];
                        Nodefield::where('nid', '=', $nid)->where('field_name', '=', $field['field_name'])->update($field_data);
                    } else {
                        $field_data = array();
                        $field_data['nid'] = $nid;
                        $field_data['field_name'] = $field['field_name'];
                        $field_data['value'] = $input[$field['field_name']];
                        Nodefield::create($field_data);
                    }
                }
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑内容,ID为' . $node['id']));
            } catch (Exception $e) {
                //事务回滚
                DB::rollback();
                return View::make('BackTheme::templates.message', array('message' => '编辑内容失败！', 'type' => 'error', 'url' => Request::url()));
            }
            //提交事务
            DB::commit();
            return View::make('BackTheme::templates.message', array('message' => '编辑内容成功！', 'type' => 'success', 'url' => '/admin/node'));
        }
        return View::make('BackTheme::templates.node/edit', array('node' => $node));
    }

    /**
     * 根据nid删除内容
     * @param type $nid
     * @return type
     */
    public function delete($nid) {
        //判断是否存在
        $node = Node::find($nid);
        if ($node) {
            //1、删除相关字段
            Nodefield::where('nid', '=', $nid)->delete();
            //2、删除内容
            $node->delete();
            //3、记录日志
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除内容,ID为' . $nid));
            //return '删除内容成功';
            return Redirect::to('admin/node');
            //return View::make('BackTheme::templates.message', array('message' => '删除内容成功！', 'type' => 'success', 'url' => '/admin/node'));
        } else {
            //return '删除内容失败，不存在的内容！';
            return View::make('BackTheme::templates.message', array('message' => '删除内容失败，不存在的内容！', 'type' => 'error', 'url' => '/admin/node'));
        }
    }

}
