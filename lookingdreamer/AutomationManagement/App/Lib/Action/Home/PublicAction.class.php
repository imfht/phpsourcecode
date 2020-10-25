<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class PublicAction extends BaseAction{

    // 单页面统一处理
    public function _empty($method)
    {
        $pageName = array(
            'map'       => '站点地图',
            'aboutus'   => '关于我们',
            'contact'   => '联系我们',
            'joinus'    => '加入我们',
            'team'    => '捐赠',
            'logo'   => '支持服务',
        );

        $method  = strtolower($method);
        if (array_key_exists($method,$pageName))
        {
            $this->title = $pageName[$method];
            $this->display($method);
        }
        else
        {
            $this->error('错误操作！');
        }
    }

    // 友情连接
    public function links() {
        $Link =  M("Link");
        $list   =  $Link->where('status=1 and type=0')->order('sort')->select();
        $this->assign('list',$list);
        $this->title  =  '友情链接';
        $this->display();
    }

// 类定义 end
}
?>