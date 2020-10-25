<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Blog\Controller;

use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends BlogController
{

    //系统首页
    public function index($page = 1)
    {

        /* 分类信息 */
        $category = 0; //$this->category();

        /* 获取当前分类列表 */
        $Document = D('Document');
        $list = $Document->page($page, 10)->lists($category['id']);
        if (false === $list) {
            $this->error('获取列表数据失败！');
        }



        /* 模板赋值并渲染模板 */
        $this->assign('category', $category);
        $this->assign('list', $list);

        $this->assign('page', D('Document')->page); //分页


        $this->display();
    }

    /* 文档分类检测 */
    private function category($id = 0)
    {

    }
}