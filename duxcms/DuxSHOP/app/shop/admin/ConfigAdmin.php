<?php

/**
 * 商城设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\shop\admin;


class ConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商城设置',
                'description' => '配置商城基本功能',
            ],
        ];
    }

    /*private function getTabs() {
        return [
            [
                'name' => '购物设置',
                'url' => url('index')
            ],
            [
                'name' => '店铺设置',
                'url' => url('store')
            ]

        ];
    }*/

    /**
     * 商城设置
     */
    public function index() {
        if (!isPost()) {
            $info = target('ShopConfig')->getConfig();
            $this->assign('info', $info);
            //$this->setTabs($this->getTabs(), 0);
            $this->systemDisplay();
        } else {
            if (target('ShopConfig')->saveInfo()) {
                $this->success('功能配置成功！');
            } else {
                $this->error('功能配置失败');
            }
        }
    }

}