<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Controller;
use Think\Controller;
use Think\Exception;
class ConfigController extends BaseController {
    /**
     +----------------------------------------------------------
     * 网站设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function index(){
        
        try {

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-网站设置',
                'breadcrumb'        => '&gt;网站设置',
                'pname_cn'			=> $cop['pname_cn'],
                'copyright_cn'		=> $cop['copyright_cn'],
                'version_major'		=> $cop['version_major'],
                'web_site'			=> C('WEB_SITE'),
                'is_reg'			=> C('IS_REG'),
                'reg_date'			=> C('REG_DATE'),
                'industry'			=> C('INDUSTRY'),
                'logo'				=> $cop['logo'],
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
       
    }
    /**
     +----------------------------------------------------------
     * 保存网站设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
   	public function save_website_config(){
   		$param = array(
   			'pname_cn'		=> I('post.pname_cn'),
   			'version_major'	=> I('post.version_major'),
   			'copyright_cn'	=> $_POST['copyright_cn'],
   			'web_site'		=> I('post.web_site'),
   			
   			'logo'			=> I('post.logo'),
   		);

   		try {
            $webconfig 		= D('WebConfig');
            $webconfig->save_website_config($param);
            $this->success('设置成功', U('Config/index'));
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        
            $this->error($e->getMessage(), U('Config/index'));
        }
   	}
   	/**
     +----------------------------------------------------------
     * 上传图片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function upload_img(){
       	$type = I('post.type');
        $imagename = I('post.imagename');
        $return_data    = array();
        try {
            $webconfig 		= D('WebConfig');
            $img_info = $webconfig->upload_logo($imagename, $type);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '上传成功',
                'data'          => $img_info,
            );
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 认证设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function auth(){
        try {

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             	=> $cop['pname_cn'].$cop['version_major'].'-认证设置',
                'breadcrumb'        	=> '&gt;认证设置',
                'default_merchant'		=> C('DEFAULT_MERCHANT'),
                'ad_count'				=> C('AD_COUNT'),
                'station_ad_count'		=> C('STATION_AD_COUNT'),
                'default_station_slide'	=> C('DEFAULT_STATION_SLIDE'),
                'default_ad'			=> C('DEFAULT_AD'),
                'homepage_banner'		=> C('HOMEPAGE_BANNER'),
                'qq_app_id'				=> C('QQ_APP_ID'),
                'qq_app_key'			=> C('QQ_APP_KEY'),
                'weibo_app_key'			=> C('WEIBO_APP_KEY'),
                'weibo_app_secret'		=> C('WEIBO_APP_SECRET'),

            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
       
    }
    /**
     +----------------------------------------------------------
     * 保存认证设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
   	public function save_auth_config(){

   		$param = array(
   			
   			
   			'qq_app_id'				=> I('post.qq_app_id'),
   			'qq_app_key'			=> I('post.qq_app_key'),
   			'weibo_app_key'			=> I('post.weibo_app_key'),
   			'weibo_app_secret'		=> I('post.weibo_app_secret'),
   		);
   		try {
            $webconfig 		= D('WebConfig');
            $webconfig->save_auth_config($param);
            $this->success('设置成功', U('Config/auth'));
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
            $this->error($e->getMessage(), U('Config/auth'));
        }
   	}
   	/**
     +----------------------------------------------------------
     * 短信设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function sms(){
        try {

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             	=> $cop['pname_cn'].$cop['version_major'].'-短信设置',
                'breadcrumb'        	=> '&gt;短信设置',
                'sms_message'			=> C('SMS_MESSAGE'),
                'sms_reg'				=> C('SMS_REG'),
                'sms_user'				=> C('SMS_USER'),
                'sms_password'			=> C('SMS_PASSWORD'),
                

            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }
       
    }
    /**
     +----------------------------------------------------------
     * 保存认证设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
   	public function save_sms_config(){

   		$param = array(
   			'sms_message'	=> I('post.sms_message'),
   			'sms_reg'		=> I('post.sms_reg'),
   			'sms_user'		=> I('post.sms_user'),
   			'sms_password'	=> I('post.sms_password'),
   			
   		);
   		try {
            $webconfig 		= D('WebConfig');
            $webconfig->save_sms_config($param);
            $this->success('设置成功', U('Config/sms'));
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
            $this->error($e->getMessage(), U('Config/sms'));
        }
   	}
    public function _empty(){
           $this->display('Empty:index');
    }
}