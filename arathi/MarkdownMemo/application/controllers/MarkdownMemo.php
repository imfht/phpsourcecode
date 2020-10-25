<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MarkdownMemo extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
    }

    public function index(){
        $baseUrl = base_url();
        if ($baseUrl == null) {
            $baseUrl = "";
        }

        //TODO 从数据库获取CDN
        $jqueryMobileCssCDN = "";
        $jqueryCDN = "";
        $jqueryMobileJsCDN = "";
        $showdownCDN = "";
        if ($jqueryMobileCssCDN==null || $jqueryMobileCssCDN == ""){
            $jqueryMobileCssCDN = $baseUrl . "/assets/jquery.mobile.min.css";
        }
        if ($jqueryCDN==null || $jqueryCDN == ""){
            $jqueryCDN = $baseUrl . "/assets/jquery.min.js";
        }
        if ($jqueryMobileJsCDN==null || $jqueryMobileJsCDN == ""){
            $jqueryMobileJsCDN = $baseUrl . "/assets/jquery.mobile.min.js";
        }
        if ($showdownCDN==null || $showdownCDN == ""){
            $showdownCDN = $baseUrl . "/assets/showdown.min.js";
        }

        //TODO 获取当前用户的配置
        $darkness = false;
        $theme = $darkness?'b':'a';

        $data = array(
            'baseUrl' => $baseUrl,
            'siteUrl' => site_url(),
            'production' => false,
            'theme' => $theme,
            'jqueryMobileCssCDN' => $jqueryMobileCssCDN,
            'jqueryCDN' => $jqueryCDN,
            'jqueryMobileJsCDN' => $jqueryMobileJsCDN,
            'showdownCDN' => $showdownCDN
        );
        $this->load->view('frontend', $data);
    }
}
