<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 服务逻辑
 */
class Service extends AdminBase
{

    // 对象实例
    protected static $instance = [];


    /**
     * 构造方法
     */
    public function __construct()
    {

        parent::__construct();

    }

    /**
     * 获取驱动信息
     */
    public function getDriverInfo($where = [], $field = true)
    {

        return $this->setname('Driver')->getDataInfo($where, $field);
    }

    /**
     * 驱动安装
     */
    public function driverInstall($data = [])
    {

        $where['service_name'] = $data['service_name'];
        $where['driver_name']  = $data['driver_name'];

        $info = $this->setname('Driver')->getDataInfo($where, true, '', false);

        $info['config']       = serialize($data['param']);
        $info['service_name'] = $data['service_name'];
        $info['driver_name']  = $data['driver_name'];

        $url = es_url('service/servicelist', ['service_name' => $data['service_name']]);

        if (empty($info['id'])) {
            return $this->setname('Driver')->dataAdd($info, false, $url, '操作成功');
        } else {
            return $this->setname('Driver')->dataEdit($info, ['id' => $info['id']], false, $url, '操作成功');
        }


    }

    /**
     * 驱动卸载
     */
    public function driverUninstall($data = [])
    {

        $where['service_name'] = $data['service_class'];
        $where['driver_name']  = $data['driver_class'];

        return $this->setname('Driver')->dataDel($where, '操作成功', true);
    }

    /**
     * 获取服务 or 驱动列表
     */
    public function getServiceList($service_name)
    {

        $object_list = $this->getObjectList($service_name);

        $list = [];

        foreach ($object_list as $object) {

            if (is_null($service_name)) {

                $info = $object->serviceInfo();
            } else {

                $info = $object->driverInfo();

                $dv_info = $this->setname('Driver')->getDataInfo(['driver_name' => $info['driver_class']]);

                empty($dv_info) ? $info['is_install'] = DATA_DISABLE : $info['is_install'] = DATA_NORMAL;
            }

            $list[] = $info;
        }

        return $list;
    }

    /**
     * 获取对象列表
     */
    public function getObjectList($service_name)
    {

        if (is_null($service_name)) {

            $file_list = file_list(PATH_SERVICE);

            $object_path = "\\" . SYS_APP_NAMESPACE . "\\" . SYS_COMMON_DIR_NAME . "\\" . LAYER_SERVICE_NAME;

        } else {

            $driver_name = strtolower($service_name);

            $file_list = file_list(PATH_SERVICE . $driver_name . DS . 'driver');

            $object_path = "\\" . SYS_APP_NAMESPACE . "\\" . SYS_COMMON_DIR_NAME . "\\" . LAYER_SERVICE_NAME . "\\$driver_name\\driver";

        }

        foreach ($file_list as $v) {

            $class_name = str_replace('.php', '', $v);

            if (false === strpos($class_name, 'Base') && !isset(self::$instance[$class_name])) {

                $class = $object_path . '\\' . $class_name;

                self::$instance[$class_name] = new $class();
            }
        }

        return self::$instance;
    }

}
