<?php
namespace Admin\Controller;

use Think\Controller;


class AdminController extends Controller {
    /**
     * 模块初始化方法
     * 1.检测用户是否登录
     * 2.获取权限菜单
     */
    function _initialize() {
        header('Content-Type: text/html; charset=utf-8');
        // 获取当前用户ID
        define('UID',is_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }
    }

    /**
     * 通用返回列表
     *
     * @param string $model 模型名
     * @param array $where  查询条件
     */
    function lists($model = '', $where = array(), $field = true) {
        $ret = array();
        $Model = M($model);
        $ret['results'] = $Model->where($where)->count();
        $ret['rows'] = $Model->field($field)->where($where)->limit(I('post.start'),I('post.limit'))->select();
        //$ret['sql'] = $Model->getLastSql();

        exit(json_encode($ret));
    }


    /**
     * 构建ajax返回信息
     *
     * $msg = 'SUC' 时仅返回执行成功标识，其他情况为错误信息
     *
     * @param string $msg
     * @param int $id
     */
    function ajaxReturn($msg = 'SUC', $id = 0) {
        $ret = array();
        $id = intval($id);
        if($msg == 'SUC') {
            $ret['success'] = true;
            if($id) {
                $ret['id'] = $id;
            }
        } else {
            $ret['hasError'] = true;
            $ret['error'] = $msg;
        }

        exit(json_encode($ret));
    }
}
