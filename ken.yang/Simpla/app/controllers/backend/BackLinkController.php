<?php

/*
 * 友情链接
 */

class BackLinkController extends BackBaseController {

    /**
     * 列表
     * @return type
     */
    public function index() {
        $links = Link::orderBy('weight', 'desc')->select()->paginate(20);
        return View::make("BackTheme::templates.link.index", array('links' => $links));
    }

    /**
     * 添加
     * @return response
     */
    public function add() {
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'url' => 'required|max:256',
                'description' => 'max:128',
                'weight' => 'required|max:10',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'url.required' => '必须填写URL地址',
                'url.max' => 'URL地址最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'weight.required' => '必须添加位置',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //判断是否上传了图片
            if ($_FILES) {
                if ($_FILES['image']['size'] != 0) {
                    $file_name = Image::upload($_FILES['image'], 'upload/link/');
                    $input['image'] = $file_name;
                }
            }

            $data = array();
            $data['title'] = $input['title'];
            $data['url'] = $input['url'];
            $data['description'] = $input['description'];
            $data['image'] = $input['image'];
            $data['weight'] = $input['weight'];
            $result = Link::create($data);

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'add', 'message' => '添加友情连接,ID为' . $result['id']));
            return View::make('BackTheme::templates.message', array('message' => '添加友情连接成功！', 'type' => 'success', 'url' => '/admin/link'));
        }
        return View::make("BackTheme::templates.link.add");
    }

    /**
     * 编辑
     * @param int $id
     * @return response
     */
    public function edit($id) {
        $link = Link::where('id', $id)->first();
        if (Request::method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'url' => 'required|max:256',
                'description' => 'max:128',
                'weight' => 'required|max:10',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'url.required' => '必须填写URL地址',
                'url.max' => 'URL地址最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'weight.required' => '必须添加位置',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            //判断是否上传了图片
            if ($_FILES) {
                if ($_FILES['image']['size'] != 0) {
                    $file_name = Image::upload($_FILES['image'], 'upload/link/');
                    $input['image'] = $file_name;
                }
            }


            $link->title = $input['title'];
            $link->url = $input['url'];
            $link->description = $input['description'];
            $link->image = $input['image'];
            $link->weight = $input['weight'];
            $result = $link->save();

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '编辑友情连接,ID为' . $result['id']));
            return View::make('BackTheme::templates.message', array('message' => '编辑友情连接成功！', 'type' => 'success', 'url' => '/admin/link'));
        }
        return View::make("BackTheme::templates.link.edit", array('link' => $link));
    }

    /**
     * 删除
     * @param int $id
     * @return response
     */
    public function delete($id) {
        $link = Link::find($id);
        $result = $link->delete();
        if ($result) {
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'delete', 'message' => '删除友情连接,ID为' . $id));
            return Redirect::to('/admin/link');
        }

        return View::make('BackTheme::templates.message', array('message' => '删除友情连接失败！', 'type' => 'error', 'url' => '/admin/link'));
    }

}
