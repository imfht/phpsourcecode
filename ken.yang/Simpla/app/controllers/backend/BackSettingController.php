<?php

class BackSettingController extends BackBaseController {

    /**
     * 
     * 全局设置
     */
    public function index() {
        //读取model
        $site_name = Setting::find('site_name');
        $site_mail = Setting::find('site_mail');
        $site_description = Setting::find('site_description');
        $site_url = Setting::find('site_url');
        $site_logo = Setting::find('site_logo');
        $site_copyright = Setting::find('site_copyright');
        $site_tongji = Setting::find('site_tongji');
        $site_maintenance = Setting::find('site_maintenance');

        $user_is_allow_login = Setting::find('user_is_allow_login');
        $user_is_allow_register = Setting::find('user_is_allow_register');

        $admin_theme = Setting::find('admin_theme');
        $theme_default = Setting::find('theme_default');

        $home_list_num = Setting::find('home_list_num');
        $category_list_num = Setting::find('category_list_num');
        //默认显示
        $setting = array(
            'site_name' => $site_name->value,
            'site_mail' => $site_mail->value,
            'site_description' => $site_description->value,
            'site_url' => $site_url->value,
            'site_logo' => '/upload/site/' . $site_logo->value ? $site_logo->value : 'logo.png',
            'site_copyright' => $site_copyright->value,
            'site_tongji' => $site_tongji->value,
            'site_maintenance' => $site_maintenance->value,
            'user_is_allow_login' => $user_is_allow_login->value,
            'user_is_allow_register' => $user_is_allow_register->value,
            'admin_theme' => $admin_theme->value,
            'theme_default' => $theme_default->value,
            'home_list_num' => $home_list_num->value,
            'category_list_num' => $category_list_num->value,
        );
        if (Request::method() == 'POST') {
            $input = Input::all();
            //判断是否上传了图片
            if ($_FILES) {
                if ($_FILES['site_logo']['size'] != 0) {
                    $file_name = Image::upload($_FILES['site_logo'], 'upload/site/', 'logo', 300);
                    $input['site_logo'] = $file_name;
                    $site_logo->value = $input['site_logo'];
                    $site_logo->save();
                }
            }

            //数据赋值
            $site_name->value = $input['site_name'];
            $site_mail->value = $input['site_mail'];
            $site_description->value = $input['site_description'];
            $site_url->value = $input['site_url'];
            $site_copyright->value = $input['site_copyright'];
            $site_tongji->value = $input['site_tongji'];
            $user_is_allow_login->value = $input['user_is_allow_login'];
            $user_is_allow_register->value = $input['user_is_allow_register'];
            $admin_theme->value = $input['admin_theme'];
            $theme_default->value = $input['theme_default'];
            $home_list_num->value = $input['home_list_num'];
            $category_list_num->value = $input['category_list_num'];
            //保存数据
            $site_name->save();
            $site_mail->save();
            $site_description->save();
            $site_url->save();

            $site_copyright->save();
            $site_tongji->save();
            $user_is_allow_login->save();
            $user_is_allow_register->save();
            $admin_theme->save();
            $theme_default->save();
            $home_list_num->save();
            $category_list_num->save();

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '站点设置'));
            return View::make('BackTheme::templates.message', array('message' => '保存设置成功！', 'type' => 'success', 'url' => '/admin/setting'));
        }
        return View::make('BackTheme::templates.setting/index')->with('setting', $setting);
    }

    /**
     * ----------------------------------------------------------------
     * 主题设置
     * @param string $set_default_name 系统默认主题
     * @return type
     */
    public function theme($set_default_name = null) {
        //获取系统默认主题
        $admin_theme = Setting::find('admin_theme');
        $theme_default = Setting::find('theme_default');

        //判断是否是提交表单
        if ($set_default_name != null) {
            //保存数据
            $theme_default->value = $set_default_name;
            $theme_default->save();

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '主题设置,设置为新主题:' . $set_default_name));
            return View::make('BackTheme::templates.message', array('message' => '设置成功！', 'type' => 'success', 'url' => '/admin/setting/theme'));
        }
        //获取主题下面的所有主题
        $theme_list = Base::get_all_themes();

        return View::make('BackTheme::templates.setting/theme/index', array('theme_list' => $theme_list, 'admin_theme' => $admin_theme->value, 'theme_default' => $theme_default->value));
    }

    /**
     * --------------------------------------------------------------
     * 模块设置
     * @param string $module_name 模块名字
     * @return string  open,close,install,uninstall
     */
    public function module($module_name = null, $type = null) {
        //判断是否是提交表单
        $enabled = null;
        if ($module_name != null && $type != null) {
            switch ($type) {
                case 'open':
                    $message = '开启模块成功';
                    $enabled = true;
                    break;
                case 'close':
                    $message = '关闭模块成功';
                    $enabled = false;
                    break;
                case 'install':
                    $message = '安装模块成功';
                    break;
                case 'uninstall':
                    $message = '删除模块成功';
                    break;
            }
            Setting::module_handle($module_name, $type, $enabled);

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '模块设置,模块为:' . $module_name));
            return View::make('BackTheme::templates.message', array('message' => $message, 'type' => 'success', 'url' => '/admin/setting/module'));
        }
        //获取模块下面的所有模块
        $module_list = Base::get_all_modules();

        //print_r($module_list);die();
        return View::make('BackTheme::templates.setting/module/index', array('module_list' => $module_list));
    }

    /**
     * 刷新所有模块
     */
    public function module_refresh_all() {
        //获取所有模块下面的module.php文件
        $modules_block = array();
        $dir = dirname(__DIR__) . '/modules';
        $modules_list = Base::get_file_list($dir);
        foreach ($modules_list as $row) {
            if (file_exists(dirname(__DIR__) . '/modules/' . $row . '/module.php')) {
                require_once dirname(__DIR__) . '/modules/' . $row . '/module.php';
                $module_hook = get_class_methods($row . '_module');
            } else {
                continue;
            }
        }

        //将区块信息写入数据库
        foreach ($modules_block as $row) {
            //1、判断接口必填字段是否存在
            if (!$row['description'] || !$row['machine_name'] || !$row['callback']) {
                continue;
            }
            //2、对比数据库数据
            $result = Block::where('machine_name', '=', $row['machine_name'])->first()->toArray();
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
        Logs::create(array('uid' => Auth::user()->id, 'type' => 'other', 'message' => '刷新所有模块'));
        return View::make('BackTheme::templates.message', array('message' => '刷新所有模块成功！', 'type' => 'success', 'url' => '/admin/block'));
    }

    /**
     * -------------------------------------------------------------------------
     * SEO设置
     * @return type
     */
    public function seo() {
        $home_seo = Seo::find(1);
        //判断是否是提交表单
        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'title' => 'max:256',
                'description' => 'max:256',
                'keywords' => 'max:256',
            );
            $messages = array(
                'title.max' => '标题最多只能输入:max个字符',
                'description.max' => '描述最多只能输入:max个字符',
                'keywords.max' => '关键字最多只能输入:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->with('home_seo', $home_seo)->withInput();
            }
            $seo_data = array();
            $seo_data['title'] = $input['seo_title'];
            $seo_data['description'] = $input['seo_description'];
            $seo_data['keywords'] = $input['seo_keywords'];
            Seo::where('type', '=', 'home')->update($seo_data);

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => 'SEO设置'));
            return View::make('BackTheme::templates.message', array('message' => '保存成功！', 'type' => 'success', 'url' => '/admin/setting/seo'));
        }

        return View::make('BackTheme::templates.setting/seo/index', array('home_seo' => $home_seo));
    }

    /**
     * --------------------------------------------------------------------------
     * 缓存设置页面
     * @return type
     */
    public function cache() {
        //获取缓存设置
        $site_cache = Setting::find('site_cache');
        //判断是否是提交表单
        if (Request:: method() == 'POST') {
            $input = Input::all();
            $site_cache->value = $input['site_cache_value'];
            $site_cache->status = $input['site_cache_status'];
            $site_cache->extend = $input['site_cache_extend'];
            $site_cache->save();

            Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '缓存页面设置'));
            return View::make('BackTheme::templates.message', array('message' => '保存设置成功！', 'type' => 'success', 'url' => '/admin/setting/cache'));
        }

        return View::make('BackTheme::templates.setting/cache/index')->with('site_cache', $site_cache);
    }

    /**
     * 清除缓存
     */
    public function cache_clear() {
        Cache::flush();
        Logs::create(array('uid' => Auth::user()->id, 'type' => 'edit', 'message' => '清除所有缓存'));
        return View::make('BackTheme::templates.message', array('message' => '清除所有缓存成功！', 'type' => 'success', 'url' => '/admin/setting/cache'));
    }

    /**
     * --------------------------------------------------------------------------
     * 邮件SMTP设置
     * @return type
     */
    public function email() {
        //判断是否是提交表单
        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'host' => 'required|max:100',
                'port' => 'required|max:10',
                'username' => 'required|max:30',
                'password' => 'required|max:20',
                'from_address' => 'required|max:100|email',
                'from_name' => 'required|max:30',
            );
            $messages = array(
                'host.required' => '必须填写主机地址',
                'host.max' => '主机地址最多只能输入:max个字符',
                'port.required' => '必须填写端口号',
                'hoportst.max' => '端口号最多只能输入:max个字符',
                'username.required' => '必须填写用户名',
                'username.max' => '用户名最多只能输入:max个字符',
                'password.required' => '必须填写密码',
                'password.max' => '密码最多只能输入:max个字符',
                'from_address.required' => '必须填写发送地址',
                'from_address.max' => '发送地址最多只能输入:max个字符',
                'from_address.email' => '发送地址必须为邮箱',
                'from_name.required' => '必须填写发送名字',
                'from_name.max' => '发送名字最多只能输入:max个字符',
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $input['pretend'] = isset($input['pretend']) ? 'true' : 'false';
            //1、替换数据
            $mail_setting_dir = dirname(dirname(__DIR__)) . '/config/mail_setting.php';
            $mail_setting = file_get_contents($mail_setting_dir);
            $mail_setting = str_replace('{{host}}', $input['host'], $mail_setting);
            $mail_setting = str_replace('{{port}}', $input['port'], $mail_setting);
            $mail_setting = str_replace('{{from_address}}', $input['from_address'], $mail_setting);
            $mail_setting = str_replace('{{from_name}}', $input['from_name'], $mail_setting);
            $mail_setting = str_replace('{{username}}', $input['username'], $mail_setting);
            $mail_setting = str_replace('{{password}}', $input['password'], $mail_setting);
            $mail_setting = str_replace('{{pretend}}', $input['pretend'], $mail_setting);
            //2、写入数据
            $mail = dirname(dirname(__DIR__)) . '/config/mail.php';
            file_put_contents($mail, $mail_setting);

            return View::make('BackTheme::templates.message', array('message' => '保存成功', 'type' => 'success', 'url' => '/admin/setting/email'));
        }

        $email = array(
            'host' => Config::get('mail.host'),
            'port' => Config::get('mail.port', '25'),
            'from_address' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name'),
            'username' => Config::get('mail.username'),
            'password' => Config::get('mail.password'),
            'pretend' => Config::get('mail.pretend'),
        );

        return View::make('BackTheme::templates.setting/email/index', $email);
    }

    /**
     * 发送测试邮件
     * @return type
     */
    public function email_send_test() {
        try {
            $input = Input::all();
            $data = array(
                'email' => $input['email'],
                'siteName' => Setting::find('site_name'),
            );
            $content = '这是一封测试邮件，来自于' . $data['siteName'] . ',请不要回复';
            Mail::queue('email', $content, function($message) use ($data) {
                $message->to($data['email'], $data['siteName'])->subject('测试邮件 --' . $data['siteName']);
            });
            $message = '发送邮件成功';
            $type = 'success';
        } catch (Exception $e) {
            $message = '发送邮件失败';
            $type = 'error';
        }

        return View::make('BackTheme::templates.message', array('message' => $message, 'type' => $type, 'url' => '/admin/setting/email'));
    }

    /**
     * 
     * @param string $type  up为开启站点，down为关闭站点
     */
    public function maintenance($type) {
        $site_maintenance = Setting::find('site_maintenance');
        if ($type == 'up') {
            try {
                $site_maintenance->value = '0';
                $site_maintenance->save();
                Artisan::call('up');
                $message = '开启站点成功';
                $type = 'success';
            } catch (Exception $e) {
                $message = '开启站点失败';
                $type = 'error';
            }
        } else {
            try {
                $site_maintenance->value = '1';
                $site_maintenance->save();
                Artisan::call('down');
                $message = '关闭站点成功';
                $type = 'success';
            } catch (Exception $e) {
                $message = '关闭站点失败';
                $type = 'error';
            }
        }
        return View::make('BackTheme::templates.message', array('message' => $message, 'type' => $type, 'url' => '/admin/setting'));
    }

}
