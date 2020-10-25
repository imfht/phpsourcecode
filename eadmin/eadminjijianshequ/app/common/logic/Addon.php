<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;


/**
 * 插件逻辑
 */
class Addon extends Common
{

    // 插件实例
    protected static $instance = [];

    /**
     * 获取插件列表
     */
    public function getAddonList()
    {

        $object_list = $this->getUninstalledList();

        $list = [];


        foreach ($object_list as $object) {

            $addon_info = $object->addonInfo();

            $addon_info['has_config'] = is_file(PATH_ADDON . strtolower($addon_info['name']) . '/' . 'config.php') ? DATA_NORMAL : DATA_DISABLE;

            $info = $this->getDataInfo(['name' => $addon_info['name']]);

            $addon_info['is_install'] = empty($info) ? DATA_DISABLE : DATA_NORMAL;

            $list[] = $addon_info;
        }

        return $list;
    }

    /**
     * 获取未安装插件列表
     */
    public function getUninstalledList()
    {

        $dir_list = get_dir(PATH_ADDON);

        foreach ($dir_list as $v) {

            $class = "\\" . SYS_ADDON_DIR_NAME . "\\$v\\" . ucfirst($v);

            if (!isset(self::$instance[$class])) : self::$instance[$class] = new $class(); endif;
        }

        return self::$instance;
    }

    /**
     * 获取插件数据列表
     */
    public function getAdminList($name, $map)
    {
        $class = get_addon_class($name);

        if (!class_exists($class)) {
            $this->error('插件不存在');
        }

        $addon = new $class();

        $param = $addon->admin_list;

        if (!$param) {
            $this->error('插件列表信息不正确');
        }


        //$this->assign('addon', $addon);
        //$this->assign('title', $addon->addonInfo['title']);
        //$this->assign($param);


        if (!isset($map))
            $map = [];

        if ($name == 'Alipay') {

            !empty(input('request.id')) && $map['id'] = input('request.id');
            !empty(input('request.trade_no')) && $map['trade_no'] = input('request.trade_no');

        }

        /*      $keyword=$this->request->param('keyword');


                    if ($keyword!='') {
                        $map['title'] = ['like', "%{$keyword}%"];

                    }

                    $this->assign('keyword',$keyword);*/

        $model = strtolower($param['model']);
        $class = get_addon_model($name, $model);

        if (class_exists($class)) {
            $model = new $class();


            $ref = new \ReflectionClass($model);

            $funarr = $ref->getMethods();

            $attr_arr = [];

            if ($param['join'] && $param['group']) {
                $list = $model->getDataList($map, $param['field'], $param['order'], 0, $param['join'], $param['group']);
            } else {

                if ($param['join']) {
                    $list = $model->getDataList($map, $param['field'], $param['order'], 0, $param['join']);
                } elseif ($param['group']) {
                    $list = $model->getDataList($map, $param['field'], $param['order'], 0, '', $param['group']);
                } else {
                    $list = $model->getDataList($map, $param['field'], $param['order']);
                }

            }


            foreach ($funarr as $k => $v) {

                $name = $v->name;

                if ($v->class == $class && preg_match('/^get(.*?)Attr/', $name, $result) > 0) {


                    $attrname = camelcase2underline($result[1]);

                    $attr_field = object_to_array($v->getParameters());

                    $attr_field = $attr_field[0]['name'];


                    foreach ($list['data'] as $key => $vo) {

                        $list['data'][$key][$attrname] = $model->$name($vo[$attr_field]);


                    }


                }

            }
        } else {
            $list = [];
        }


        extract($param);
        $data['param'] = $param['listKey'];
        $data['list']  = $list;
        if ($addon->custom_adminlist) {
            $data['custom_adminlist'] = $addon->addon_path . $addon->custom_adminlist;
        } else {
            $data['custom_adminlist'] = '';
        }

        return $data;

    }

    /**
     * 设置插件配置
     */
    public function setAddonConfig($param)
    {
        $config          = json_encode($param['config']);
        $param['config'] = $config;

        return $this->dataEdit($param, ['id' => $param['id']], false) ? [RESULT_SUCCESS, '配置设置成功'] : [RESULT_SUCCESS, '设置未更改或者配置失败'];


    }

    /**
     * 获取插件配置
     */
    public function getAddonConfig($name)
    {


        $addon       = $this->getDataInfo(['name' => $name]);//插件信息
        $addon_class = get_addon_class($addon['name']);
        $data        = new $addon_class;


        $addon['addon_path']    = $data->addon_path;
        $addon['custom_config'] = $data->custom_config;

        $db_config = $addon['config'];

        $config = include $data->config_file;

        if ($db_config) {
            $db_config = json_decode($db_config, true);

            foreach ($config as $key => $value) {
                if ($value['type'] != 'group') {
                    $config[$key]['value'] = $db_config[$key];
                } else {
                    foreach ($value['options'] as $gourp => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            $config[$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                        }
                    }
                }


            }
        }
        $addon['config'] = $config;

        return $addon;

    }

    /**
     * 获取钩子列表
     */
    public function getHookList($where = [], $field = true, $order = '')
    {

        return $this->setname(ucwords(SYS_HOOK_DIR_NAME))->getDataList($where, $field, $order);
    }

    /**
     * 执行插件sql
     */
    public function executeSql($name = '', $sql_name = '')
    {
        if (file_exists(PATH_ADDON . $name . DS . 'data' . DS . $sql_name . '.sql')) {
            $sql_string = file_get_contents(PATH_ADDON . $name . DS . 'data' . DS . $sql_name . '.sql');


            $sql_string = str_replace("\r", "\n", $sql_string);
            $sql_string = str_replace('es_', DB_PREFIX, $sql_string);
            $charset[1] = substr($sql_string, 0, 1);
            $charset[2] = substr($sql_string, 1, 1);
            $charset[3] = substr($sql_string, 2, 1);

            if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
                $sql_string = substr($sql_string, 3);

            }
            $sql = explode(";\n", $sql_string);


            foreach ($sql as $value) {
                if (!empty(trim($value))) {

                    $this->query($value);

                }
            }


        }


    }


}
