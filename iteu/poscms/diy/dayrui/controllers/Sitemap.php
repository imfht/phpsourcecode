<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


 
class Sitemap extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 网站地图
     */
    public function index() {
        $file = WEBPATH.'cache/data/sitemap-'.SITE_ID.'.html';
        // 系统开启静态首页、非手机端访问、静态文件不存在时，才生成文件
		if (!$this->template->mobile && !is_file($file)) {
            ob_start();
            $this->template->assign(array(
                'meta_title' => '网站地图'.SITE_SEOJOIN.SITE_TITLE,
                'meta_keywords' => SITE_KEYWORDS,
                'meta_description' => SITE_DESCRIPTION,
            ));
            $this->template->display('sitemap.html');
            $html = ob_get_clean();
            @file_put_contents($file, $html, LOCK_EX);
            echo $html;exit;
		} else {
			$this->template->assign(array(
                'meta_title' => '网站地图'.SITE_SEOJOIN.SITE_TITLE,
				'meta_keywords' => SITE_KEYWORDS,
				'meta_description' => SITE_DESCRIPTION,
			));
			$this->template->display('sitemap.html');
		}
    }

}