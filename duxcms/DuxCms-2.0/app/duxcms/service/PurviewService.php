<?php
namespace app\duxcms\service;
/**
 * 权限接口
 */
class PurviewService{
	/**
	 * 获取模块权限
	 */
	public function getAdminPurview(){
		return array(
            'AdminExpand' => array(
                'name' => '扩展模型',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminExpandField' => array(
                'name' => '扩展字段',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminForm' => array(
                'name' => '表单管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminFormField' => array(
                'name' => '表单字段',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminFormData' => array(
                'name' => '表单数据',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminFragment' => array(
                'name' => '网站碎片',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminPosition' => array(
                'name' => '推荐位管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'AdminStatistics' => array(
                'name' => '站点统计',
                'auth' => array(
                    'index' => '访客统计',
                    'spider' => '蜘蛛统计',
                )
            ),
            'AdminSafe' => array(
                'name' => '安全检测',
                'auth' => array(
                    'index' => '访客统计',
                )
            ),
            'AdminTags' => array(
                'name' => 'TAG管理',
                'auth' => array(
                    'index' => '列表',
                    'batchAction' => '删除',
                )
            ),
            'AdminTags' => array(
                'name' => '系统更新',
                'auth' => array(
                    'index' => '更新管理',
                )
            ),
        );
	}
	


}
