<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Think\Controller;
/**
 * 所有模块公共控制器
 * @author jry <598821125@qq.com>
 */
class CommonController extends Controller{

    public function _empty($name){
        $this->operation($name);
    }
    private function operation($path){
        $this->error("您访问的路径".$path."不存在");
    }
}
