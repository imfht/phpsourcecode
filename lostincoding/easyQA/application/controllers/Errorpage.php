<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Errorpage extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($status_code = 404)
    {
        if ($status_code == 404) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
        }
        $this->data['title'] = "Error {$status_code}";
        $this->load->view("{$this->theme_id}/{$status_code}", $this->data);
    }
}
