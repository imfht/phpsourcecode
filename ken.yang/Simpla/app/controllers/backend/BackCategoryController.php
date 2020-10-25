<?php

/*
 * 分类管理
 */

class BackCategoryController extends BackBaseController {

    /**
     * 分类类型列表
     */
    public function index() {
        $category_type = Categorytype::all();
        return View::make('BackTheme::templates.category/index')->with('category_type', $category_type);
    }

    /**
     * 
     * 分类列表
     */
    public function category_list($ctid) {
        $category_type = Categorytype::find($ctid);

        $category = Category::get_all_category($ctid);

        $data = array(
            'category_type' => $category_type,
            'categories' => $category,
            'ctid' => $ctid,
        );
        return View::make('BackTheme::templates.category/list')->with('data', $data);
    }

    /**
     * 
     * 添加分类
     */
    public function add($ctid) {
        //获取分类级别
        $categories = Category::get_all_category($ctid);

        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
                'category_class' => 'required',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'category_class.required' => '必须填写分类级别',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('categories', $categories)->withInput();
            }
            //创建分类
            $data = array();
            $data['title'] = $input['title'];
            $data['description'] = $input['description'];
            $data['pid'] = $input['category_class'];
            $data['tid'] = $ctid;
            $result = Category::create($data);

            //保存SEO数据
            $seo_data['type'] = 'category';
            $seo_data['cid'] = $result['id'];
            $seo_data['title'] = $seo_data['title'] = isset($input['seo_title']) ? $input['seo_title'] : '';
            $seo_data['description'] = isset($input['seo_description']) ? $input['seo_description'] : '';
            $seo_data['keywords'] = isset($input['seo_keywords']) ? $input['seo_keywords'] : '';
            Seo::create($seo_data);

            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '创建分类,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加分类成功！', 'type' => 'success', 'url' => '/admin/category/' . $ctid . '/list'));
            }
        }
        return View::make('BackTheme::templates.category/add')->with('categories', $categories);
    }

    /**
     * 编辑分类
     * @param type $ctid  分类类型type category
     * @param type $cid   分类ID
     */
    public function edit($ctid, $cid) {
        //获取分类级别
        $categories = Category::get_all_category($ctid);
        $current_category = Category::find($cid);

        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
                'category_class' => 'required',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'category_class.required' => '必须填写分类级别',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('categories', $categories)->with('current_category', $current_category)->withInput();
            }
            //保存分类
            $current_category->title = $input['title'];
            $current_category->description = $input['description'];
            $current_category->pid = $input['category_class'];
            $current_category->tid = $ctid;
            $current_category->save();
            //保存SEO数据
            $seo_data['title'] = $seo_data['title'] = isset($input['seo_title']) ? $input['seo_title'] : '';
            $seo_data['description'] = isset($input['seo_description']) ? $input['seo_description'] : '';
            $seo_data['keywords'] = isset($input['seo_keywords']) ? $input['seo_keywords'] : '';
            Seo::where('cid', '=', $cid)->update($seo_data);

            if ($current_category) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑分类,ID为' . $cid));
                return View::make('BackTheme::templates.message', array('message' => '更新分类成功！', 'type' => 'success', 'url' => '/admin/category/' . $ctid . '/list'));
            }
        }
        //获取SEO信息
        list($title, $description, $keywords) = Seo::load_category_seo($current_category);
        $current_category->seo = array(
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords
        );
        return View::make('BackTheme::templates.category/edit')->with('categories', $categories)->with('current_category', $current_category);
    }

    /**
     * 删除分类
     */
    public function delete($ctid, $cid) {
        $category = Category::find($cid);
        if (!$category) {
            return View::make('BackTheme::templates.message', array('message' => '删除失败，不存在该分类！', 'type' => 'error', 'url' => '/admin/category/' . $ctid . '/list'));
        }
        //如果有子分类，则不允许删除
        $is_child = Category::where('pid', '=', $cid)->get()->toArray();
        if (!$is_child) {
            $category->delete();
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除分类,ID为' . $cid));
            return View::make('BackTheme::templates.message', array('message' => '删除分类成功！', 'type' => 'success', 'url' => '/admin/category/' . $ctid . '/list'));
        }
        return View::make('BackTheme::templates.message', array('message' => '删除分类失败，存在下级分类，请删除下级分类再删除该分类！', 'type' => 'error', 'url' => '/admin/category/' . $ctid . '/list'));
    }

    /**
     * 
     * 添加分类类型
     */
    public function add_type() {
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|unique:category_type,title|max:32',
                'description' => 'max:256',
                'machine_name' => 'required|unique:category_type,title|max:64|alpha_dash',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.unique' => '标题已经存在',
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'machine_name.required' => '必须填写机器名字',
                'machine_name.unique' => '机器名字已经存在',
                'machine_name.max' => '机器名字最多只能输入:max个字符',
                'machine_name.alpha_dash' => '机器名字仅允许字母、数字、破折号（-）以及底线（_）',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //创建分类类型
            $data = array();
            $data['title'] = $input['title'];
            $data['description'] = $input['description'];
            $data['machine_name'] = $input['machine_name'];
            $result = Categorytype::create($data);
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '创建分类类型,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加分类类型成功！', 'type' => 'success', 'url' => '/admin/category'));
            }
        }
        return View::make('BackTheme::templates.category/add_type');
    }

    /**
     * 编辑分类类型
     */
    public function edit_type($ctid) {
        $category_type = Categorytype::find($ctid);
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
                //'machine_name' => 'required|max:64',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                //'machine_name.required' => '必须填写机器名字',
                //'machine_name.max' => '机器名字最多只能输入64个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('category_type', $category_type)->withInput();
            }
            //保存编辑
            $category_type['title'] = $input['title'];
            $category_type['description'] = $input['description'];
            //$category_type['machine_name'] = $input['machine_name'];
            $result = $category_type->save();
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑分类类型,ID为' . $ctid));
                return View::make('BackTheme::templates.message', array('message' => '编辑分类类型成功！', 'type' => 'success', 'url' => '/admin/category'));
            }
        }
        return View::make('BackTheme::templates.category/edit_type')->with('category_type', $category_type);
    }

    /**
     * 删除分类类型
     */
    public function delete_type($ctid) {
        //禁止删除顶部分类类型和底部分类类型
        if ($ctid == 1) {
            return View::make('BackTheme::templates.message', array('message' => '禁止删除默认分类类型！', 'type' => 'warning', 'url' => '/admin/category'));
        }

        //如果存在
        $is_child = Category::where('tid', '=', $ctid)->get()->toArray();
        if (!$is_child) {
            $categorytype = Categorytype::find($ctid);
            $categorytype->delete();
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除分类类型,ID为' . $ctid));
            return View::make('BackTheme::templates.message', array('message' => '删除分类类型成功！', 'type' => 'success', 'url' => '/admin/category'));
        }
        return View::make('BackTheme::templates.message', array('message' => '删除分类类型失败，存在子分类，请删除子分类再删除该分类类型！', 'type' => 'error', 'url' => '/admin/category'));
    }

    /**
     * 编辑权重
     */
    public function edit_weight($ctid) {
        if (Request::method() == 'POST') {
            $input = Input::all();
            if (isset($input['weight'])) {
                foreach ($input['weight'] as $key => $value) {
                    $category = Category::find($key);
                    $category->weight = $value;
                    $category->save();
                }
            }
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑区块权重位置'));
            return View::make('BackTheme::templates.message', array('message' => '修改排序权重成功！', 'type' => 'success', 'url' => '/admin/category/' . $ctid . '/list'));
        }
    }

}
