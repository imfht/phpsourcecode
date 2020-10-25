<?php

class TB_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
}

class TB_Admin extends TB_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('TrackBlog');
        if (isset($_SESSION['username'])) {
            return true;
        } else {
            redirect('/admin/admin/login');
        }
    }

}