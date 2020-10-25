<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\logic\Common as LogicCommon;

/**
 * 插件控制器基类
 */
class AddonBase extends ControllerBase
{
    public $addon_path = '';
    protected $param;
    public $config_file = '';
    public $custom_config = '';
    public $admin_list = [];
    public $custom_adminlist = '';
    // 配置逻辑
    protected $commonLogic = null;

    /**
     * 插件基类构造方法
     */
    public function _initialize()
    {
        // 执行父类构造方法
        $this->param      = $this->request->param();
        $this->addon_path = PATH_ADDON . $this->getName() . DS;
        if (is_file($this->addon_path . 'config.php')) {
            $this->config_file = $this->addon_path . 'config.php';
        }
        $this->commonLogic = get_sington_object('commonLogic', LogicCommon::class);

    }

    public function getName()
    {
        $class = get_class($this);
        $str   = substr($class, strrpos($class, '\\') + 1);
        return strtolower($str);
    }

    /**
     * 插件模板渲染
     */
    public function addonTemplate($template_name = '', $module = '')
    {

        $class = get_class($this);

        $addon_name = strtolower(substr($class, DATA_NORMAL + strrpos($class, '\\')));

        $static_path = SYS_DSS . SYS_ADDON_DIR_NAME . SYS_DSS . $addon_name . SYS_DSS . 'static' . SYS_DSS;

        $this->assign('static_path', WEB_URL . $static_path);

        if (empty($module)) {

            $module = MODULE_NAME;
        }

        $this->fetch(SYS_ADDON_DIR_NAME . '@' . $module . '@' . $addon_name . '@' . $template_name);
    }

    /**
     * 获取插件所需的钩子是否存在，没有则新增
     *
     * @param string $str    钩子名称
     * @param string $addons 插件名称
     * @param string $addons 插件简介
     */
    public function getisHook($str, $addons, $msg = '')
    {

        $where['name'] = $str;
        $gethook       = $this->commonLogic->setname('hook')->getDataInfo($where);
        if (!$gethook || empty($gethook) || !is_array($gethook)) {
            $data['name']        = $str;
            $data['describe']    = $msg;
            $data['status']      = 1;
            $data['create_time'] = time();
            $data['update_time'] = time();
            $data['addon_list']  = $addons;
            if (false !== $this->commonLogic->setname('hook')->dataAdd($data, false)) {

            }
        }
    }

    /**
     * 删除钩子
     *
     * @param string $hook 钩子名称
     */
    public function deleteHook($hook)
    {

        $condition = [
            'name' => $hook,
        ];
        $this->commonLogic->setname('hook')->dataDel($condition, '删除钩子成功', true);
    }

    /**
     * 新增插件信息
     */
    public function installAddon($info)
    {

        $where['name'] = $info['name'];
        $gethook       = $this->commonLogic->setname('addon')->getDataInfo($where);

        if (!$gethook || empty($gethook) || !is_array($gethook)) {
            $data['name']          = $info['name'];
            $data['title']         = $info['title'];
            $data['describe']      = $info['describe'];
            $data['status']        = 1;
            $data['config']        = '';
            $data['create_time']   = time();
            $data['update_time']   = time();
            $data['author']        = $info['author'];
            $data['version']       = $info['version'];
            $data['has_adminlist'] = $info['has_adminlist'];

            if (false !== $this->commonLogic->setname('addon')->dataAdd($data, false)) {

            }
        }
    }

    /**
     * 删除插件
     *
     * @param string $hook 插件名称
     */
    public function uninstallAddon($name)
    {

        $condition = [
            'name' => $name,
        ];
        $this->commonLogic->setname('addon')->dataDel($condition, '删除插件成功', true);
    }

    /**
     * 获取插件的配置数组
     */
    public function getConfig($name = '')
    {
        static $_config = [];
        if (empty($name)) {
            $name = $this->getName();
        }

        if (isset($_config[$name])) {
            return $_config[$name];
        }
        $config        = [];
        $map['name']   = $name;
        $map['status'] = 1;
        $config        = $this->commonLogic->setname('addon')->getDataValue($map, 'config');


        if ($config) {
            $config = json_decode($config, true);
        } else {

            if (file_exists($this->config_file)) {
                $temp_arr = include $this->config_file;

                foreach ($temp_arr as $key => $value) {
                    if ($value['type'] == 'group') {
                        foreach ($value['options'] as $gkey => $gvalue) {
                            foreach ($gvalue['options'] as $ikey => $ivalue) {
                                $config[$ikey] = $ivalue['value'];
                            }
                        }
                    } else {
                        $config[$key] = $temp_arr[$key]['value'];
                    }
                }
            }


        }
        $_config[$name] = $config;
        return $config;
    }


}
