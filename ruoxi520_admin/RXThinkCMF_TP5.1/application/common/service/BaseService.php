<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;

use think\Model;

/**
 * 服务层基类
 * @author 牧羊人
 * @since 2020/7/11
 * Class BaseService
 * @package app\common\service
 */
class BaseService extends Model
{
    // 模型
    protected $model;

    /**
     * 获取数据列表
     * @return array
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function getList()
    {
        // 初始化变量
        $map = [];
        $sort = 'id desc';
        $is_sql = 0;

        // 获取参数
        $argList = func_get_args();
        if (!empty($argList)) {
            // 查询条件
            $map = (isset($argList[0]) && !empty($argList[0])) ? $argList[0] : [];
            // 排序
            $sort = (isset($argList[1]) && !empty($argList[1])) ? $argList[1] : 'id desc';
            // 是否打印SQL
            $is_sql = isset($argList[2]) ? isset($argList[2]) : 0;
        }

        // 常规查询条件
        $param = request()->param();
        if ($param) {
            // 筛选名称
            if (isset($param['name']) && $param['name']) {
                $map[] = ['name', 'like', "%{$param['name']}%"];
            }

            // 筛选标题
            if (isset($param['title']) && $param['title']) {
                $map[] = ['title', 'like', "%{$param['title']}%"];
            }

            // 筛选类型
            if (isset($param['type']) && $param['type']) {
                $map[] = ['type', '=', $param['type']];
            }

            // 筛选状态
            if (isset($param['status']) && $param['status']) {
                $map[] = ['status', '=', $param['status']];
            }

            // 手机号码
            if (isset($param['mobile']) && $param['mobile']) {
                $map[] = ['mobile', '=', $param['mobile']];
            }
        }

        // 设置查询条件
        if (is_array($map)) {
            $map[] = ['mark', '=', 1];
        } elseif ($map) {
            $map .= " AND mark=1 ";
        } else {
            $map .= " mark=1 ";
        }
        $result = $this->model->where($map)->order($sort)->page(PAGE, PERPAGE)->column("id");

        // 打印SQL
        if ($is_sql) {
            echo $this->model->getLastSql();
        }

        $list = [];
        if (is_array($result)) {
            foreach ($result as $val) {
                $info = $this->model->getInfo($val);
                $list[] = $info;
            }
        }

        //获取数据总数
        $count = $this->model->where($map)->count();

        //返回结果
        $message = array(
            "msg" => '操作成功',
            "code" => 0,
            "data" => $list,
            "count" => $count,
        );
        return $message;
    }

    /**
     * 添加或编辑
     * @return array
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function edit()
    {
        // 获取参数
        $argList = func_get_args();
        // 查询条件
        $data = isset($argList[0]) ? $argList[0] : [];
        // 是否打印SQL
        $is_sql = isset($argList[1]) ? $argList[1] : false;
        if (!$data) {
            $data = request()->param();
        }
        $error = '';
        $rowId = $this->model->edit($data, $error, $is_sql);
        if ($rowId) {
            return message();
        }
        return message($error, false);
    }

    /**
     * 设置状态
     * @return array
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function setStatus()
    {
        $data = request()->param();
        if (!$data['id']) {
            return message('记录ID不能为空', false);
        }
        if (!$data['status']) {
            return message('记录状态不能为空', false);
        }
        $error = '';
        $rowId = $this->model->edit($data, $error);
        if (!$rowId) {
            return message($error, false);
        }
        return message();
    }
}
