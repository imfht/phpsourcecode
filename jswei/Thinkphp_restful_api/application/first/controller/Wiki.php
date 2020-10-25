<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 14:51
// +----------------------------------------------------------------------
// | TITLE: 文档显示
// +----------------------------------------------------------------------

namespace app\first\controller;

use DawnApi\facade\Doc;

class Wiki extends Doc{
    public $titleDoc = '去剪头WIKI';

    public function main(){
        return $this->index();
    }
}