<?php
/**
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: sun(slf02@ourstu.com)
 * Date: 2018/9/25
 * Time: 14:01
 */

namespace app\article\controller;


use app\common\controller\Api;

class Index extends Api
{
    /**
     * @author sun slf02@ourstu.com
     * @date 2018/9/25 14:06
     * 获取文章列表
     */
    public function getArticleList()
    {
        $aPage=input('post.page',1,'intval');
        $list=db('Article')->where('status','=',1)->page($aPage,10)->order('create_time desc')->select();
        if($list){
            foreach ($list as &$value){
                $value['create_time']=time_format($value['create_time']);
                $value['cover']=pic($value['cover']);
            }
            unset($value);
            $this->apiSuccess($list);
        }else{
            $this->apiSuccess('没有数据');
        }
    }

    /**
     * @author sun slf02@ourstu.com
     * @date 2018/9/25 14:08
     * 获取文章详情页
     */
    public function getArticleDetail()
    {
        $aId=input('get.id',0,'intval');
        if($aId){
            $this->apiError('非法请求');
        }
        $info=db('Article')->find($aId);
        $this->apiSuccess($info);

    }
}