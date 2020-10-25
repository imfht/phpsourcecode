<?php

/**
 * 注册协议
 */

namespace app\member\middle;


class AgreementMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];

    protected function meta() {
        $this->setMeta('用户协议');
        $this->setName('用户协议');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '用户协议',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();

        return $this->config;
    }


    protected function data() {
        $this->config = $this->getConfig();
        return $this->run([
            'content' => html_out($this->config['reg_agreement'])
        ]);
    }

}