<?php
class TrackBlog
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('url');
    }

    public function showMessage($type = 'default', $content = '', $url = '/')
    {
        $data['type'] = $type;
        $data['content'] = $content;
        $data['url'] = $url;
        $this->CI->load->view('admin/showmessage', $data);
    }
}