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
class MerchantController extends BaseController {
    /**
     +----------------------------------------------------------
     * 公共认证设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function auth_set(){
        $merchant         = D('Merchant');
        try{
            $info = $merchant->get_auth_info();
            
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-认证设置',
                'info'              => $info,
                'breadcrumb'        => '&gt;认证设置'
            ));
        $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
        
        
    }
    /**
     +----------------------------------------------------------
     * 公共认证页面设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function page_set(){
        $merchant         = D('Merchant');
        try{
            $info = $merchant->get_page_info();
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-认证页面设置',
                'info'              => $info,
                'breadcrumb'        => '&gt;认证页面设置'
            ));
        $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
        
        
    }
  
    /**
     +----------------------------------------------------------
     * 获取当前登录商家的广告
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function ad_set(){
        $merchant         = D('Merchant');
        $return_data    = array();
        try{
            $list = $merchant->get_ad_list();
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-认证广告设置',
                'list'          => $list,
                'breadcrumb'        => '&gt;认证广告设置'
            ));
            $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
     
    }
    /**
     +----------------------------------------------------------
     * 认证页面设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function save_page(){
        $param = array(
            'shop_name'         => I('post.shop_name'),
            'telephone'         => I('post.telephone'),
            'homepage_logo'     => I('post.homepage_logo'),
            'homepage_banner'   => I('post.homepage_banner'),
        );
      
        $return_data    = array();
        try {
            $merchant         = D('Merchant');
            $merchant->save_page($param);
            $this->success('页面设置成功', U('Merchant/page_set'));
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
            $this->error($e->getMessage(), U('Merchant/page_set'));
        }
    }

    /**
     +----------------------------------------------------------
     * 认证设置
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function save_auth(){

        $param = array(
            'qq_verify'         => I('post.qq_verify'),
            'weibo_verify'      => I('post.weibo_verify'),
            'weixin_verify'     => I('post.weixin_verify'),
            'mobile_verify'     => I('post.mobile_verify'),
            'akey_verify'       => I('post.akey_verify'),
            'virtual_verify'    => I('post.virtual_verify'),
            'weixin_name'       => I('post.weixin_name'),
            'rest_online_times' => I('post.rest_online_times'),
            'online_type'       => I('post.online_type'),
            'online_times'      => I('post.online_times'),
            'online_times1'     => I('post.online_times1'),
            'href'              => I('post.href'),
            'href_website'      => I('post.href_website'),
            'weibo_name'		=> I('post.weibo_name'),
            'qr_code'           => I('post.qr_code'),
            'ad_times'          => I('post.ad_times'),
            'one_auth_type'		=> I('post.one_auth_type'),
            'one_auth_href'		=> I('post.one_auth_href'),
            'two_auth_type'		=> I('post.two_auth_type'),
            'two_auth_href'		=> I('post.two_auth_href'),
            'old_user_auth_type'=> I('post.old_user_auth_type'),
            'ad_status'			=> I('post.ad_status'),
        );



        $return_data    = array();
        try {
            $merchant         = D('Merchant');
            $merchant->save_auth($param);
            $this->success('认证设置成功', U('Merchant/auth_set'));
        } catch (Exception $e) {
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
            $this->error($e->getMessage(), U('Merchant/auth_set'));
        }
    }
    
    
    /**
     +----------------------------------------------------------
     * 添加广告图片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function add_merchant_ad(){
        
        $return_data    = array();
        try {
            $merchant = D('Merchant');
            $merchant->add_merchant_ad();
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
     * 删除广告图片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function del_merchant_ad(){
        $id = I('post.id');
        $return_data    = array();
        try {
            $merchant = D('Merchant');
            $merchant->del_merchant_ad($id);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '删除广告成功',
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
     * 上传商家图片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
    */
    public function upload_img(){
        $type = I('post.type');
        $imagename = I('post.imagename');
        $return_data    = array();
        try {
            $merchant         = D('Merchant');
            $img_info = $merchant->upload_merchant_logo_and_banner($type, $imagename);
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
    public function _empty(){
           $this->display('Empty:index');
    }
}