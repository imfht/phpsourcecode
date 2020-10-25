<?php

class ContactController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        View::share('title', '联系我们-');
        View::share('description', '有任何问题或者疑问，请联系我们');

        if (Request:: method() == 'POST') {
            //进行提交次数验证
            if (Session::has('contact_num')) {
                $contact_num = Session::get('contact_num');
            } else {
                $contact_num = 0;
            }

            if ($contact_num >= 3) {
                return View::make('Theme::templates/message', array('message' => '你已经提交过了，请稍后再次提交！', 'type' => 'info', 'url' => '/'));
            }
            $input = Input::all();
            $rules = array(
                'title' => 'required|max:32',
                'people' => 'max:256',
                'contact' => 'max:256',
                'body' => 'required|max:1000',
            );
            $messages = array(
                'title.required' => '必须填写标题',
                'title.max' => '标题最多只能输入:max个字符',
                'people.max' => '联系人最多只能输入:max个字符',
                'contact.max' => '联系方式最多只能输入:max个字符',
                'body.required' => '必须填写内容',
                'body.max' => '内容最多只能输入:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return Redirect::to('/contact')->withErrors($validator);
            }
            //提交数据
            $data = array();
            $data['title'] = $input['title'];
            $data['people'] = $input['people'];
            $data['contact'] = $input['contact'];
            $data['body'] = $input['body'];
            $data['created_at'] = NOW_FORMAT_TIME;
            $result = Contact::create($data);
            if ($result) {
                //提交成功记录SESSION，最多能提交3次
                $contact_num++;
                Session::put('contact_num', $contact_num);
                return View::make('Theme::templates/message', array('message' => '提交成功！', 'type' => 'success', 'url' => '/'));
            }
        }

        return View::make('contact::contact');
    }

    public function admin() {
        $list = Contact::paginate(20);
        $paginate = $list->links();
        return View::make("contact::admin.list", array('list' => $list, 'paginate' => $paginate));
    }

}
