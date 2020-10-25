<?php

/*
 * 菜单管理
 */

class BackMenuController extends BackBaseController {

    /**
     * 菜单类型列表
     */
    public function index() {
        $menu_type = Menutype::all();
        return View::make('BackTheme::templates.menu/index')->with('menu_type', $menu_type);
    }

    /**
     * 
     * 菜单列表
     */
    public function menu_list($mtid) {
        $menu_type = Menutype::find($mtid);

        $menu = Menu::get_all_menu_by_id($mtid);

        $data = array(
            'menu_type' => $menu_type,
            'menus' => $menu,
            'tid' => $mtid,
        );
        return View::make('BackTheme::templates.menu/list')->with('data', $data);
    }

    /**
     * 
     * 添加菜单
     */
    public function add($mtid) {
        //获取菜单级别
        $menus = Menu::get_all_menu_by_id($mtid);

        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
                'url' => 'required|max:256',
                'menu_class' => 'required',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'url.required' => '必须填写URL地址',
                'url.max' => 'URL地址最多只能输入:max个字符',
                'menu_class.required' => '必须选择菜单级别',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('menus', $menus)->withInput();
            }
            //创建用户
            $data = array();
            $data['title'] = $input['title'];
            $data['description'] = $input['description'];
            $data['url'] = $input['url'];
            $data['pid'] = $input['menu_class'];
            $data['tid'] = $mtid;
            $result = Menu::create($data);
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加菜单,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加菜单成功！', 'type' => 'success', 'url' => '/admin/menu/' . $mtid . '/list'));
            }
        }
        return View::make('BackTheme::templates.menu/add')->with('menus', $menus);
    }

    /**
     * 编辑菜单
     * @param type $mtid  菜单类型type menu
     * @param type $id   菜单ID
     */
    public function edit($mtid, $mid) {
        //获取菜单级别
        $menus = Menu::get_all_menu_by_id($mtid);
        $current_menu = Menu::find($mid);

        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
                'url' => 'required|max:256',
                'menu_class' => 'required',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'url.required' => '必须填写URL地址',
                'url.max' => 'URL地址最多只能输入:max个字符',
                'menu_class.required' => '必须选择菜单级别',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('menus', $menus)->with('current_menu', $current_menu)->withInput();
            }
            //保存用户
            $current_menu['title'] = $input['title'];
            $current_menu['description'] = $input['description'];
            $current_menu['url'] = $input['url'];
            $current_menu['pid'] = $input['menu_class'];
            $current_menu['tid'] = $mtid;
            $current_menu->save();
            if ($current_menu) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑菜单,ID为' . $mid));
                return View::make('BackTheme::templates.message', array('message' => '更新菜单成功！', 'type' => 'success', 'url' => '/admin/menu/' . $mtid . '/list'));
            }
        }
        return View::make('BackTheme::templates.menu/edit')->with('menus', $menus)->with('current_menu', $current_menu);
    }

    /**
     * 删除菜单
     */
    public function delete($mtid, $mid) {
        $menu = Menu::find($mid);
        if (!$menu) {
            return View::make('BackTheme::templates.message', array('message' => '删除失败，不存在该菜单！', 'type' => 'error', 'url' => '/admin/menu/' . $mtid . '/list'));
        }
        //如果有子菜单，则不允许删除
        $is_child = Menu::where('pid', '=', $mid)->get()->toArray();
        if (!$is_child) {
            $menu->delete();
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除菜单,ID为' . $mid));
            return View::make('BackTheme::templates.message', array('message' => '删除菜单成功！', 'type' => 'success', 'url' => '/admin/menu/' . $mtid . '/list'));
        }
        return View::make('BackTheme::templates.message', array('message' => '删除菜单失败，存在下级菜单，请删除下级菜单再删除该菜单！', 'type' => 'error', 'url' => '/admin/menu/' . $mtid . '/list'));
    }

    /**
     * 
     * 添加菜单类型
     */
    public function add_type() {
        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|unique:menu_type,title|max:32',
                'description' => 'max:256',
                'machine_name' => 'required|unique:menu_type,machine_name|max:64|alpha_dash',
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
            //创建菜单类型
            $data = array();
            $data['title'] = $input['title'];
            $data['description'] = $input['description'];
            $data['machine_name'] = $input['machine_name'];
            $result = Menutype::create($data);
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加菜单类型,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加菜单类型成功！', 'type' => 'success', 'url' => '/admin/menu'));
            }
        }
        return View::make('BackTheme::templates.menu/add_type');
    }

    /**
     * 编辑菜单类型
     */
    public function edit_type($mtid) {
        $menu_type = Menutype::find($mtid);
        if (Request:: method() == 'POST') {
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
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //保存编辑
            $menu_type['title'] = $input['title'];
            $menu_type['description'] = $input['description'];
            //$menu_type['machine_name'] = $input['machine_name'];
            $result = $menu_type->save();
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑菜单类型,ID为' . $mtid));
                return View::make('BackTheme::templates.message', array('message' => '编辑菜单类型成功！', 'type' => 'success', 'url' => '/admin/menu'));
            }
        }
        return View::make('BackTheme::templates.menu/edit_type')->with('menu_type', $menu_type);
    }

    /**
     * 删除菜单类型
     */
    public function delete_type($mtid) {
        //禁止删除顶部菜单类型和底部菜单类型
        if ($mtid == 1 || $mtid == 2) {
            return View::make('BackTheme::templates.message', array('message' => '禁止删除顶部菜单类型和底部菜单类型！', 'type' => 'warning', 'url' => '/admin/menu'));
        }

        //如果存在
        $is_child = Menu::where('tid', '=', $mtid)->get()->toArray();
        if (!$is_child) {
            $menutype = Menutype::find($mtid);
            $menutype->delete();
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除菜单类型,ID为' . $mtid));
            return View::make('BackTheme::templates.message', array('message' => '删除菜单类型成功！', 'type' => 'success', 'url' => '/admin/menu'));
        }
        return View::make('BackTheme::templates.message', array('message' => '删除菜单类型失败，存在子菜单，请删除子菜单再删除该菜单类型！', 'type' => 'error', 'url' => '/admin/menu'));
    }

    /**
     * 编辑权重
     */
    public function edit_weight($mtid) {
        if (Request::method() == 'POST') {
            $input = Input::all();
            if (isset($input['weight'])) {
                foreach ($input['weight'] as $key => $value) {
                    $menu = Menu::find($key);
                    $menu->weight = $value;
                    $menu->save();
                }
            }
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑菜单权重位置'));
            return View::make('BackTheme::templates.message', array('message' => '修改排序权重成功！', 'type' => 'success', 'url' => '/admin/menu/' . $mtid . '/list'));
        }
    }

}
