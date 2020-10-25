<?php
namespace app\order\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return array(
            'Config' => array(
                'name' => '订单设置',
                'auth' => array(
                    'index' => '订单设置',
                )
            ),
            'ConfigExpress' => array(
                'name' => '物流设置',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'ConfigDelivery' => array(
                'name' => '运费模板',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'ConfigWaybill' => array(
                'name' => '物流接口',
                'auth' => array(
                    'index' => '列表',
                    'setting' => '配置',
                )
            ),
            'Parcel' => array(
                'name' => '配货管理',
                'auth' => array(
                    'index' => '列表',
                    'print' => '打印',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Delivery' => array(
                'name' => '发货管理',
                'auth' => array(
                    'index' => '列表',
                    'print' => '打印',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Receipt' => array(
                'name' => '收款管理',
                'auth' => array(
                    'index' => '列表',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Refund' => array(
                'name' => '退款管理',
                'auth' => array(
                    'index' => '列表',
                    'info' => '详情',
                )
            ),
            'Coupon' => array(
                'name' => '优惠券管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'CouponClass' => array(
                'name' => '优惠券分类',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'CouponLog' => array(
                'name' => '优惠券记录',
                'auth' => array(
                    'index' => '列表',
                    'del' => '删除',
                )
            ),
            'Take' => array(
                'name' => '自提点管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Invoice' =>  array(
                'name' => '发票管理',
                'auth' => array(
                    'index' => '列表',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'InvoiceClass' =>  array(
                'name' => '发票分类',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
        );
    }


}
