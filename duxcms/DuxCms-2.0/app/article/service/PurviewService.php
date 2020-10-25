<?php
namespace app\article\service;
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
                'name' => '文章栏目管理',
                'auth' => array(
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminContent' => array(
                'name' => '文章管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                    'batchAction' => '批量操作',
                )
            ),
            'AdminSetting' => array(
                'name' => '文章模块设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
        );
	}
	


}
