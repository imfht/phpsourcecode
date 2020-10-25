<?php

/*
 * 评论：第三方评论配置
 */

class BackCommentController extends BackBaseController {

    /**
     * 评论设置
     * @return array
     */
    public function index() {
        if (Request::method() == 'POST') {
            $input = Input::all();
            $comment_data = array();
            $comment_data['value'] = $input['site_comment_value'];
            $comment_data['status'] = $input['site_comment_status'];
            Setting::where('name', '=', 'site_comment')->update($comment_data);
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '评论设置'));
            return View::make('BackTheme::templates.message', array('message' => '保存成功！', 'type' => 'success', 'url' => '/admin/setting/comment'));
        }
        $site_comment = Setting::find('site_comment');
        return View::make('BackTheme::templates.setting/comment/index', array('site_comment' => $site_comment));
    }

    /**
     * 第三方评论编辑
     * @param type $name
     * @return type
     */
    public function edit($name) {

        if (Request::method() == 'POST') {
            $input = Input::all();
            $comment_data = array();
            $comment_data['choose'] = $input['comment_choose'];
            $comment_data['code_one'] = $input['comment_code_one'];
            $comment_data['code_two'] = $input['comment_code_two'];
            Comment::where('machine_name', '=', $name)->update($comment_data);
            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '第三方评论设置'));
            return View::make('BackTheme::templates.message', array('message' => '保存成功！', 'type' => 'success', 'url' => '/admin/setting/comment/' . $name));
        }
        $comment = Comment::where('machine_name', '=', $name)->get()->toArray();
        return View::make('BackTheme::templates.setting/comment/edit', array('comment' => $comment[0]));
    }

}
