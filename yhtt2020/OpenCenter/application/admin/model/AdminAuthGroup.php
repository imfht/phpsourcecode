<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/12
 * Time: 16:05
 * ----------------------------------------------------------------------
 */
namespace app\admin\model;

use think\Model;

class AdminAuthGroup extends Model
{
    protected $table = ADMIN . 'auth_group';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 权限分组信息验证
     * @param array $data
     * @author:wdx(wdx@ourstu.com)
     */
    public function _checkAdminAuthGroupData($data = [])
    {
        if (!$data['title']) {
            $this->error('请输入分组名称');
        }
        //唯一性校验
        if (isset($data['id'])) {
            if ($this->adminAuthGroup->where('title', $data['title'])->value('id') != $data['id']) {
                $this->error('分组名称已存在');
            }
        } else {
            if ($this->adminAuthGroup->where('title', $data['name'])->value('id')) {
                $this->error('分组名称已存在');
            }
        }
        if (!$data['module']) {
            $this->error('请输入模块名称');
        }
        if (!$data['type']) {
            $this->error('请选择分类');
        }
        if (!$data['end_time']) {
            $this->error('请选择有效期限');
        }
        if (!$data['rules']) {
            $this->error('请分配权限');
        }
    }
}