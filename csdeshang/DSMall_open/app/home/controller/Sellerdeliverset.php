<?php

/*
 * 发货设置
 */

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
class Sellerdeliverset extends BaseSeller {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/sellerdeliver.lang.php');
    }

    /**
     * 发货地址列表
     */
    public function index() {
        $daddress_model = model('daddress');
        $condition = array();
        $condition[] = array('store_id', '=', session('store_id'));
        $address_list = $daddress_model->getAddressList($condition, '*', '', 20);
        View::assign('address_list', $address_list);
        /* 设置卖家当前菜单 */
        $this->setSellerCurMenu('sellerdeliverset');
        /* 设置卖家当前栏目 */
        $this->setSellerCurItem('daddress');
        return View::fetch($this->template_dir . 'index');
    }

    /**
     * 新增/编辑发货地址
     */
    public function daddress_add() {
        $address_id = intval(input('param.address_id'));
        if ($address_id > 0) {
            $daddress_mod = model('daddress');
            //编辑
            if (!request()->isPost()) {
                $address_info = $daddress_mod->getAddressInfo(array('daddress_id' => $address_id, 'store_id' => session('store_id')));
                View::assign('address_info', $address_info);
                return View::fetch($this->template_dir . 'daddress_add');
            } else {
                $data = array(
                    'seller_name' => input('post.seller_name'),
                    'area_id' => input('post.area_id'),
                    'city_id' => input('post.city_id'),
                    'area_info' => input('post.region'),
                    'daddress_detail' => input('post.address'),
                    'daddress_telphone' => input('post.telphone'),
                    'daddress_company' => input('post.company'),
                );
                //验证数据  BEGIN
                $sellerdeliverset_validate = ds_validate('sellerdeliverset');
                if (!$sellerdeliverset_validate->scene('daddress_add')->check($data)) {
                    ds_json_encode(10001, $sellerdeliverset_validate->getError());
                }
                //验证数据  END
                $result = $daddress_mod->editDaddress($data, array('daddress_id' => $address_id, 'store_id' => session('store_id')));
                if ($result) {
                    ds_json_encode(10000, lang('ds_common_op_succ'));
                } else {
                    ds_json_encode(10001, lang('store_daddress_modify_fail'));
                }
            }
        } else {
            //新增
            if (!request()->isPost()) {
                $address_info = array(
                    'daddress_id' => '', 'city_id' => '1', 'area_id' => '1', 'seller_name' => '',
                    'area_info' => '', 'daddress_detail' => '', 'daddress_telphone' => '', 'daddress_company' => '',
                );
                View::assign('address_info', $address_info);
                return View::fetch($this->template_dir . 'daddress_add');
            } else {
                $data = array(
                    'store_id' => session('store_id'),
                    'seller_name' => input('post.seller_name'),
                    'area_id' => input('post.area_id'),
                    'city_id' => input('post.city_id'),
                    'area_info' => input('post.region'),
                    'daddress_detail' => input('post.address'),
                    'daddress_telphone' => input('post.telphone'),
                    'daddress_company' => input('post.company'),
                );
                //验证数据  BEGIN
                $sellerdeliverset_validate = ds_validate('sellerdeliverset');
                if (!$sellerdeliverset_validate->scene('daddress_add')->check($data)) {
                    ds_json_encode(10001, $sellerdeliverset_validate->getError());
                }
                //验证数据  END
                $result = Db::name('daddress')->insertGetId($data);
                if ($result) {
                    ds_json_encode(10000, lang('ds_common_op_succ'));
                } else {
                    ds_json_encode(10001, lang('store_daddress_add_fail'));
                }
            }
        }
    }

    /**
     * 删除发货地址
     */
    public function daddress_del() {
        $address_id = intval(input('param.address_id'));
        if ($address_id <= 0) {
            ds_json_encode(10001, lang('store_daddress_del_fail'));
        }
        $condition = array();
        $condition[] = array('daddress_id', '=', $address_id);
        $condition[] = array('store_id', '=', session('store_id'));
        $delete = model('daddress')->delDaddress($condition);
        if ($delete) {
            ds_json_encode(10000, lang('store_daddress_del_succ'));
        } else {
            ds_json_encode(10001, lang('store_daddress_del_fail'));
        }
    }

    /**
     * 设置默认发货地址
     */
    public function daddress_default_set() {
        $address_id = intval(input('get.address_id'));
        if ($address_id <= 0)
            return false;
        $condition = array();
        $condition[] = array('store_id', '=', session('store_id'));
        $update = model('daddress')->editDaddress(array('daddress_isdefault' => 0), $condition);
        $condition[] = array('daddress_id', '=', $address_id);
        $update = model('daddress')->editDaddress(array('daddress_isdefault' => 1), $condition);
    }

    public function express() {
        $storeextend_model = model('storeextend');
        if (!request()->isPost()) {
            $express_list = rkcache('express', true);

            //取得店铺启用的快递公司ID
            $express_select = ds_getvalue_byname('storeextend', 'store_id', session('store_id'), 'express');

            if (!is_null($express_select)) {
                $express_select = explode(',', $express_select);
            } else {
                $express_select = array();
            }
            View::assign('express_select', $express_select);
            //页面输出
            View::assign('express_list', $express_list);

            /* 设置卖家当前菜单 */
            $this->setSellerCurMenu('sellerdeliverset');
            /* 设置卖家当前栏目 */
            $this->setSellerCurItem('express');
            return View::fetch($this->template_dir . 'express');
        } else {
            $data['store_id'] = session('store_id');
            $cexpress_array = input('post.cexpress/a'); #获取数组
            if (!empty($cexpress_array)) {
                $data['express'] = implode(',', $cexpress_array);
            } else {
                $data['express'] = '';
            }
            $condition=array();
            $condition[]=array('store_id','=',session('store_id'));
            if (!$storeextend_model->getStoreextendInfo($condition)) {
                $result = $storeextend_model->addStoreextend($data);
            } else {
                $result = $storeextend_model->editStoreextend($data,$condition);
            }
            if ($result) {
                ds_json_encode('10000', lang('ds_common_save_succ'));
            } else {
                ds_json_encode('10001', lang('ds_common_save_fail'));
            }
        }
    }

    /**
     * 免运费额度设置
     */
    public function free_freight() {
        if (!request()->isPost()) {
            View::assign('store_free_price', $this->store_info['store_free_price']);
            View::assign('store_free_time', $this->store_info['store_free_time']);

            /* 设置卖家当前菜单 */
            $this->setSellerCurMenu('sellerdeliverset');
            /* 设置卖家当前栏目 */
            $this->setSellerCurItem('free_freight');
            return View::fetch($this->template_dir . 'free_freight');
        } else {
            $store_model = model('store');
            $store_free_price = floatval(abs(input('post.store_free_price')));
            $store_free_time = input('post.store_free_time');
            $store_model->editStore(array(
                'store_free_price' => $store_free_price,
                'store_free_time' => $store_free_time
                    ), array('store_id' => session('store_id')));
            ds_json_encode(10000, lang('ds_common_save_succ'));
        }
    }

    /**
     * 默认配送区域设置
     */
    public function deliver_region() {
        if (!request()->isPost()) {
            $deliver_region = array(
                '', ''
            );
            if (strpos($this->store_info['deliver_region'], '|')) {
                $deliver_region = explode('|', $this->store_info['deliver_region']);
            }
            View::assign('deliver_region', $deliver_region);
            /* 设置卖家当前菜单 */
            $this->setSellerCurMenu('sellerdeliverset');
            /* 设置卖家当前栏目 */
            $this->setSellerCurItem('deliver_region');
            return View::fetch($this->template_dir . 'deliver_region');
        } else {
            model('store')->editStore(array('deliver_region' => input('post.area_ids') . '|' . input('post.region')), array('store_id' => session('store_id')));
            ds_json_encode(10000, lang('ds_common_save_succ'));
        }
    }

    /**
     * 发货单打印设置
     */
    public function print_set() {
        $store_info = $this->store_info;

        if (!request()->isPost()) {
            View::assign('store_info', $store_info);
            /* 设置卖家当前菜单 */
            $this->setSellerCurMenu('sellerdeliverset');
            /* 设置卖家当前栏目 */
            $this->setSellerCurItem('print_set');
            return View::fetch($this->template_dir . 'print_set');
        } else {
            $data = array(
                'store_printexplain' => input('store_printexplain')
            );

            $sellerdeliverset_validate = ds_validate('sellerdeliverset');
            if (!$sellerdeliverset_validate->scene('print_set')->check($data)) {
                $this->error($sellerdeliverset_validate->getError());
            }
            $update_arr = array();
            //上传认证文件
            if ($_FILES['store_seal']['name'] != '') {
                $default_dir = BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_STORE;
                $file_name = session('store_id') . '_' . date('YmdHis') . rand(10000, 99999).'.png';
                $upload = request()->file('store_seal');


                $file_config = array(
                    'disks' => array(
                        'local' => array(
                            'root' => $default_dir
                        )
                    )
                );
                config($file_config, 'filesystem');
                try {
                    validate(['image' => 'fileSize:' . ALLOW_IMG_SIZE . '|fileExt:' . ALLOW_IMG_EXT])
                            ->check(['image' => $upload]);
                    $file_name = \think\facade\Filesystem::putFileAs('', $upload, $file_name);
                    $update_arr['store_seal'] = $file_name;
                    //删除旧认证图片
                    if (!empty($store_info['store_seal'])) {
                        @unlink(BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . ATTACH_STORE . DIRECTORY_SEPARATOR . $store_info['store_seal']);
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $update_arr['store_printexplain'] = input('post.store_printexplain');

            $rs = model('store')->editStore($update_arr, array('store_id' => session('store_id')));
            if ($rs) {
                $this->success(lang('ds_common_save_succ'));
            } else {
                $this->error(lang('ds_common_save_fail'));
            }
        }
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    function getSellerItemList() {
        $menu_array = array(
            array(
                'name' => 'daddress',
                'text' => lang('store_deliver_daddress_list'),
                'url' => (string) url('Sellerdeliverset/index')
            ),
            array(
                'name' => 'express',
                'text' => lang('store_deliver_default_express'),
                'url' => (string) url('Sellerdeliverset/express')
            ),
            array(
                'name' => 'free_freight',
                'text' => lang('free_freight'),
                'url' => (string) url('Sellerdeliverset/free_freight')
            ),
            array(
                'name' => 'deliver_region',
                'text' => lang('default_delivery_area'),
                'url' => (string) url('Sellerdeliverset/deliver_region')
            ),
            array(
                'name' => 'print_set',
                'text' => lang('print_set'),
                'url' => (string) url('Sellerdeliverset/print_set')
            )
        );
        return $menu_array;
    }

}

?>
