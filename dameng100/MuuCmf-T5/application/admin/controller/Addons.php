<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;
/**
 * 扩展后台插件管理页面
 */
class Addons extends Admin
{

    public function _initialize()
    {
        $this->assign('_extra_menu', array(
            lang('_ALREADY_INSTALLED_IN_THE_BACKGROUND_') => model('admin/Addons')->getAdminList(),
        ));
        parent::_initialize();
    }

    /**
     * 插件列表
     */
    public function index()
    {
        $param = input('');
        $type = isset($param['type']) ? $param['type'] : 'all';

        $map = [];
        if($type == 'yes'){
            $map['is_setup'] = 1;
        }
        if($type == 'no'){
            $map['is_setup'] = 0;
        }

        $list = model('Addons')->getListByPage($map);
        // 获取分页显示
        $page = $list->render();

        $this->setTitle(lang('_PLUGIN_LIST_'));
        $this->assign('type', $type);
        $this->assign('_list', $list);
        $this->assign('page', $page);
        
        return $this->fetch();
    }

    /**
     * 设置状态
     */
    public function setStatus()
    {
        $data['id'] = input('id');
        $status = input('status');
        if($status == 'enable'){
            $data['status'] = 1;
            $msg = lang('_ENABLE_SUCCESS_');
        }else{
            $data['status'] = 0;
            $msg = lang('_DISABLE_SUCCESS_');
        }

        $res = model('Addons')->editData($data);

        if($res){
            $this->success($msg);
        }else{
            $this->error('error');
        }
    }

    /**
     * 设置插件页面
     */
    public function config()
    {
        $id = (int)input('id');
        $addon = Db::name('Addons')->find($id);
        if (!$addon)
            $this->error(lang('_PLUGIN_NOT_INSTALLED_'));

        $addon_class = get_addon_class($addon['name']);

        if (!class_exists($addon_class))
            trace(lang('_FAIL_ADDON_PARAM_',array('model'=>$addon['name'])), 'ADDONS', 'ERR');

        $data = new $addon_class;

        $addon['addon_path'] = $data->addons_path;
        $addon['custom_config'] = $data->custom_config;
        $this->meta_title = lang('_ADDONS_SET_') . $data->info['title'];
        $db_config = $addon['config'];
        $addon['config'] = include $data->config_file;

        if ($db_config) {
            $db_config = json_decode($db_config, true);
            foreach ($addon['config'] as $key => $value) {
                if ($value['type'] != 'group') {
                    if(!empty($db_config[$key])){
                        $addon['config'][$key]['value'] = $db_config[$key];
                    }else{
                        $addon['config'][$key]['value'] = '';
                    }
                    
                } else {
                    foreach ($value['options'] as $gourp => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            $addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                        }
                    }
                }
            }
        }
        
