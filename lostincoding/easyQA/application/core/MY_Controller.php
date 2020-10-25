<?php

/**
 * 自定义一般控制器,用于取代CI_Controller进行使用
 */
class MY_Controller extends CI_Controller
{
    protected $time = null;
    protected $now = null;
    protected $theme_id = 'default';
    protected $skin = null;
    protected $user = null;
    protected $msg_to_me_counts = 0;
    protected $data = null;
    protected $result = null;
    protected $topic = null;
    protected $keywords = null;
    protected $description = null;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('msg_model');

        $this->time = time();
        $this->now = date('Y-m-d H:i:s', $this->time);
        if (isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
            $this->data['user'] = $_SESSION['user'];
            //获取收到的消息数
            $this->msg_to_me_counts = $this->msg_model->gets_to_me_count($this->user['id'], 1);
            $this->data['msg_to_me_counts'] = $this->msg_to_me_counts;
        }
        //查看cookie中是否有自动登录
        else {
            //如果cookie中有自动登录
            if (!empty($_COOKIE['user_id'])) {
                //cookie需要解密
                $user_id = $this->simpleencrypt->decode($_COOKIE['user_id'], $this->config->config['encrypt_key']);
                $this->load->model('user_model');
                $this->load->model('skinsetting_model');
                //获取登录信息
                $user = $this->user_model->get($user_id);
                if (is_array($user)) {
                    //如果账号已被冻结
                    if ($user['freeze_status'] == 2) {
                        setcookie('user_id', '', time() - 3600, '/', '', false, true);
                        echo '账号已被冻结。';
                        exit;
                    }

                    //获取设置的皮肤
                    $_SESSION['skin'] = $this->skinsetting_model->get_by_userId($user['id']);

                    //设置session
                    $_SESSION['user'] = $user;
                    //从cookie中自动登录成功后直接跳转到工作台页面
                    redirect();
                } else {
                    setcookie('user_id', '', time() - 3600, '/', '', false, true);
                }
            }
        }

        //主题
        $this->data['theme_id'] = $this->theme_id;

        //未登录用户获取一个随机皮肤
        if (empty($_SESSION['skin']) && !isset($_SESSION['user'])) {
            $this->load->model('skin_model');
            //$rand_num = mt_rand(1, 51);
            $rand_num = 50;
            $skin = $this->skin_model->get($rand_num);
            if (is_array($skin)) {
                $skin['lock_background'] = 2;
            }
            $this->skin = $skin;
            $_SESSION['skin'] = $this->skin;
        }
        //皮肤
        if (isset($_SESSION['skin'])) {
            $this->skin = $_SESSION['skin'];
        }

        $this->data['skin'] = $this->skin;
        $this->data['config'] = $this->config->config;
        $this->data['active'] = null;
        $this->data['active_nav'] = null;
        $this->data['topic'] = $this->input->get('topic');
        $this->data['keywords'] = $this->config->config['site_info']['meta']['keywords'];
        $this->data['description'] = $this->config->config['site_info']['meta']['description'];
        $this->result['error_code'] = 'ok';
    }

    /**
     * 检验是否登录
     * @param  string  $url 未登录的跳转链接
     * @return boolean      已登录则返回true
     */
    protected function is_signin($url = 'account/signin')
    {
        if (isset($_SESSION['user'])) {
            return true;
        }
        redirect($url);
    }

    /**
     * 使得带有参数的url省略默认index方法可以正常访问,如直接访问task/13而不需要task/index/13
     */
    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        } else {
            //如果没有方法，则把$method压入第一个参数
            $arr1stParam = array($method);
            $params = array_merge($arr1stParam, $params);
            call_user_func_array(array($this, 'index'), $params);
        }
    }
}

//非MY开头的Controller不会自动加载,所以要在此处手动require加载
require_once 'U_Controller.php';
require_once 'API_Controller.php';
require_once 'BaseAPI_Controller.php';
require_once 'Admin_Controller.php';
require_once 'AdminAPI_Controller.php';
