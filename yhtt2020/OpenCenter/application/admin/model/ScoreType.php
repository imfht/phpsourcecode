<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/10/17
 * Time: 19:45
 * ----------------------------------------------------------------------
 */
namespace app\admin\model;

use think\model;

class ScoreType extends model
{
    protected $table = USER . 'score_type';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 获取积分类型列表
     * @author:wdx(wdx@ourstu.com)
     */
    public function getList($map = [], $page = 1, $limit = 20)
    {
        $list = $this->where($map)->page($page, $limit)->select();
        return $list;
    }

    /**
     * 获取积分标题列表
     * @return array
     * @author:wdx(wdx@ourstu.com)
     */
    public function getTypeList()
    {
        $data = $this->where('status', 1)->column('title', 'id');
        return $data;
    }
}