        $this->assign('data', $addon);
        if ($addon['custom_config'])
            $this->assign('custom_config', $this->fetch($addon['addon_path'] . $addon['custom_config']));
        return $this->fetch();
    }

    /**
     * 保存插件设置
     */
    public function saveConfig()
    {
        $id = (int)input('id');
        $config = input('config/a');
        $flag = Db::name('Addons')->where(['id'=>$id])->setField('config', json_encode($config));
        if (isset($config['addons_cache'])) {//清除缓存
            cache($config['addons_cache'], null);
        }
        if ($flag !== false) {
            cache('hooks', null);
            $this->success(lang('_SAVE_'));
        } else {
            $this->error(lang('_SAVE_FAILED_'));
        }
    }

    /**
     * 安装插件
     */
    public function install()
    {
        $id = input('id',0,'intval');
        $addon_name = trim(input('addon_name'));
        $rs = model('admin/Addons')->install($addon_name);
        if ($rs === true) {
            $this->success(lang('_INSTALL_PLUG-IN_SUCCESS_'));
        } else {
            $this->error(model('admin/Addons')->getError());
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall()
    {
        $id = trim(input('id'));
        $db_addons = Db::name('Addons')->find($id);
        $class = get_addon_class($db_addons['name']);

        if (!$db_addons || !class_exists($class))
            $this->error(lang('_PLUGIN_DOES_NOT_EXIST_'));

        $addons = new $class;
        $uninstall_flag = $addons->uninstall();
        if (!$uninstall_flag)
            $this->error(lang('_EXECUTE_THE_PLUG-IN_TO_THE_PRE_UNLOAD_OPERATION_FAILED_') . session('addons_uninstall_error'));
        
        $res = model('Addons')->uninstall($db_addons['name']);
        if ($res === false) {
            $this->error(model('admin/Addons')->getError());
        } else {
            $this->success(lang('_SUCCESS_UNINSTALL_'));
        }
    }

    /**
     * 钩子管理列表
     */
    public function hooks()
    {
        $this->setTitle(lang('_HOOK_LIST_'));
        $map = $fields = ['id'=>['>',0]];

        list($list,$page) = $this->commonLists('Hooks', $map, 'id desc', []);
        $list = $list->toArray()['data'];
        int_to_string($list, ['type' => config('HOOKS_TYPE')]);

        $this->assign('list', $list);
        
        return $this->fetch();
    }

    /**
     * 新增钩子
     * @return [type] [description]
     */
    public function addhook()
    {
        $this->assign('data', null);
        $this->setTitle(lang('_NEW_HOOK_'));
        return $this->fetch('edithook');
    }

    //钩子出编辑挂载插件页面
    public function edithook($id)
    {
        $hook = Db::name('Hooks')->field(true)->find($id);
        //所有插件
        $all_addons = model('Addons')->getAll(['status'=>1,'is_setup'=>1]);
        $all_addons = array_combine(array_column($all_addons,'name'),$all_addons);

        $all_addons_arr = [];
        foreach ($all_addons as $key => $v) {
            $all_addons_arr[] = ['name'=>$v['name'],'title' => $v['title']];
        }
        
        //已挂载数据
        if(empty($hook['addons'])){
            $ok_addons = [];
        }else{
           $ok_addons = explode(',',$hook['addons']); 
        }
        
        $ok_addons_arr = [];
        foreach ($ok_addons as $key => $v) {
            //为避免数组中含有已卸载插件，这里做个判断
            if(!empty($all_addons[$v]['name'])){
                $ok_addons_arr[] = ['name'=>$all_addons[$v]['name'],'title' =>$all_addons[$v]['title']];
            }
        }
        //未挂载去除差值
        $tmp_arr =[];//声明数组
        foreach($all_addons_arr as $k => $v)
        {
            if(in_array($v, $ok_addons_arr))
            {
                unset($all_addons_arr[$k]);
            }else {
                $tmp_arr[] = $v;
            }
        }
        $all_addons_arr = $tmp_arr;
        //清理缓存
        cache('hooks', null);
        $this->assign('data', $hook);
        //看板挂载数据
        $this->assign('all_addons_arr', $all_addons_arr);
        $this->assign('ok_addons_arr', $ok_addons_arr);

        $this->setTitle(lang('_EDIT_HOOK_'));
        return $this->fetch('edithook');
    }

    //超级管理员删除钩子
    public function delhook($id)
    {
        if (Db::name('Hooks')->delete($id) !== false) {
            cache('hooks', null);
            $this->success(lang('_DELETE_SUCCESS_'));
        } else {
            $this->error(lang('_DELETE_FAILED_'));
        }
    }

    /**
     * 编辑、新增钩子处理
     * @return [type] [description]
     */
    public function updateHook()
    {
        if(request()->isPost())
        {
            $data = input('');
            $res = model('admin/Hooks')->editData($data);
            if ($res !== false){
                cache('hooks', null);
                $this->success(lang('_UPDATE_'), url('hooks'));
            }else{
                $this->error(lang('_UPDATE_FAILED_'));
            }
            /*
            if ($data) {
                if ($data['id']) {
                    $flag = Db::name('Hooks')->where(['id'=>$data['id']])->update($data);
                    if ($flag !== false){
                       cache('hooks', null);
                        $this->success(lang('_UPDATE_'), Cookie('__forward__')); 
                    }else{
                        $this->error(lang('_UPDATE_FAILED_'));
                    }
                } else {
                    $flag = Db::name('Hooks')->allowField(true)->insert($data);
                    if ($flag){
                        cache('hooks', null);
                        $this->success(lang('_NEW_SUCCESS_'), Cookie('__forward__'));
                    }else{
                        $this->error(lang('_NEW_FAILURE_'));
                    }
                }

            } else {
                $this->error($hookModel->getError());
            }
            */
        }
        
    }

    public function del($id = '', $name)
    {
        $ids = array_unique((array)input('ids', 0));

        if (empty($ids)) {
            $this->error(lang('_ERROR_DATA_SELECT_'));
        }

        $class = get_addon_class($name);
        if (!class_exists($class))
            $this->error(lang('_PLUGIN_DOES_NOT_EXIST_'));
        $addon = new $class();
        $param = $addon->admin_list;
        if (!$param)
            $this->error(lang('_THE_PLUGIN_LIST_INFORMATION_IS_NOT_CORRECT_'));
        extract($param);
        if (isset($model)) {
            $addonModel = model("Addons://{$name}/{$model}");
            if (!$addonModel)
                $this->error(lang('_MODEL_CANNOT_BE_REAL_'));
        }

        $map = array('id' => array('in', $ids));
        if ($addonModel->where($map)->delete()) {
            cache('hooks', null);
            $this->success(lang('_DELETE_SUCCESS_'));
        } else {
            $this->error(lang('_DELETE_FAILED_'));
        }
    }

}
