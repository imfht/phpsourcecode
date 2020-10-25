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
class StationController extends BaseController {
    public function nav(){
        try {
            $merchants_micro_station_nav    = D('MerchantsMicroStationNav');
            $nav_list = $merchants_micro_station_nav->get_station_nav_list_by_userid();

            
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-导航菜单管理',
                'rows'          => $nav_list,
                'breadcrumb'        => '&gt;微站管理&gt;导航菜单管理'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Index/index'));
        }



    }
    /**
     +----------------------------------------------------------
     * 添加导航页面
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function add_nav(){
        try {
            $id = I('get.id');
            $nav_id = I('get.nav_id');
            //获取导航详情
            $merchants_micro_station_nav    = D('MerchantsMicroStationNav');
            $nav_info = $merchants_micro_station_nav->get_station_nav_info_by_id($id, $nav_id);

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-导航信息',
                'nav_info'          => $nav_info,
                'breadcrumb'        => '&gt;微站管理&gt;<a href="'.U('Station/nav').'">导航管理</a>&gt;'.($id == '' ? '添加' : '编辑').'导航'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Station/nav'));
        }
    }
    /**
     +----------------------------------------------------------
     * 添加导航
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function save_nav(){

        $param = array(
            'id'        => I('post.id'),
            'nav_id'    => I('post.nav_id'),
            'nav_name'  => I('post.nav_name'),
            'nav_image' => I('post.nav_image'),
            'nav_href'  => I('post.nav_href'),
            'sort'      => I('post.sort'),
            'status'    => I('post.status'),
        );
        $merchants_micro_station_nav    = D('MerchantsMicroStationNav');
        $return_data                    = array();
        try{
            if (intval($param['id']) == 0 && intval($param['nav_id']) == 0){
                $nav_list = $merchants_micro_station_nav->add_nav($param);
                $this->success('添加导航成功', U('Station/nav'));
            }else{
                $nav_list = $merchants_micro_station_nav->edit_nav($param);
                $this->success('编辑导航成功', U('Station/nav'));
            }
            
            
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Station/add_nav', array('id'=>$param['id'])));
        }
    }
    
    /**
     +----------------------------------------------------------
     * 删除导航
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function del_nav(){
        $merchants_micro_station_nav    = D('MerchantsMicroStationNav');
        $return_data                    = array();
        try{
            $nav_list = $merchants_micro_station_nav->del_nav(I('post.id'));
            $return_data = array(
                'ret'           => 1,
                'msg'           => '删除导航成功',
            );
        }catch(Exception $e){
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
     * 获取幻灯片列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function slide(){
        $merchants_micro_station_slide    = D('MerchantsMicroStationSlide');
        $return_data                    = array();
        try{
            $slide_list = $merchants_micro_station_slide->get_station_slide_list_by_userid();
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-幻灯片管理',
                'slide_list'        => $slide_list,
                'breadcrumb'        => '&gt;微站管理&gt;幻灯片管理'
            ));
            $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
        exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 添加幻灯片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function add_slide(){
        $param = array(
            'title'  => I('post.title'),
            'image' => I('post.image'),
            'url'  => I('post.url'),
        );
        $merchants_micro_station_slide    = D('MerchantsMicroStationSlide');
        $return_data                    = array();
        try{
            $nav_list = $merchants_micro_station_slide->add_slide($param);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '上传成功',
                'data'          => $img_info,
            );
        }catch(Exception $e){
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
     * 编辑幻灯片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function edit_slide(){
        $param = array(
            'id'    => I('post.id'),
            'sort' => I('post.sort'),
            'url'   => I('post.url'),
        );
        $merchants_micro_station_slide    = D('MerchantsMicroStationSlide');
        $return_data                    = array();
        try{
            $nav_list = $merchants_micro_station_slide->edit_slide($param);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '编辑成功',
            );
        }catch(Exception $e){
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
     * 删除幻灯片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function del_slide(){
        $merchants_micro_station_slide    = D('MerchantsMicroStationSlide');
        $return_data                    = array();
        try{
            $nav_list = $merchants_micro_station_slide->del_slide(I('post.id'));
            $return_data = array(
                'ret'           => 1,
                'msg'           => '删除成功',
            );
        }catch(Exception $e){
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
     * 获取关于我们信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function about_us(){
        $merchants_micro_station_about    = D('MerchantsMicroStationAbout');
        try{
            $about_info = $merchants_micro_station_about->get_station_about_info();
            $about_info['content'] = str_replace(array("\r\n", "\r", "\n"), "", $about_info['content']); 
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-关于我们',
                'about_info'          => $about_info,
                'breadcrumb'        => '&gt;微站管理&gt;关于我们'
            ));
            $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 编辑关于我们
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function edit_about_info(){
        $merchants_micro_station_about    = D('MerchantsMicroStationAbout');
        $return_data                    = array();
        try{
            $merchants_micro_station_about->edit_about_info($_POST['content']);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '编辑成功',
            );
        }catch(Exception $e){
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
     * 获取新闻列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function new_list(){
        $merchants_micro_station_new    = D('MerchantsMicroStationNew');
        try{
            $list = $merchants_micro_station_new->get_station_new_list_by_userid();
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-新闻管理',
                'list'          => $list,
                'breadcrumb'        => '&gt;微站管理&gt;新闻管理'
            ));
            $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
        
        
    }
    /**
     +----------------------------------------------------------
     * 添加新闻
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function add_new(){
        try {
            $id = I('get.id');
            //获取导航详情
            $merchants_micro_station_new    = D('MerchantsMicroStationNew');
            $new_info = $merchants_micro_station_new->get_station_new_info_by_id($id);

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-新闻信息',
                'new_info'          => $new_info,
                'breadcrumb'        => '&gt;微站管理&gt;<a href="'.U('Station/new_list').'">新闻管理</a>&gt;'.($id == '' ? '添加' : '编辑').'新闻'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Station/new_list'));
        }
        
    }
    /**
     +----------------------------------------------------------
     * 保存新闻
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function save_new(){
        $param = array(
            'id'        => I('post.id'),
            'title'     => I('post.title'),
            'content'   => $_POST['content'],
        );

        $merchants_micro_station_new    = D('MerchantsMicroStationNew');
        $return_data                    = array();
        try{
           

            if (intval($param['id']) == 0 && intval($param['nav_id']) == 0){
                $merchants_micro_station_new->add_new($param);
                $this->success('添加新闻成功', U('Station/new_list'));
            }else{
                $merchants_micro_station_new->edit_new($param);
                $this->success('编辑新闻成功', U('Station/new_list'));
            }
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Station/add_new', array('id'=>$param['id'])));
        }
    }
    /**
     +----------------------------------------------------------
     * 删除新闻
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function del_new(){
        $merchants_micro_station_new    = D('MerchantsMicroStationNew');
        $return_data                    = array();
        try{
            $nav_list = $merchants_micro_station_new->del_new(I('post.id'));
            $return_data = array(
                'ret'           => 1,
                'msg'           => '删除成功',
            );
        }catch(Exception $e){
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
     * 获取产品列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function product(){
        $merchants_micro_station_product    = D('MerchantsMicroStationProduct');
        try{
            $list = $merchants_micro_station_product->get_station_product_list_by_userid();
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-产品管理',
                'list'          => $list,
                'breadcrumb'        => '&gt;微站管理&gt;产品管理'
            ));
            $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 添加产品
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function add_product(){
        try {
            $id = I('get.id');
            //获取导航详情
            $merchants_micro_station_product    = D('MerchantsMicroStationProduct');
            $product_info = $merchants_micro_station_product->get_station_product_info_by_id($id);

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-产品信息',
                'info'          => $product_info,
                'breadcrumb'        => '&gt;微站管理&gt;<a href="'.U('Station/product').'">产品管理</a>&gt;'.($id == '' ? '添加' : '编辑').'产品'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Station/product'));
        }
    }
    /**
     +----------------------------------------------------------
     * 保存产品
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function save_product(){
        $param = array(
            'id'        => I('post.id'),
            'title'  => I('post.title'),
            'thumb' => I('post.thumb'),
            'content' => $_POST['content'],
        );
        $merchants_micro_station_product    = D('MerchantsMicroStationProduct');
        
        try{
           

            if (intval($param['id']) == 0){
                $merchants_micro_station_product->add_product($param);
                $this->success('添加产品成功', U('Station/product'));
            }else{
                $merchants_micro_station_product->edit_product($param);
                $this->success('编辑产品成功', U('Station/product'));
            }
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Station/add_product', array('id'=>$param['id'])));
        }
    }
    /**
     +----------------------------------------------------------
     * 删除产品
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function del_product(){
        $merchants_micro_station_product    = D('MerchantsMicroStationProduct');
        $return_data                    = array();
        try{
            $nav_list = $merchants_micro_station_product->del_product(I('post.id'));
            $return_data = array(
                'ret'           => 1,
                'msg'           => '删除成功',
            );
        }catch(Exception $e){
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
     * 获取联系我们信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function contact_us(){
        $merchants_micro_station_contact    = D('MerchantsMicroStationContact');
        try{
            $contact_info = $merchants_micro_station_contact->get_station_contact_info();
            $cop = C('COPYRIGHT');
            
            $contact_info['content'] = str_replace(array("\r\n", "\r", "\n"), "", $contact_info['content']); 

            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-联系我们',
                'contact_info'          => $contact_info,
                'breadcrumb'        => '&gt;微站管理&gt;联系我们'
            ));
            $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
        
        
    }
    /**
     +----------------------------------------------------------
     * 编辑联系我们
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function edit_contact_info(){
        $merchants_micro_station_contact    = D('MerchantsMicroStationContact');
        $return_data                    = array();
        try{
            $merchants_micro_station_contact->edit_contact_info($_POST['content']);

            $return_data = array(
                'ret'           => 1,
                'msg'           => '编辑成功',
            );
        }catch(Exception $e){
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
     * 获取活动列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function activity(){
        $merchants_micro_station_activity    = D('MerchantsMicroStationActivity');
        try{
            $list = $merchants_micro_station_activity->get_station_activity_list_by_userid();
            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-活动管理',
                'list'          => $list,
                'breadcrumb'        => '&gt;微站管理&gt;活动管理'
            ));
            $this->display();
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Index/index'));
        }
    }
    /**
     +----------------------------------------------------------
     * 添加活动页面
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function add_activity(){
        try {
            $id = I('get.id');
            //获取导航详情
            $merchants_micro_station_activity    = D('MerchantsMicroStationActivity');
            $activity_info = $merchants_micro_station_activity->get_station_activity_info_by_id($id);

            $cop = C('COPYRIGHT');
            $this->assign(array(
                'title'             => $cop['pname_cn'].$cop['version_major'].'-活动信息',
                'info'          => $activity_info,
                'breadcrumb'        => '&gt;微站管理&gt;<a href="'.U('Station/activity').'">活动管理</a>&gt;'.($id == '' ? '添加' : '编辑').'活动'
            ));
            $this->display();
        } catch (Exception $e) {
            $this->error($e->getMessage(), U('Station/activity'));
        }
    }
    /**
     +----------------------------------------------------------
     * 添加活动
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function save_activity(){
        
        $param = array(
            'id'        => I('post.id'),
            'title'  => I('post.title'),
            'thumb' => I('post.thumb'),
            'content' => $_POST['content'],
            'start_datetime'    => I('post.start_datetime'),
            'end_datetime'      => I('post.end_datetime')
        );
        $merchants_micro_station_activity    = D('MerchantsMicroStationActivity');
        
        try{
           

            if (intval($param['id']) == 0){
                $merchants_micro_station_activity->add_activity($param);
                $this->success('添加活动成功', U('Station/activity'));
            }else{
                $merchants_micro_station_activity->edit_activity($param);
                $this->success('编辑活动成功', U('Station/activity'));
            }
        }catch(Exception $e){
            $this->error($e->getMessage(), U('Station/add_activity', array('id'=>$param['id'])));
        }
    }
    /**
     +----------------------------------------------------------
     * 删除活动
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function del_activity(){
        $merchants_micro_station_activity    = D('MerchantsMicroStationActivity');
        $return_data                    = array();
        try{
            $nav_list = $merchants_micro_station_activity->del_activity(I('post.id'));
            $return_data = array(
                'ret'           => 1,
                'msg'           => '删除成功',
            );
        }catch(Exception $e){
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
}