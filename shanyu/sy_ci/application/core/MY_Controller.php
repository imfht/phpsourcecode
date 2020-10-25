<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    protected function page_html($base_url='',$url_prfix='',$total='',$limit=10){
        if(!$base_url || !$url_prfix || !$total) show_error('page_html error');
        $this->load->library('pagination');
        $page_config=array();
        $page_config['base_url'] = $base_url;
        $page_config['total_rows'] = $total;
        $page_config['per_page'] = $limit;
        $page_config['suffix'] = '.html';
        $page_config['prefix'] = $url_prfix.'_';
        $page_config['first_url'] = $base_url.$url_prfix.'.html';
        $page_config['num_links'] = 6;
        $page_config['prev_link'] = FALSE;
        $page_config['next_link'] = FALSE;
        $page_config['data_page_attr'] = 'data-page';
        $page_config['cur_tag_open'] = '<li class="active"><span>';
        $page_config['cur_tag_close'] = '</span></li>';
        $page_config['num_tag_open'] = '<li>';
        $page_config['num_tag_close'] = '</li>';
        $page_config['prev_tag_open'] = '<li>';
        $page_config['prev_tag_close'] = '</li>';
        $page_config['next_tag_open'] = '<li>';
        $page_config['next_tag_close'] = '</li>';
        $page_config['use_page_numbers'] = TRUE;
        $page_config['attributes']['rel'] = FALSE;
        $page_config['last_link'] = FALSE;
        $page_config['first_link'] = FALSE;
        $this->pagination->initialize($page_config);
        return $this->pagination->create_links();
    }

    public function layout($view='',$data=array()){
        $category_list=$data['category_list']=config_item('category_list');
        $seo=config_item('seo');
        isset($data['nav']) or $data['nav']=array();
        if( array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'] ){
            $this->load->view($view,$data); 
        }else{
            $this->load->view('header',compact('seo','category_list'));
            $this->load->view($view,$data);
            $this->load->view('footer');
        }
    }
}