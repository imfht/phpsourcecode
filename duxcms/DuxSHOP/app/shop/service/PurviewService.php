<?php
namespace app\shop\service;
/**
 * 权限接口
 */
class PurviewService{
	/**
	 * 获取模块权限
	 */
	public function getSystemPurview(){
		return array(
            'Brand' => array(
                'name' => '品牌管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'BrandApply' => array(
                'name' => '品牌申请',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'BrandContract' => array(
                'name' => '品牌续约',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Spec' => array(
                'name' => '规格管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'SpecGroup' => array(
                'name' => '规格分组',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'Config' => array(
                'name' => '商城设置',
                'auth' => array(
                    'index' => '基本设置',
                )
            ),
        );
	}
	


}
