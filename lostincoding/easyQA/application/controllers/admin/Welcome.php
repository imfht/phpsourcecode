<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect('admin/article');
    }
}
