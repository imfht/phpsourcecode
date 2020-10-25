<?php

/**
 * 插件模型
 * Class AddonModel
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class AddonsModel extends Model
{
    public $table = 'addons';
    private $id;
    private $addon; //插件名

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->id = Q('id', 0, 'intval'); //插件id
        $this->addon = Q('addon', ''); //插件名
    }

    /**
     * [getAddonList 插件列表]
     * @return [type] [description]
     */
    public function getAddonList()
    {
        // 取得模块目录名称
        $dirs = array_map('basename', glob(APP_ADDON_PATH . '*', GLOB_ONLYDIR));
        if (empty($dirs))
        {
            return false;
        }
        $addons = array();
        $addonList = $this->where(array('name' => array('IN', $dirs)))->order('id ASC')->all();
        if ($addonList)
        {
            foreach ($addonList as $addon)
            {
                $addon['install'] = 1;
                $addon['config'] = unserialize($addon['config']);
                $addons[$addon['name']] = $addon;
            }
        }
        
        foreach ($dirs as $d)
        {
            $addonObj = $this->getAddonObj($d); //获得插件对象
            if (!$addonObj) continue;
            //没有安装插件
            if (!isset($addons[$d])) {
                $addon = $addonObj->info;
                $addon['status']=0;
                $addon['install'] = 0;
                $addon['config'] = $addonObj->getConfig(); //获得插件配置
                $addons[$d] = $addon;
            } else {
                //前台文件
                $indexActionFile = APP_ADDON_PATH . $d . '/Controller/IndexController.class.php';
                $addons[$d]['IndexAction'] = is_file($indexActionFile) ? __WEB__."?g=Addon&m={$d}&c=Index&a=index": '';
            }
            //插件帮助文档
            $addons[$d]['help'] = is_file(APP_ADDON_PATH . $d . '/help.html') ? U('help') . '&addon=' . $d : '';
        }
        int_to_string($addons, array('status' => array(1 => '启用', 0 => '禁用')));
        ksort($addons);
        return $addons;
    }




    /**
     * [disabledAddon 禁用插件]
     * @return [type] [description]
     */
    public function disabledAddon()
    {
        if (!$this->addon) {
            $this->error = '参数错误';
            return false;
        }
        if (!$this->where(array('name' => $this->addon))->find()) {
            $this->error = '插件不存在';
            return false;
        }
        return $this->where(array('name' => $this->addon))->save(array('status' => 0));
    }

    /**
     * [enabledAddon 启用插件]
     * @return [type] [description]
     */
    public function enabledAddon()
    {
        if (!$this->addon) {
            $this->error = '参数错误';
            return false;
        }
        if (!$this->where(array('name' => $this->addon))->find()) {
            $this->error = '插件不存在';
            return false;
        }
        return $this->where(array('name' => $this->addon))->save(array('status' => 1));
    }

    /**
     * 获得插件对象
     * @param $addon 插件名
     * @return mixed;
     */
    public function getAddonObj($addon)
    {
        $classFile = APP_ADDON_PATH . $addon . '/' . $addon . 'Addon.class.php';
        if (!is_file($classFile)) return false;
        require_cache($classFile);
        $class = $addon . 'Addon'; //类名
        if (!class_exists($class)) {
            return false;
        }
        return new $class;
    }

    /**
     * [installAddon 安装插件]
     * @return [type] [description]
     */
    public function installAddon()
    {
        $addon = Q('addon');
        if (!$addon) {
            $this->error = '参数错误';
        }
        if ($this->where(array('name' => $addon))->find()) {
            $this->error = '插件已经安装过';
            return false;
        }
        //获得插件对象
        $addonObj = $this->getAddonObj($addon);
        if (!$addonObj) {
            $this->error = '获取插件对象失败';
            return false;
        }
        $info = $addonObj->info;
        if (!$info) {
            $this->error = '获取插件信息失败';
            return false;
        }
        $data = $info;
        if (!$addonObj->install()) {
            if ($addonObj->error) {
                $this->error = $addonObj->error;
            } else {
                $this->error = '执行插件预安装失败';
            }
            return false;
        }
        $data['create_time'] = time(); //安装时间
        $data['config'] = serialize($addonObj->getConfig());
        if ($this->create($data)) {
            if (!$this->add()) {
                $this->error = '写入数据失败';
                return false;
            }
            $hooksModel = K('hooks');
            if (!$hooksModel->updateHooks($addon)) {
                $this->error = '更新钩子失败';
                return false;
            }
            //有后台菜单时，更新后台菜单
            if ($info['has_adminlist']) {
                $this->addAdminMenu($addon);
            }
            //更新缓存
            $this->updateAddonCache();
        }
        return true;
    }

    /**
     * [uninstallAddon 卸载插件]
     * @return [type] [description]
     */
    public function uninstallAddon()
    {
        $addon = Q('addon');
        if (!$addon) {
            $this->error = '参数错误';
        }
        if (!$this->where(array('name' => $addon))->find()) {
            $this->error = '插件不存在';
            return false;
        }
        //获得插件对象
        $addonObj = $this->getAddonObj($addon);
        if (!$addonObj) {
            $this->error = '获取插件对象失败';
            return false;
        }
        if (!$addonObj->uninstall()) {
            $this->error = '执行插件预卸载失败';
            return false;
        }
        //删除Hook表记录
        if (!K('Hooks')->removeHooks($addon)) {
            $this->error = '移除钩子失败';
            return false;
        }
        //删除Addon表记录
        if (!$this->where(array('name' => $addon))->del()) {
            $this->error = '删除插件失败';
            return false;
        }
        //有后台菜单时，更新后台菜单
        $this->delAdminMenu($addon);
        //更新缓存
        $this->updateAddonCache();
        return true;
    }

    /**
     * [addAdminMenu 添加后台菜单]
     * @param [type] $addon_name [description]
     */
    public function addAdminMenu($addon_name)
    {
        $addon = $this->where(array('name' => $addon_name))->find();
        $data = array(
            'pid' => 50,
            'title' => $addon['title'],
            'group' => 'Addons',
            'module' => $addon_name,
            'controller' => 'Admin',
            'action' => 'index',
            'param' => '',
            'comment' => '插件' . $addon_name . '后台管理',
            'is_show' => '1',
            'type' => '1',
        );
        return m('node')->add($data);
    }


    /**
     * [addonUniqueCheck 验证插件唯一性]
     * @param  [type] $name  [description]
     * @param  [type] $value [description]
     * @param  [type] $msg   [description]
     * @param  [type] $arg   [description]
     * @return [type]        [description]
     */
    public function addonUniqueCheck($name, $value, $msg, $arg)
    {
        $name = Q('name', '');
        if (M('addons')->where("name='$name'")->find()) {
            return $msg;
        }
        else if (is_dir(APP_ADDON_PATH . $name))
        {
            return $msg;
        }
        else
        {
            return true;
        }
    }

    /**
     * [delAdminMenu 删除插件菜单]
     * @param  [type] $addon_name [description]
     * @return [type]             [description]
     */
    public function delAdminMenu($addon_name)
    {
        return M('node')->where("`group`='Addons' AND module='$addon_name'")->del();
    }


    /**
     * [updateAddonCache 更新缓存]
     * @return [type] [description]
     */
    public function updateAddonCache()
    {
        $addonsList = M('addons')->all();
        $addons = array();
        if ($addonsList) {
            foreach ($addonsList as $addon) {
                $addons[$addon['name']] = $addon;
                $addons[$addon['name']]['config'] = unserialize($addon['config']);
            }
        }
        return S('addons', $addons);
    }
}