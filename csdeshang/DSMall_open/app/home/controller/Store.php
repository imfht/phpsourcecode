<?php

namespace app\home\controller;

use think\facade\View;
use think\facade\Lang;
use think\facade\Db;

/**
 * ============================================================================
 * DSMall多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 控制器
 */
class Store extends BaseStore {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/store.lang.php');
    }

    public function index() {
        $editable_page_model = model('editable_page');
        $editable_page = $editable_page_model->getOneEditablePage(array('store_id' => $this->store_info['store_id'], 'editable_page_path' => 'store/index', 'editable_page_client' => 'pc'));
        if ($editable_page) {
            Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/seller_editable_page.lang.php');
            $editable_page['if_edit'] = 0;
            $editable_page['editable_page_theme_config'] = json_decode($editable_page['editable_page_theme_config'], true);
            //获取可编辑模块
            $data = $editable_page_model->getEditablePageConfigByPageId($editable_page['editable_page_id'],$this->store_info['store_id']);
            View::assign('editable_page_config_list', $data['editable_page_config_list']);
            View::assign('editable_page', $editable_page);
        } else {
            $condition = array();
            $condition[]=array('store_id','=',$this->store_info['store_id']);

            $goods_model = model('goods'); // 字段
            $fieldstr = "goods_id,goods_commonid,goods_name,goods_advword,store_id,store_name,goods_price,goods_promotion_price,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count,goods_promotion_type";
            //得到最新12个商品列表
            $new_goods_list = $goods_model->getGoodsListByColorDistinct($condition, $fieldstr, 'goods_id desc', 12);

            $condition[]=array('goods_commend','=',1);
            //得到12个推荐商品列表
            $recommended_goods_list = $goods_model->getGoodsListByColorDistinct($condition, $fieldstr, 'goods_sort desc,goods_id desc', 12);
            $goods_list = $this->getGoodsMore($new_goods_list, $recommended_goods_list);
            View::assign('new_goods_list', $goods_list[1]);
            View::assign('recommended_goods_list', $goods_list[2]);

            //幻灯片图片
            if ($this->store_info['store_slide'] != '' && $this->store_info['store_slide'] != ',,,,') {
                View::assign('store_slide', explode(',', $this->store_info['store_slide']));
                View::assign('store_slide_url', explode(',', $this->store_info['store_slide_url']));
            }
        }
        View::assign('page', 'index');
        return View::fetch($this->template_dir . 'index');
    }

    private function getGoodsMore($goods_list1, $goods_list2 = array()) {
        if (!empty($goods_list2)) {
            $goods_list = array_merge($goods_list1, $goods_list2);
        } else {
            $goods_list = $goods_list1;
        }
        // 商品多图
        if (!empty($goods_list)) {
            $goodsid_array = array();       // 商品id数组
            $commonid_array = array(); // 商品公共id数组
            $storeid_array = array();       // 店铺id数组
            foreach ($goods_list as $value) {
                $goodsid_array[] = $value['goods_id'];
                $commonid_array[] = $value['goods_commonid'];
                $storeid_array[] = $value['store_id'];
            }
            $goodsid_array = array_unique($goodsid_array);
            $commonid_array = array_unique($commonid_array);

            // 商品多图
            $goodsimage_more = model('goods')->getGoodsImageList(array(array('goods_commonid', 'in', $commonid_array)));

            foreach ($goods_list1 as $key => $value) {
                // 商品多图
                foreach ($goodsimage_more as $v) {
                    if ($value['goods_commonid'] == $v['goods_commonid'] && $value['store_id'] == $v['store_id'] && $value['color_id'] == $v['color_id']) {
                        $goods_list1[$key]['image'][] = $v;
                    }
                }
            }

            if (!empty($goods_list2)) {
                foreach ($goods_list2 as $key => $value) {
                    // 商品多图
                    foreach ($goodsimage_more as $v) {
                        if ($value['goods_commonid'] == $v['goods_commonid'] && $value['store_id'] == $v['store_id'] && $value['color_id'] == $v['color_id']) {
                            $goods_list2[$key]['image'][] = $v;
                        }
                    }
                }
            }
        }
        return array(1 => $goods_list1, 2 => $goods_list2);
    }

    public function article() {
        //判断是否为导航页面
        $storenavigation_model = model('storenavigation');
        $store_navigation_info = $storenavigation_model->getStorenavigationInfo(array('storenav_id' => intval(input('param.storenav_id'))));
        if (!empty($store_navigation_info) && is_array($store_navigation_info)) {
            View::assign('store_navigation_info', $store_navigation_info);
            return View::fetch($this->template_dir . 'article');
        }
    }

    /**
     * 全部商品
     */
    public function goods_all() {

        $condition = array();
        $condition[] = array('store_id', '=', $this->store_info['store_id']);
        $inkeyword = trim(input('inkeyword'));
        if ($inkeyword != '') {
            $condition[] = array('goods_name', 'like', '%' . $inkeyword . '%');
        }

        // 排序
        $order = input('order');
        $order = $order == 1 ? 'asc' : 'desc';
        $key = trim(input('key'));
        switch ($key) {
            case '1':
                $order = 'goods_id ' . $order;
                break;
            case '2':
                $order = 'goods_promotion_price ' . $order;
                break;
            case '3':
                $order = 'goods_salenum ' . $order;
                break;
            case '4':
                $order = 'goods_collect ' . $order;
                break;
            case '5':
                $order = 'goods_click ' . $order;
                break;
            default:
                $order = 'goods_id desc';
                break;
        }

        //查询分类下的子分类
        $storegc_id = intval(input('storegc_id'));
        if ($storegc_id > 0) {
            $condition[] = array('goods_stcids', 'like', '%,' . $storegc_id . ',%');
        }

        $goods_model = model('goods');
        $fieldstr = "goods_id,goods_commonid,goods_name,goods_advword,store_id,store_name,goods_price,goods_promotion_price,goods_marketprice,goods_storage,goods_image,goods_freight,goods_salenum,color_id,evaluation_good_star,evaluation_count,goods_promotion_type";

        $recommended_goods_list = $goods_model->getGoodsListByColorDistinct($condition, $fieldstr, $order, 24);
        $recommended_goods_list = $this->getGoodsMore($recommended_goods_list);
        View::assign('recommended_goods_list', $recommended_goods_list[1]);

        /* 引用搜索相关函数 */
        require_once(base_path() . '/home/common_search.php');

        //输出分页
        View::assign('show_page', empty($recommended_goods_list[1]) ? '' : $goods_model->page_info->render());
        $stc_class = model('storegoodsclass');
        $stc_info = $stc_class->getStoregoodsclassInfo(array('storegc_id' => $storegc_id));
        View::assign('storegc_name', $stc_info['storegc_name']);
        View::assign('page', 'index');

        return View::fetch($this->template_dir . 'goods_list');
    }

    /**
     * ajax获取动态数量
     */
    function ajax_store_trend_count() {
        $count = model('storesnstracelog')->getStoresnstracelogCount(array('stracelog_storeid' => $this->store_info['store_id']));
        echo json_encode(array('count' => $count));
        exit;
    }

    /**
     * ajax 店铺流量统计入库
     */
    public function ajax_flowstat_record() {
        $store_id = intval(input('param.store_id'));
        if ($store_id <= 0 || session('store_id') == $store_id) {
            echo json_encode(array('done' => true, 'msg' => 'done'));
            die;
        }
        //确定统计分表名称
        $last_num = $store_id % 10; //获取店铺ID的末位数字
        $tablenum = ($t = intval(config('ds_config.flowstat_tablenum'))) > 1 ? $t : 1; //处理流量统计记录表数量
        $flow_tablename = ($t = ($last_num % $tablenum)) > 0 ? "flowstat_$t" : 'flowstat';
        //判断是否存在当日数据信息
        $stattime = strtotime(date('Y-m-d', TIMESTAMP));
        $stat_model = model('stat');
        //查询店铺流量统计数据是否存在
        // halt($flow_tablename);
        if ($flow_tablename == 'flowstat') {
            $flow_tablename_condition = array('flowstat_stattime' => $stattime, 'store_id' => $store_id, 'flowstat_type' => 'sum');
        } else {
            $flow_tablename_condition = array('flowstat_stattime' => $stattime, 'store_id' => $store_id, 'flowstat_type' => 'sum');
        }
        $store_exist = $stat_model->getoneByFlowstat($flow_tablename, $flow_tablename_condition);
        if (input('param.controller_param') == 'Goods' && input('param.action_param') == 'index') {//统计商品页面流量
            $goods_id = intval(input('param.goods_id'));
            if ($goods_id <= 0) {
                echo json_encode(array('done' => false, 'msg' => 'done'));
                die;
            }
            if ($flow_tablename == 'flowstat') {
                $flow_tablename_condition = array('flowstat_stattime' => $stattime, 'goods_id' => $goods_id, 'flowstat_type' => 'goods');
            } else {
                $flow_tablename_condition = array('flowstat_stattime' => $stattime, 'goods_id' => $goods_id, 'flowstat_type' => 'goods');
            }
            $goods_exist = $stat_model->getoneByFlowstat($flow_tablename, $flow_tablename_condition);
        }
        //向数据库写入访问量数据
        $insert_arr = array();
        if ($store_exist) {
            Db::name($flow_tablename)->where(array('flowstat_stattime' => $stattime, 'store_id' => $store_id, 'flowstat_type' => 'sum'))->inc('flowstat_clicknum')->update();
        } else {
            $insert_arr[] = array('flowstat_stattime' => $stattime, 'flowstat_clicknum' => 1, 'store_id' => $store_id, 'flowstat_type' => 'sum', 'goods_id' => 0);
        }
        if (input('param.controller_param') == 'Goods' && input('param.action_param') == 'index') {//已经存在数据则更新
            if ($goods_exist) {
                Db::name($flow_tablename)->where(array('flowstat_stattime' => $stattime, 'goods_id' => $goods_id, 'flowstat_type' => 'goods'))->inc('flowstat_clicknum')->update();
            } else {
                $insert_arr[] = array('flowstat_stattime' => $stattime, 'flowstat_clicknum' => 1, 'store_id' => $store_id, 'flowstat_type' => 'goods', 'goods_id' => $goods_id);
            }
        }
        if ($insert_arr) {
            Db::name($flow_tablename)->insertAll($insert_arr);
        }
        echo json_encode(array('done' => true, 'msg' => 'done'));
    }

}
