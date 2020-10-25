<?php
namespace app\page\service;
/**
 * 权限接口
 */
class PurviewService{
	/**
	 * 获取模块权限
	 */
	public function getAdminPurview(){
		return array(
            'AdminCategory' => array(
                'name' => '单页栏目管理',
                'auth' => array(
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminSetting' => array(
                'name' => '单页模块设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
        );
	}
	


}
