<?php

/**
 * 首页模块
 */

namespace app\index\middle;

class IndexMiddle extends \app\base\middle\BaseMiddle {

    public function meta() {
        $this->setMeta('首页');
        $this->setName('首页');
        $this->setCrumb([
            [
                'name' => '首页',
                'url' => ROOT_URL . '/'
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }



}