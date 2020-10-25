<?php

class BackReportController extends BackBaseController {

    /**
     * 报告首页
     */
    public function index() {
        return View::make('BackTheme::templates.report.index');
    }

    /**
     * 获取最近注册的用户数量信息
     * @return type
     */
    public function register_count() {
        $register_count = Report::get_register_count();
        return View::make('BackTheme::templates.report.register.index', array('register_count' => $register_count));
    }

    /**
     * 获取最近发布的文章数量信息
     * @return type
     */
    public function node_count() {
        $node_count = Report::get_node_count();
        return View::make('BackTheme::templates.report.node.index', array('node_count' => $node_count));
    }

    /**
     * 用户操作日志
     * @return type
     */
    public function logs() {
        $logs = Logs::orderBy('created_at', 'desc')->paginate(15);
        foreach ($logs as $key => $row) {
            switch ($row['type']) {
                case 'add':
                    $logs[$key]['type'] = '添加';
                    break;
                case 'edit':
                    $logs[$key]['type'] = '更新';
                    break;
                case 'delete':
                    $logs[$key]['type'] = '删除';
                    break;
                case 'login':
                    $logs[$key]['type'] = '登陆';
                    break;
                case 'other':
                    $logs[$key]['type'] = '其他';
                    break;
            }
            //$logs[$key]['type'] = $row
        }
        return View::make('BackTheme::templates.report.logs.index', array('logs' => $logs));
    }

}
