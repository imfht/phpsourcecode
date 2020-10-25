<?php
namespace app\home\controller;
use app\home\controller\SiteController;
/**
 * 站点首页
 */

class IndexController extends SiteController {

	/**
     * 主页
     */
    public function index(){
    	//MEDIA信息
        $media=$this->getMedia();
    	$this->assign('media', $media);
        $this->siteDisplay(config('tpl_index'));
    }

    /**
     * 页面不存在
     */
    public function error404(){
    	$this->siteDisplay('error_404');
    }
}