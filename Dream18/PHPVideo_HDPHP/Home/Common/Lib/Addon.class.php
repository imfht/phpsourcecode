<?php

/**
 * 插件基类
 * Class Addon
 */
abstract class Addon
{
    public $addonName; //插件名
    public $addonPath; //插件目录
    public $configFile; //插件配置文件
    public $error;//插件错误信息
    protected $view;//视图

    public function __construct()
    {
        $this->addonName = $this->getAddonName();
        $this->addonPath = APP_ADDON_PATH . $this->addonName . '/';
        $this->configFile = $this->addonPath . 'config.php';
        $this->view = ViewFactory::factory();
    }

    /**
     * 获得配置项
     * @return array|mixed
     */
    public function getConfig()
    {
        $config = array();
        if ($data = M('addons')->where(array('name' => $this->addonName))->find()) {
            $config = unserialize($data['config']);
        }
        if (empty($data)) {
            if (is_file($this->configFile)) {
                $data = require $this->configFile;
                $config = array();
                foreach ($data as $name => $v) {
                    $config[$name] = $v['value'];
                }
            }
        }
        return $config;
    }

    //获得插件名
    final public function getAddonName()
    {
        $class = get_class($this);
        return substr($class, 0, strrpos($class, 'Addon'));
    }

    /**
     * 分配变量
     * @access protected
     * @param mixed $name 变量名
     * @param mixed $value 变量值
     * @return mixed
     */
    protected function assign($name, $value = null)
    {
        return $this->view->assign($name, $value);
    }

    /**
     * 显示视图
     * @return mixed
     */
    final protected function display($tplFile = null)
    {
        //执行视图对象中的display同名方法
        echo $this->fetch($tplFile);
    }

    /**
     * 获得视图显示内容
     */
    final protected function fetch($tplFile = null)
    {
        if (is_null($tplFile)) {
            $tplFile = $this->addonName;
        }
        if (!is_file($tplFile)) {
            $tplFile = $this->addonPath . $tplFile . C('TPL_FIX');
        }
        if (!is_file($tplFile)) {
            return false;
        }
        return $this->view->fetch($tplFile);
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}