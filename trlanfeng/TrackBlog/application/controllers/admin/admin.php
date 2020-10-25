<?php

/**
 * 管理类
 */
class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //加载model
        $this->load->model('admin_model');
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->helper('url');
    }

    public function index()
    {
        if ($this->check_login()) {
            redirect('/admin/index');
        } else {
            $this->login();
        }
    }

    public function check_login()
    {
        if (isset($_SESSION['username'])) {
            redirect('/admin/index');
        } else {
            if (isset($_POST['submit'])) {
                $this->form_validation->set_rules('username', '用户名', 'required|max_length[16]');
                $this->form_validation->set_rules('password', '密码', 'required|max_length[16]');
                if ($this->form_validation->run() === FALSE) {
                    return false;
                } else {
                    $data = $this->admin_model->getOne($_POST['username']);
                    $password = md5($_POST['password']);
                    $password = substr($password, 0, strlen($password) - 2);
                    if ($data['passwd'] === $password) {
                        $_SESSION['username'] = $_POST['username'];
                        redirect('/admin');
                    } else {
                        $this->login();
                    }
                }
            } else {
                redirect('/admin/index');
            }
        }
    }

    public function login()
    {
        $this->load->view('admin/login');
    }

    public function logout()
    {
        session_destroy();
        $this->index();
    }
}

?>