<?php

/*
 * 搜索功能
 */

class SearchController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @return type
     */
    public function index() {
        //SEO
        View::share('title', '搜索');
        View::share('description', '通过搜索查找你想要的内容');

        $key = '';
        $input = Input::all();
        if (isset($input['key'])) {

            $rules = array(
                'key' => 'required|max:10|min:2',
            );
            $messages = array(
                'key.required' => '必须填写标题',
                'key.max' => '标题最多只能输入:max个字符',
                'key.min' => '标题最多只能输入:min个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return Redirect::to('/search')->withErrors($validator);
            }

            $key = trim($input['key']);
            $key = (!get_magic_quotes_gpc()) ? addslashes($key) : $key;

            $results = Node::where('status', '=', 1)->where('title', 'like', "%$key%")->orWhere('body', 'like', "%$key%")->paginate(10);
            //获取分页
            $paginate = $results->links();

            $template = Theme::template('search');
            return View::make($template, array('key' => $key, 'result' => $results, 'paginate' => $paginate));
        }
        $template = Theme::template('search');
        return View::make($template, array('key' => $key));
    }

}
