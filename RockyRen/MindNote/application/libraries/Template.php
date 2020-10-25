<?php

class Template
{
    public function __construct(){
        $this->CI = & get_instance();
        $this->CI->load->helper('url');
        $this->header_view_src = 'template/header';
        $this->footer_view_src = 'template/footer';
    }

    /**
     * 整合内容区和预设的页头和页尾一并输出
     * @param $content_src
     * @param null $data
     */
    public function output($content_src, $data=null){
        $this->setData($data);
        $this->CI->load->view($this->header_view_src, $data);
        $this->CI->load->view($content_src, $data);
        $this->CI->load->view($this->footer_view_src);

    }

    /**
     * 设置页头模板的路径，会覆盖默认配置
     * @param $header_src
     * @param null $data
     */
    public function set_header_view_src($header_src, $data = null){
        $this->header_view_src = $header_src;
        $this->setData($data);
    }

    /**
     * 设置页尾模板的路径，会覆盖默认配置
     * @param $footer_src
     * @param null $data
     */
    public function set_footer_view_src($footer_src, $data = null){
        $this->footer_view_src = $footer_src;
        $this->setData($data);
    }

    /**
     * 设置内部的与view层相关的变量数组
     * @param $data
     */
    protected function setData($data){
        if( is_array($data) )
        {
        $this->data = array_merge(array($this->data), $data);
        }
    }

    private $CI;   //引用CI中的超级对象
    private $header_view_src; //页头模板的路径
    private $footer_view_src; //页尾的模板路径
    private $data;  //输出到view层变量数组

}