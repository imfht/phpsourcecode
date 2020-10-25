<?php

namespace app\index\controller;

use think\Controller;

/**
 * @title   模块名称
 * @desc    我是模块名称
 * Class Index
 * @package app\api\controller\v1
 */
class Index extends Controller
{
    /**
     * @title 方法1
     * @desc  类的方法1
     * @url   url('api/v1.index/index',true,'',true)
     *
     * @param int $page  0 999
     * @param int $limit 10
     *
     * @return int $id 0 索引
     * @return int $id 0 索引
     * @return int $id 0 索引
     */
    public function index($page = 1, $limit = 10)
    {
        return [];
    }

    /**
     * @title   方法2
     * @desc    类的方法2 哦
     * @author  OkCoder
     * @version 1.0
     *
     * @param int $page  0 999
     * @param int $limit 10
     *
     * @return int $id 0 索引
     * @return int $id 0 索引
     * @return int $id 0 索引
     */
    public function uuurl()
    {

    }
}