<?php
namespace app\admin\model;

use think\Model;

/**
 * 插件模型
 */
class Addons extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 新增或编辑数据
     *
     * @param      <type>  $data   The data
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function editData($data)
    {
        if(!empty($data['id'])){
            $res = $this->allowField(true)->save($data,$data['id']);
        }else{
            $res = $this->allowField(true)->save($data);
        }
        if($res){
            return $this->id;
        }else{
            return false;
        }
        
    }

    /**
     * [getListByPage description]
     * @param  [type]  $map   [description]
     * @param  string  $order [description]
     * @param  string  $field [description]
     * @param  integer $r     [description]
     * @return [type]         [description]
     */
    public function getListByPage($map,$order='sort desc,create_time desc',$field='*',$r=20)
    {
        $this->reload();

        $list = $this->where($map)->order($order)->field($field)->paginate($r,false,['query'=>request()->param()]);

        foreach ($list as &$val) {
            $val['icon'] = $this->getIcon($val['name']);
            $class  = get_addon_class($val['name']);
            if(!class_exists($class)){
                $val['has_config'] = 0;
            }else{
                $addon = new $class();
                $val['has_config'] = count($addon->getConfig());
            }
        }
        unset($val);

        return $list;
    }

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
        $install_flag = $addons->install();
        if (!$install_flag) {
            $this->error = lang('_PERFORM_A_PLUG__IN__OPERATION_FAILED_') . session('addons_install_error');
            return false;
        }

        //获取库内插件信息
        $data = $this->getAddon($name);
        $data = $data->toArray();
        $data['config'] = json_encode($addons->getConfig());
        $data['is_setup'] = 1;
        
        if (!$data) {
            $this->error = $this->getError();
            return false;
        }
        if ($this->save($data,['id'=>$data['id']])) {
            $hooks_update = model('Hooks')->updateHooks($name);
            if ($hooks_update) {
                cache('hooks', null);
                return true;
            } else {
                $this->error = lang('_THE_UPDATE_HOOK_IS_FAILED_PLEASE_TRY_TO_REINSTALL_');
                return false;
            }

        } else {
            $this->error = lang('_WRITE_PLUGIN_DATA_FAILED_');
            return false;
        }
    }

    /**
     * 卸载插件
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function uninstall($name)
    {
        //获取库内插件信息
        $data = $this->getAddon($name);
        $data = $data->toArray();
        $data['is_setup'] = 0;

        if ($this->save($data,['id'=>$data['id']])) {
            $hooks_update = model('Hooks')->removeHooks($name);
            if ($hooks_update === false) {
                $this->error(lang('_FAILED_HOOK_MOUNTED_DATA_UNINSTALL_PLUG-INS_'));
            }
            cache('hooks', null);
            return true;
        } else {
            $this->error = lang('_UNINSTALL_PLUG-IN_FAILED_');
            return false;
        }
    }

    /**
     * 获取图标
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function getIcon($name) 
    {
        $file = PUBLIC_PATH . '/static/addons/'.$name.'/images/icon.png';
        
        if(file_exists($file)){
            return STATIC_URL . '/addons/'.$name.'/images/icon.png';
        }else{
            return STATIC_URL . '/admin/images/plugin.png';
        }
    }

    /**
     * 获取插件的后台列表
     */
    public function getAdminList()
    {
        $admin = [];
        $db_addons = $this->where("status=1 AND has_adminlist=1 AND is_setup=1")->field('title,name')->select();
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

    /**
     * [getAll description]
     * @return [type] [description]
     */
    public function getAll($where = [])
    {
        $result = $this->where($where)->order('sort desc,id desc')->select();

        return $result;
    }

    /**
     * 在库里获取单个插件信息
     * @return [type] [description]
     */
    public function getAddon($name)
    {
        $map['name'] = $name;
        $res = $this->where($map)->find();

        return $res;
    }

    /**
     * 重新加载插件并处理数据表
     * @return [type] [description]
     * @author 严大蒙同学<59262424@qq.com>
     */
    private function reload()
    {
        $dirs = array_map('basename', glob(ADDONS_PATH . '*', GLOB_ONLYDIR));

        if ($dirs === FALSE || !file_exists(ADDONS_PATH)) {
            $this->error = lang('_THE_PLUGIN_DIRECTORY_IS_NOT_READABLE_OR_NOT_');
            return FALSE;
        }
        //初始化空容器
        $addons = [];
        foreach ($dirs as $value) {
            if (!isset($addons[$value])) {
                $class = get_addon_class($value);
                
                if (!class_exists($class)) { // 实例化插件失败忽略执行
                    \think\Log::record(lang('_PLUGIN_') . $value . lang('_THE_ENTRY_FILE_DOES_NOT_EXIST_WITH_EXCLAMATION_'));
                    continue;
                }
                $obj = new $class;
                $info = $obj->info;
                if ($info) {
                    unset($info['status']);
                }
                $admin = $obj->admin;
                if($admin == 1 || $admin == true){
                    $info['has_adminlist'] = 1;
                }

                //合并数据表内模块
                $db_info = $this->getAddon($info['name']);
                if($db_info){
                    $db_info = $db_info->toArray();
                    
                    if(is_array($db_info)){
                        $info = array_merge($db_info, $info);
                    }
                }
                $addons[] = $info;
            }
        }

        //写入数据库
        $this->saveAll($addons);

        //移除已删除的模块目录
        $db_list = $this->getAll();
        foreach($db_list as $val){
            if(!is_dir(ADDONS_PATH . '/' .$val['name'])){
                $this->destroy(['id' => $val['id']]);
            }
        }
    }
}
