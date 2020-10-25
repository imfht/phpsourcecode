<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Group\Widget;

use Think\Action;

/**
 * Class DynamicWidget  群组动态
 * @package Group\Widget
 * @author:xjw129xjt xjt@ourstu.com
 */
class DynamicWidget extends Action
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($dynamic='')
    {


        $user= query_user(array('avatar128','avatar64','nickname','uid','space_url','icons_html'),$dynamic['uid']);
        $this->assign('dynamic', $dynamic);
        $this->assign('user', $user);

        $this->display('Widget/dynamic');

    }

}
