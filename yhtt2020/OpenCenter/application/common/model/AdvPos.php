<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: lin(lt@ourstu.com)
 * Date: 2018/9/11
 * Time: 13:35
 * ----------------------------------------------------------------------
 */
namespace app\common\model;

use think\Model;

class AdvPos extends Model
{
    protected $table = COMMON . 'adv_pos';

    public function getTypeTextAttr($value,$data)
    {
        $status = [1=>'单图',2=>'多图',3=>'文字链接',4=>'代码'];
        return $status[$data['type']];
    }

    public function getStatusTextAttr($value,$data)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $status[$data['status']];
    }

    /**
     * @param $name
     * @param $path
     * @return array|mixed|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author:lin(lt@ourstu.com)
     */
    public function getInfo($name, $path)
    {
        $advPos = cache('adv_pos_by_pos_' .$path. $name);
        if (empty($advPos)) {
            $advPos = $this->where(array('name' => $name, 'path' => $path, 'status' => 1))->find();
            if(!empty($advPos)){
                $advPos['type_text'] = $advPos->type_text;
                $advPos['status_text'] = $advPos->status_text;
                $advPos->toArray();
            }
            cache('adv_pos_by_pos_'  .$path. $name,$advPos);
        }
        return $advPos;
    }

}