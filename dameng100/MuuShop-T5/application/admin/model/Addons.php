<?php
namespace app\admin\model;

use think\Model;

/**
 * 插件模型
 */
class Addons extends Model
{
    /**
     * 插件安装
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function install($name)
    {

        $class = get_addon_class($name);
        if (!class_exists($class)) {
            $this->error = lang('_PLUGIN_DOES_NOT_EXIST_');
            return false;
        }
        $addons = new $class;
        //获取插件基础信息
        $info = $addons->info;

        if (!$info || !$addons->checkInfo())//检测信息的正确性
        {
            $this->error = lang('_PLUGIN_INFORMATION_MISSING_');
            return false;
        }
        session('addons_install_error', null);
        $install_flag = $addons->install();
        if (!$install_flag) {
            $this->error = lang('_PERFORM_A_PLUG__IN__OPERATION_FAILED_') . session('addons_install_error');
            return false;
        }
        
        $data = (array)$info;

        if ($addons->admin == 1 || $addons->admin == true) {
            $data['has_adminlist'] = 1;
        } else {
            $data['has_adminlist'] = 0;
        }
        if (!$data) {
            $this->error = $this->getError();
            return false;
        }
        if ($this->save($data)) {
            $config = ['config' => json_encode($addons->getConfig())];
            $this->save($config,['name'=>$name]);

            $hooks_update = model('Hooks')->updateHooks($name);
            if ($hooks_update) {
                cache('hooks', null);
                return true;
            } else {
                $this->where(['name'=>$name])->delete();
                $this->error = lang('_THE_UPDATE_HOOK_IS_FAILED_PLEASE_TRY_TO_REINSTALL_');
                return false;
            }

        } else {
            $this->error = lang('_WRITE_PLUGIN_DATA_FAILED_');
            return false;
        }
    }


    /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public function getList()
    {
        $addon_dir = ADDONS_PATH;
        $dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));

        if ($dirs === FALSE || !file_exists($addon_dir)) {
            $this->error = lang('_THE_PLUGIN_DIRECTORY_IS_NOT_READABLE_OR_NOT_');
            return FALSE;
        }

        $addons = [];
        $where['name'] = ['in', $dirs];
        $list = collection($this->where($where)->select())->toArray();

        foreach ($list as $addon) {
            $addon['uninstall'] = 0;
            $addon['icon_photo'] = $this->getIcon($addon['name']);
            $addons[$addon['name']] = $addon;
        }

        foreach ($dirs as $value) {
            if (!isset($addons[$value])) {
                $class = get_addon_class($value);

                if (!class_exists($class)) { // 实例化插件失败忽略执行
                    \think\Log::record(lang('_PLUGIN_') . $value . lang('_THE_ENTRY_FILE_DOES_NOT_EXIST_WITH_EXCLAMATION_'));
                    continue;
                }

                $obj = new $class;
                $addons[$value] = $obj->info;
                if ($addons[$value]) {
                    $addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
                }
                $addons[$value]['icon_photo'] = $this->getIcon($addons[$value]['name']);
            }
        }
        
        int_to_string($addons, ['status' => [-1 => lang('_DAMAGE_'), 0 => lang('_DISABLE_'), 1 => lang('_ENABLE_'), null => lang('_NOT_INSTALLED_')]]);

        $addons = list_sort_by($addons, 'uninstall', 'desc');
        return $addons;
    }
    /**
     * 获取图标
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function getIcon($name) 
    {
        $file = './static/addons/'.$name.'/images/icon.png';
        if(is_file($file)){
            return '/static/addons/'.$name.'/images/icon.png';
        }else{
            return '';
        }
    }
    /**
     * 获取插件的后台列表
     */
    public function getAdminList()
    {
        $admin = [];
        $db_addons = $this->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if ($db_addons) {
            foreach ($db_addons as $value) {
                $admin[] = [
                    'title' => $value['title'],
                    'url' => "addons/execute/{$value['name']}-admin-index"
                ];
            }
        }
        return $admin;
    }
}
