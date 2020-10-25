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
namespace home\Controller;
use Think\Controller;
use Think\Log;
use Think\Exception;
class MerchantController extends StationBaseController {
	public function index(){
        //获取商家幻灯片
        $merchants_micro_station_slide = DD('MerchantsMicroStationSlide');
        $slide_list = $merchants_micro_station_slide->get_station_slide_list();
        $this->assign(array(
            'slide_list'        => $slide_list,
        ));
        $this->display();
	}
    /**
     +----------------------------------------------------------
     * 关于我们
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function about_us(){
        $merchants_micro_station_about = DD('MerchantsMicroStationAbout');
        $about_info = $merchants_micro_station_about->get_station_about_info();
        $this->assign(array(
            'about_info'        => $about_info,
        ));
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 新闻中心
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function new_list(){
        $merchants_micro_station_new = DD('MerchantsMicroStationNew');
        $new_list = $merchants_micro_station_new->get_station_new_list();
        $this->assign(array(
            'new_list'        => $new_list,
        ));
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 新闻详情
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function new_info(){
        $id = I('get.id');
        if (empty($id)){
            redirect(U('Merchant/index'));
        }
        $merchants_micro_station_new = DD('MerchantsMicroStationNew');
        $new_info = $merchants_micro_station_new->get_station_new_info($id);

        $this->assign(array(
            'new_info'        => $new_info,
        ));
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 产品中心
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function product(){
        $merchants_micro_station_product = DD('MerchantsMicroStationProduct');
        $product_list = $merchants_micro_station_product->get_station_product_list();
        $this->assign(array(
            'product_list'        => $product_list,
        ));
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 产品详情
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function product_info(){
        $id = I('get.id');
        if (empty($id)){
            redirect(U('Merchant/index'));
        }
        $merchants_micro_station_product = DD('MerchantsMicroStationProduct');
        $product_info = $merchants_micro_station_product->get_station_product_info($id);

        $this->assign(array(
            'product_info'        => $product_info,
        ));
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 联系我们
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function contact_us(){
        $merchants_micro_station_contact = DD('MerchantsMicroStationContact');
        $contact_info = $merchants_micro_station_contact->get_station_contact_info();
        $this->assign(array(
            'contact_info'        => $contact_info,
        ));
        $this->display();
        
    }
    /**
     +----------------------------------------------------------
     * 活动中心
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function activity(){
        $merchants_micro_station_activity = DD('MerchantsMicroStationActivity');
        $activity_list = $merchants_micro_station_activity->get_station_activity_list();
        $this->assign(array(
            'activity_list'        => $activity_list,
        ));
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 活动详情
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function activity_info(){
        $id = I('get.id');
        if (empty($id)){
            redirect(U('Merchant/index', array('mid' => $this->mid)));
        }
        $merchants_micro_station_activity = DD('MerchantsMicroStationActivity');
        $activity_info = $merchants_micro_station_activity->get_station_activity_info($id);

        $this->assign(array(
            'activity_info'        => $activity_info,
        ));
        $this->display();
    }
    public function _empty(){        
        $this->display('Empty:index');
    }
}