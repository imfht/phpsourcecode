<?php
namespace app\mall\service;
/**
 * 权限接口
 */
class PurviewService{
	/**
	 * 获取模块权限
	 */
	public function getSystemPurview(){
		return array(
            'Content' => array(
                'name' => '商品管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Class' => array(
                'name' => '商品分类',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Order' => array(
                'name' => '订单管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'OrderStatis' => array(
                'name' => '订单统计',
                'auth' => array(
                    'index' => '统计信息',
                )
            ),
        );
	}
	


}
