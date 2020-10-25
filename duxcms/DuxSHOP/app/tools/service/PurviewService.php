<?php
namespace app\tools\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return array(
            'Send' => array(
                'name' => '发送管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'info' => '详情',
                )
            ),
            'SendConf' => array(
                'name' => '发送设置',
                'auth' => array(
                    'index' => '列表',
                    'setting' => '配置',
                )
            ),
            'SendTpl' => array(
                'name' => '发送模板',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                )
            ),
            'SendDefault' => array(
                'name' => '默认设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
            'Label' => array(
                'name' => '标签生成器',
                'auth' => array(
                    'index' => '生成工具',
                )
            ),
            'Queue' => array(
                'name' => '队列管理',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'QueueConf' => array(
                'name' => '队列设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
        );
    }


}
