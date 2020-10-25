<?php

/*
 * 系统安装
 */

class InstallController extends Controller {

    public function __construct() {
        View::addNamespace('InstallTheme', dirname(dirname(__DIR__)) . '/views/install/');
        View::share('version', 'V0.2');

        //通过检测app/lock.txt来判断程序是否已经安装
        if (file_exists(dirname(dirname(__DIR__)) . '/lock.txt') && !strpos(Request::url(), 'step4')) {
            View::share('title', 'Simpla安装向导：消息提示');
            $html = View::make('InstallTheme::templates.message');
            echo $html;
            exit;
        }

        /**
         * 定义静态变量
         */
        define('NOW_FORMAT_TIME', date('Y-m-d H:i:d', time()));
        define('NOW_TIME', time());
    }

    /**
     * 第一步：内容信息展示
     */
    public function index() {
        View::share('title', 'Simpla安装向导：第一步');
        return View::make('InstallTheme::templates.index');
    }

    /**
     * 第二步：检测环境
     */
    public function check() {
        View::share('title', 'Simpla安装向导：第二步');

        //获取配置环境及需求
        $lowestEnvironment = Install::_getLowestEnvironment();
        $currentEnvironment = Install::_getCurrentEnvironment();
        $recommendEnvironment = Install::_getRecommendEnvironment();

        $check_pass = true;
        foreach ($currentEnvironment as $key => $value) {
            if (false !== strpos($key, '_ischeck') && false === $value)
                $check_pass = false;
        }

        return View::make('InstallTheme::templates.check', array('lowestEnvironment' => $lowestEnvironment, 'currentEnvironment' => $currentEnvironment, 'recommendEnvironment' => $recommendEnvironment));
    }

    /**
     * 第三步：填写信息和安装
     */
    public function info() {
        View::share('title', 'Simpla安装向导：第三步');

        if (Request:: method() == 'POST') {
            $input = Input::all();
            $rules = array(
                'db_hostname' => 'required|max:100',
                'db_name' => 'required|max:20',
                'db_username' => 'required|max:30',
                'db_password' => 'max:30',
                'db_prefix' => 'required|max:10',
                'username' => 'required|max:20',
                'email' => 'required|max:64',
                'password' => 'required|min:6|max:20|confirmed|alpha_dash',
                'password_confirmation' => 'required'
            );
            $messages = array(
                'db_hostname.required' => '必须填写数据库主机',
                'db_hostname.max' => '数据库主机最多只能输入:max个字符',
                'db_name.required' => '必须填写数数据库名字',
                'db_name.max' => '数据库名字最多只能输入:max个字符',
                'db_username.required' => '必须填写数据库用户名',
                'db_username.max' => '数据库用户名最多只能输入:max个字符',
                'db_password.max' => '数据库密码最多只能输入:max个字符',
                'db_prefix.required' => '必须填写数据库前缀',
                'db_prefix.max' => '数据库前缀最多只能输入:max个字符',
                'username.required' => '必须填写管理员用户名',
                'username.max' => '管理员用户名最多只能输入:max个字符',
                'email.required' => '必须填写管理员邮箱',
                'email.max' => '管理员邮箱最多只能输入:max个字符',
                'password.required' => '必须填写密码',
                'password.min' => '密码最少为:min个字符',
                'password.max' => '密码最少为:max个字符',
                'password.confirmed' => '两次输入的密码不一样',
                'password.alpha_dash' => '密码仅允许字母、数字、破折号（-）以及底线（_）',
                'password_confirmation.required' => '必须填写两次密码'
            );
            //进行字段验证
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                Input::flash();
                return Redirect::back()->withErrors($validator)->withInput();
            }

            //1、替换数据
            $database_setting_dir = dirname(dirname(__DIR__)) . '/config/database_setting.php';
            $database_setting = file_get_contents($database_setting_dir);
            $database_setting = str_replace('{{mysql_host}}', $input['db_hostname'], $database_setting);
            $database_setting = str_replace('{{mysql_database}}', $input['db_name'], $database_setting);
            $database_setting = str_replace('{{mysql_username}}', $input['db_username'], $database_setting);
            $database_setting = str_replace('{{mysql_password}}', $input['db_password'], $database_setting);
            $database_setting = str_replace('{{mysql_prefix}}', $input['db_prefix'], $database_setting);
            //2、写入数据
            $mail = dirname(dirname(__DIR__)) . '/config/database.php';
            file_put_contents($mail, $database_setting);

            try {
                DB::connection();
            } catch (Exception $e) {
                return Redirect::back()->withErrors(['数据库连接失败,请检查后再次安装!'])->withInput();
            }

            //进行安装
            try {
                //开始事务
                DB::beginTransaction();
                //通过文件安装
                $simpla_sql = File::get(dirname(dirname(__DIR__)) . '/database/simpla.sql');
                $db_prefix = DB::connection()->getTablePrefix();
                $simpla_sql = str_replace("\r", "\n", str_replace('#__', $db_prefix, $simpla_sql));
                DB::unprepared($simpla_sql);
                //添加管理员
                $data = array();
                $data['username'] = $input['username'];
                $data['password'] = Hash::make($input['password']);
                $data['email'] = $input['email'];
                $data['created_at'] = NOW_FORMAT_TIME;
                $data['updated_at'] = NOW_FORMAT_TIME;
                $uid = DB::table('users')->insertGetId($data);
                //添加角色
                $data_role = array('uid' => $uid, 'rid' => 3);
                UserRoles::create($data_role);
                //安装完成，添加锁定文件
                File::put(dirname(dirname(__DIR__)) . '/lock.txt', '这是一个锁定文件，请不要删除!如果要重新安装，请删除该文件!');
            } catch (Exception $e) {
                //事务回滚
                DB::rollback();
                return Redirect::back()->withErrors(['数据库安装失败，请检查后重新安装!'])->withInput();
            }

            //提交事务
            DB::commit();

            //安装成功替换app数据
            //1、获取url和key值
            $app_url = 'http://' . Request::server('SERVER_NAME');
            $app_key = md5(rand(100000, 999999));
            //2、替换数据
            $app_dir = dirname(dirname(__DIR__)) . '/config/app.php';
            $app_setting = file_get_contents($app_dir);
            $app_setting = str_replace('{{url}}', $app_url, $app_setting);
            $app_setting = str_replace('{{key}}', $app_key, $app_setting);
            //3、写入数据
            file_put_contents($app_dir, $app_setting);

            //安装成功
            return Redirect::to('/install/step4');
        }

        //检查数据库连接
        try {
            $result = DB::connection()->getDatabaseName();
            $db_connection = true;
            $message = '';
        } catch (Exception $e) {
            //echo $e->getMessage();
            $db_connection = false;
            $message = $e->getMessage();
        }
        return View::make('InstallTheme::templates.info', array('db_connection' => $db_connection, 'message' => $message));
    }

    /**
     * 第四步：安装
     */
    public function success() {
        View::share('title', 'Simpla安装向导：安装完成');
        return View::make('InstallTheme::templates.success');
    }

}
