<?php

/**
 * 标签生成
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\tools\admin;

class LabelAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'ToolsLabel';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '标签生成',
                'description' => '生成站点前台调用标签',
            ]
        ];
    }

    public function index() {

        $label = request('get', 'label', 'site');
        $list = target('tools/ToolsLabel')->getTips();
        $info = $list[$label];
        //print_r($info);
        $this->assign('list', $list);
        $this->assign('info', $info);
        $this->assign('label', $label);

        $this->systemDisplay();

    }

}