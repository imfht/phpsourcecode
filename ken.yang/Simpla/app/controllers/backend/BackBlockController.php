<?php

/*
 * 区块管理
 */

class BackBlockController extends BackBaseController {

    /**
     * 获取所有区域区块关联列表
     */
    public function index() {
        $block_area = Blockarea::with('block')->orderBy('weight')->get();
        return View::make('BackTheme::templates.block/index')->with('block_area', $block_area);
    }

    /**
     * 
     * 添加区块
     */
    public function add() {
        //获取区块区域
        $block_area = Blockarea::all();
        $areas = array();
        foreach ($block_area as $row) {
            $areas[$row->id] = $row->title;
        }

        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'machine_name' => 'required|unique:block,machine_name|max:64|alpha_dash',
                'title' => 'max:32',
                'description' => 'required|max:256',
                'body' => 'required',
                'weight' => 'required|digits_between:1,9999',
                'cache' => 'between:0,1',
            );
            $messages = array(
                'machine_name.required' => '必须填写机器名字',
                'machine_name.unique' => '机器名字已经存在',
                'machine_name.max' => '机器名字最多只能输入:max个字符',
                'machine_name.alpha_dash' => '机器名字仅允许字母、数字、破折号（-）以及底线（_）',
                'title.max' => '标题最多只能输入:max个字符',
                'description.required' => '必须填写描述',
                'description.max' => '描述最多只能输入:max个字符',
                'body.required' => '必须填写内容',
                'weight.required' => '必须填写位置',
                'weight.digits_between' => '位置必须为数字且最多只能输入4个字符',
                'cache.between' => '必须选择开启或者关闭',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('areas', $areas)->withInput();
            }
            //创建区块
            $data = array();
            $data['baid'] = $input['baid'];
            $data['machine_name'] = $input['machine_name'];
            $data['title'] = $input['title'];
            $data['description'] = $input['description'];
            $data['body'] = $input['body'];
            $data['type'] = 'customer';
            $data['callback'] = '';
            $data['format'] = '';
            $data['theme'] = '';
            $data['status'] = 1;
            $data['weight'] = $input['weight'];
            $data['pages'] = '';
            $data['cache'] = $input['cache'];
            $result = Block::create($data);
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '创建区块,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加区块成功！', 'type' => 'success', 'url' => '/admin/block'));
            }
        }

        return View::make('BackTheme::templates.block/add')->with('areas', $areas);
    }

    /**
     * 编辑区块
     * @param area $baid  区块区域area block
     * @param area $id   区块ID
     */
    public function edit($bid) {
        //获取区块级别
        $block_area = Blockarea::all();
        $areas = array();
        foreach ($block_area as $row) {
            $areas[$row->id] = $row->title;
        }
        $block = Block::find($bid);

        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'max:32',
                'description' => 'required|max:256',
                //'body' => 'required',
                'weight' => 'required|digits_between:1,9999',
                'cache' => 'between:0,1',
            );
            $messages = array(
                'title.max' => '描述最多只能输入:max个字符',
                'description.required' => '必须填写区块描述',
                'description.max' => '描述最多只能输入:max个字符',
                //'body.required' => '必须填写区块内容',
                'weight.required' => '必须填写区块位置',
                'weight.digits_between' => '位置必须为数字且最多只能输入4个字符',
                'cache.between' => '必须选择开启或者关闭',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('areas', $areas)->withInput();
            }
            //更新区块
            $block['baid'] = $input['baid'];
            $block['title'] = $input['title'];
            $block['description'] = $input['description'];
            $block['body'] = isset($input['body']) ? $input['body'] : '';
            //$data['type'] = 'customer';
            //$data['callback'] = '';
            $block['format'] = '';
            $block['theme'] = '';
            $block['status'] = 1;
            $block['weight'] = $input['weight'];
            $block['pages'] = '';
            $block['cache'] = $input['cache'];
            $save_result = $block->save();
            if ($save_result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑区块,ID为' . $bid));
                return View::make('BackTheme::templates.message', array('message' => '更新区块成功！', 'type' => 'success', 'url' => '/admin/block'));
            }
        }
        return View::make('BackTheme::templates.block/edit')->with('block', $block)->with('areas', $areas);
    }

    /**
     * 删除区块
     */
    public function delete($bid) {
        $block = Block::find($bid);
        if (!$block) {
            return View::make('BackTheme::templates.message', array('message' => '删除失败，不存在该区块！', 'type' => 'error', 'url' => '/admin/block/'));
        }
        $result = $block->delete();
        if ($result) {
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除区块,ID为' . $bid));
            return View::make('BackTheme::templates.message', array('message' => '删除区块成功！', 'type' => 'success', 'url' => '/admin/block/'));
        }
        return View::make('BackTheme::templates.message', array('message' => '删除区块失败，存在下级区块，请删除下级区块再删除该区块！', 'type' => 'error', 'url' => '/admin/block/'));
    }

    /**
     * 
     * 添加区块区域
     */
    public function area_add() {
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|unique:block_area,title|max:32',
                'description' => 'max:256',
                'machine_name' => 'required|unique:block_area,machine_name|max:64|alpha_dash',
                'weight' => 'required|numeric|max:4',
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
                'weight.required' => '必须填写排序位置',
                'weight.numeric' => '排序位置必须为数字',
                'weight.max' => '排序位置最多只能输入:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //添加区块区域
            $data = array();
            $data['title'] = $input['title'];
            $data['description'] = $input['description'];
            $data['machine_name'] = $input['machine_name'];
            $result = Blockarea::create($data);
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '创建区块区域,ID为' . $result['id']));
                return View::make('BackTheme::templates.message', array('message' => '添加区块区域成功！', 'type' => 'success', 'url' => '/admin/block'));
            }
        }
        return View::make('BackTheme::templates.block/area_add');
    }

    /**
     * 编辑区块区域
     */
    public function area_edit($baid) {
        $block_area = Blockarea::find($baid);
        if (Request::mathod() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'description' => 'max:256',
                'machine_name' => 'required|max:64|alpha_dash',
                'weight' => 'required|numeric|max:4',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'machine_name.required' => '必须填写机器名字',
                'machine_name.max' => '机器名字最多只能输入:max个字符',
                'machine_name.alpha_dash' => '机器名字仅允许字母、数字、破折号（-）以及底线（_）',
                'weight.required' => '必须填写排序位置',
                'weight.numeric' => '排序位置必须为数字',
                'weight.max' => '排序位置最多只能输入:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //编辑区块区域
            $block_area['title'] = $input['title'];
            $block_area['description'] = $input['description'];
            //$block_area['machine_name'] = $input['machine_name'];
            $result = $block_area->save();
            if ($result) {
                Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑区块区域,ID为' . $baid));
                return View::make('BackTheme::templates.message', array('message' => '编辑区块区域成功！', 'type' => 'success', 'url' => '/admin/block'));
            }
        }
        return View::make('BackTheme::templates.block/edit_area')->with('block_area', $block_area);
    }

    /**
     * 删除区块区域
     */
    public function area_delete($baid) {
        //禁止删除顶部区块区域和底部区块区域
        if (in_array($baid, array(1, 2, 3, 4, 5, 6, 7))) {
            return View::make('BackTheme::templates.message', array('message' => '禁止删除系统自带区域！', 'type' => 'warning', 'url' => '/admin/block'));
        }

        //如果存在
        $is_child = Block::where('tid', '=', $baid)->get()->toArray();
        if (!$is_child) {
            $blockarea = Blockarea::find($baid);
            $blockarea->delete();
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除区块区域,ID为' . $baid));
            return View::make('BackTheme::templates.message', array('message' => '删除区块区域成功！', 'type' => 'success', 'url' => '/admin/block'));
        }
        return View::make('BackTheme::templates.message', array('message' => '删除区块区域失败，存在子区块，请删除子区块再删除该区块区域！', 'type' => 'error', 'url' => '/admin/block'));
    }

    /**
     * 编辑权重
     */
    public function edit_weight($baid) {
        foreach ($_POST['weight'] as $key => $value) {
            $block = Block::find($key);
            $block->weight = $value;
            $block->save();
        }
        Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑区块位置'));
        return View::make('BackTheme::templates.message', array('message' => '修改权重成功！', 'type' => 'success', 'url' => '/admin/block/' . $baid . '/list'));
    }

    /**
     * -----------------------------------------------------------------------
     * 刷新区块
     * 
     */
    public function refresh() {
        //获取所有模块下面的区块接口
        $modules_block = Hook_block::block_info();

        //将区块信息写入数据库
        foreach ($modules_block as $row) {
            //1、判断接口必填字段是否存在
            if (!$row['description'] || !$row['machine_name'] || !$row['callback']) {
                continue;
            }
            //2、对比数据库数据
            $result = Block::where('machine_name', '=', $row['machine_name'])->first();
            //Block::where('machine_name', '=', $row['machine_name'])->firstOrFail();在这里不能使用这个语句，会报错,应该使用first
            //如果没有数据则写入，有则更新
            if ($result) {
                if ($result['type'] == 'model') {
                    $data['title'] = $row['title'];
                    $data['description'] = $row['description'];
                    $data['callback'] = $row['callback'];
                    Block::where('machine_name', '=', $row['machine_name'])->update($data);
                } else {
                    $message = '已经存在一个模块名字叫做:' . $row['machine_name'];
                    return View::make('BackTheme::templates.message', array('message' => $message, 'type' => 'success', 'url' => '/admin/block'));
                }
            } else {
                $data = array();
                $data['baid'] = '1';
                $data['machine_name'] = $row['machine_name'];
                $data['title'] = $row['title'];
                $data['description'] = $row['description'];
                $data['body'] = '';
                $data['type'] = 'model';
                $data['callback'] = $row['callback'];
                $data['format'] = '';
                $data['theme'] = '';
                $data['status'] = 1;
                $data['weight'] = '0';
                $data['pages'] = '';
                $data['cache'] = '0';
                Block::insert($data);
            }
        }
        Logs::create(array('uid' => Auth::user()->id, 'type' => 'other', 'message' => '刷新模块区块'));
        return View::make('BackTheme::templates.message', array('message' => '刷新区块成功！', 'type' => 'success', 'url' => '/admin/block'));
    }

}
