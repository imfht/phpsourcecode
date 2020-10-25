<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\model\Common as CommonModel;
use think\facade\Cache;
use think\facade\Env;

/**
 * 操作日志控制器
 * @author rainfer <rainfer520@qq.com>
 */
class WebLog extends Base
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new CommonModel();
        $this->model = $this->model->setTable(config('database.prefix') . 'web_log')->setPk('id');
    }

    /*
     * 网站日志列表
     */
    public function weblogIndex()
    {
        $methods         = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'PATCH', 'OPTIONS', 'Ajax', 'Pjax'];
        $request_module  = input('request_module', '');
        $controllers     = [];
        $controllers_arr = [];
        if ($request_module) {
            $controllers_arr = \ReadClass::readDir(Env::get('app_path') . $request_module . DIRECTORY_SEPARATOR . 'controller');
            $controllers     = array_keys($controllers_arr);
        }
        $request_controller = input('request_controller', '');
        $actions            = [];
        if ($request_module && $request_controller) {
            $actions = $controllers_arr[$request_controller];
            $actions = array_map('array_shift', $actions['method']);
        }
        $request_action = input('request_action', '');
        $request_method = input('request_method', '');
        //组成where
        $where = [];
        $join  = [
            [config('database.prefix') . 'user b', 'b.id=a.uid', 'LEFT']
        ];
        if ($request_module) {
            $where['module'] = $request_module;
        }
        if ($request_controller) {
            $where['controller'] = $request_controller;
        }
        if ($request_action) {
            $where['action'] = $request_action;
        }
        if ($request_method) {
            $where['method'] = $request_method;
        }
        $weblog_list = $this->model->alias('a')->join($join)->where($where)
            ->order('otime desc')->paginate(config('paginate.list_rows'), false, ['query' => get_query()]);
        $show        = $weblog_list->render();
        $show        = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $show);
        $this->assign('weblog_list', $weblog_list);
        $this->assign('page', $show);
        $this->assign('request_module', $request_module);
        $this->assign('request_controller', $request_controller);
        $this->assign('request_action', $request_action);
        $this->assign('request_method', $request_method);
        $this->assign('controllers', $controllers);
        $this->assign('actions', $actions);
        $this->assign('methods', $methods);
        if (request()->isAjax()) {
            return $this->fetch('ajax_weblog_index');
        } else {
            return $this->fetch();
        }
    }

    /*
     * 网站日志删除
     */
    public function weblogDel()
    {
        $rst = $this->model->delete(input('id'));
        if ($rst !== false) {
            $this->success('删除成功', 'weblogIndex');
        } else {
            $this->error("删除失败", 'weblogIndex');
        }
    }

    /*
     * 网站日志全选删除
     */
    public function weblogAlldel()
    {
        $ids = input('id/a');
        if (empty($ids)) {
            $this->error("请至少选择一行", 'weblogIndex');
        }
        if (is_array($ids)) {
            $where = 'id in(' . implode(',', $ids) . ')';
        } else {
            $where = 'id=' . $ids;
        }
        $rst = $this->model->where($where)->delete();
        if ($rst !== false) {
            $this->success("删除成功", 'weblogIndex');
        } else {
            $this->error("删除失败", 'weblogIndex');
        }
    }

    /*
     * 网站日志清空
     */
    public function weblogDrop()
    {
        $rst = $this->model->where('id', 'gt', 0)->delete();
        if ($rst !== false) {
            $this->success('清空成功', 'weblogIndex');
        } else {
            $this->error("清空失败", 'weblogIndex');
        }
    }

    /*
     * 网站日志设置显示
     */
    public function weblogSetting()
    {
        $web_log = config('yfcmf.web_log');
        //模块
        $web_log['not_log_module']         = (isset($web_log['not_log_module']) && $web_log['not_log_module']) ? join(',', $web_log['not_log_module']) : '';
        $web_log['not_log_controller']     = (isset($web_log['not_log_controller']) && $web_log['not_log_controller']) ? join(',', $web_log['not_log_controller']) : '';
        $web_log['not_log_action']         = (isset($web_log['not_log_action']) && $web_log['not_log_action']) ? join(',', $web_log['not_log_action']) : '';
        $web_log['not_log_data']           = (isset($web_log['not_log_data']) && $web_log['not_log_data']) ? join(',', $web_log['not_log_data']) : '';
        $web_log['not_log_request_method'] = (isset($web_log['not_log_request_method']) && $web_log['not_log_request_method']) ? join(',', $web_log['not_log_request_method']) : '';
        //控制器 模块
        $controllers = [];
        $actions     = [];
        $modules     = ['home', 'admin', 'install'];
        foreach ($modules as $module) {
            $arr = cache('controllers_' . $module);
            if (empty($arr)) {
                $arr = \ReadClass::readDir(Env::get('app_path') . $module . DIRECTORY_SEPARATOR . 'controller');
                cache('controllers' . '_' . $module, $arr);
            }
            if ($arr) {
                foreach ($arr as $key => $v) {
                    $controllers[$module][]        = $module . '/' . $key;
                    $actions[$module . '/' . $key] = array_map('array_shift', $v['method']);
                }
            }
        }
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'PATCH', 'OPTIONS', 'Ajax', 'Pjax'];
        $this->assign('methods', $methods);
        $this->assign('actions', $actions);
        $this->assign('modules', $modules);
        $this->assign('controllers', $controllers);
        $this->assign('web_log', $web_log);
        return $this->fetch();
    }

    /*
     * 网站日志设置保存
     */
    public function weblogUpdate()
    {
        $weblog_on = input('weblog_on', 0, 'intval') ? true : false;
        //设置tags
        $configs     = include Env::get('app_path') . 'tags.php';
        $module_init = $configs['module_init'];
        if ($weblog_on) {
            if (!in_array('app\\common\\behavior\\WebLog', $module_init)) {
                $module_init[] = 'app\\common\\behavior\\WebLog';
            }
        } else {
            $key = array_search('app\\common\\behavior\\WebLog', $module_init);
            if ($key !== false) {
                unset($module_init[$key]);
            }
        }
        $configs = array_merge($configs, ['module_init' => $module_init]);
        file_put_contents(Env::get('app_path') . 'tags.php', "<?php\treturn " . var_export($configs, true) . ";");
        $web_log['weblog_on']              = $weblog_on;
        $web_log['not_log_module']         = input('not_log_module/a');
        $web_log['not_log_controller']     = input('not_log_controller/a');
        $web_log['not_log_action']         = input('not_log_action/a');
        $web_log['not_log_data']           = input('not_log_data/a');
        $web_log['not_log_request_method'] = input('not_log_request_method/a');
        $rst                               = sys_config_setbykey('web_log', $web_log);
        if ($rst) {
            Cache::clear();
            $this->success('设置保存成功', 'weblogSetting');
        } else {
            $this->error('设置保存失败', 'weblogSetting');
        }
    }
}
