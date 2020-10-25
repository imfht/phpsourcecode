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

namespace app\common\model;

use think\Model;
use think\facade\Env;
use ZipArchive;
use app\common\model\HookAddon as HookAddonModel;
use app\common\model\Common as CommonModel;

/**
 * 插件模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class Addon extends Model
{
    protected $autoWriteTimestamp = true;
    // 设置json类型字段
    protected $json = ['config', 'admin_actions'];

    /**
     * 获取所有插件信息
     * @return array|bool
     */
    public function getAll()
    {
        $result = cache('addon_all');
        if (!$result) {
            // 获取插件目录下的所有插件目录
            $dirs = array_map('basename', glob(config('addon_path') . '*', GLOB_ONLYDIR));
            if ($dirs === false || !file_exists(config('addon_path'))) {
                $this->error = '插件目录不可读或者不存在';
                return false;
            }
            $dirs = array_map('ucfirst', $dirs);
            // 读取数据库插件表
            $addons = self::order('sort asc,id desc')->column('*', 'name');
            // 读取未安装的插件
            $default = ['qq'=>'', 'url'=>'', 'demourl'=>'', 'category_id'=>1];
            foreach ($dirs as $addon) {
                if (!isset($addons[$addon])) {
                    $addons[$addon]['name'] = $addon;
                    // 获取插件类名
                    $class = get_addon_class($addon);
                    // 插件类不存在则跳过实例化
                    if (!class_exists($class)) {
                        $addons[$addon]['status'] = '-2';
                        continue;
                    }
                    // 实例化插件
                    $obj = new $class;
                    // 插件插件信息缺失
                    if (!isset($obj->info) || empty($obj->info) || !$obj->checkInfo()) {
                        // 插件信息缺失！
                        $addons[$addon]['status'] = '-2';
                        continue;
                    }
                    // 插件未安装
                    $addons[$addon]           = array_merge($default, $obj->info);
                    $addons[$addon]['status'] = '-1';
                }
            }
            //获取插件市场数据
            $data_online = self::getAddonsOnlines(false);
            $addons_online = $data_online['lists'];
            $categorys = $data_online['categorys'];
            // 处理插件按钮
            foreach ($addons as &$addon) {
                switch ($addon['status']) {
                    case '-2':
                        $addon['actions'] = '<button class="btn btn-xs btn-inverse" type="button" disabled>不可操作</button>';
                        break;
                    // 未安装
                    case '-1':
                        $addon['actions'] = '';
                        //是否可以升级
                        if (isset($addons_online[$addon['name']]) && version_compare($addons_online[$addon['name']]['version'], $addon['version'], 'gt')) {
                            $addon['actions']       .= '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Addons/upgrade', ['name' => $addon['name'], 'version'=>$addon['version']]) . '">升级</a> ';
                        }
                        $addon['actions'] .= '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Addons/install', ['name' => $addon['name']]) . '">安装</a>';
                        break;
                    // 禁用
                    case '0':
                        $addon['actions'] = '';
                        //是否可以升级
                        if (isset($addons_online[$addon['name']]) && version_compare($addons_online[$addon['name']]['version'], $addon['version'], 'gt')) {
                            $addon['actions']       .= '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Addons/upgrade', ['name' => $addon['name'], 'version'=>$addon['version']]) . '">升级</a> ';
                        }
                        $addon['admin_actions'] = json_decode($addon['admin_actions'], true);
                        $addon['actions']       .= '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Addons/enable', ['id' => $addon['id']]) . '">启用</a> ';
                        $addon['actions'] .= '<a class="btn btn-xs btn-danger confirm-rst-url-btn" data-info="如果包括数据表，将同时删除数据表！确认?" href="' . url('uninstall', ['name' => $addon['name']]) . '">卸载</a> ';
                        if (isset($addon['config']) && $addon['config'] != '' && isset($addon['admin_actions']['config']) && $addon['admin_actions']['config']) {
                            if (count($addon['admin_actions']['config']) == 2) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['config'][0], $addon['name'] . '://' . $addon['admin_actions']['config'][1]);
                            } elseif (count($addon['admin_actions']['config']) == 1) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['config'][0]);
                            } else {
                                $url = '';
                            }
                            if ($url) {
                                $addon['actions'] .= '<a class="btn btn-xs btn-info yf-modal-open" data-title="' . $addon['title'] . ' 设置" data-url="' . $url . '">设置</a> ';
                            }
                        }
                        if ($addon['admin'] != '0' && isset($addon['admin_actions']['index']) && $addon['admin_actions']['index']) {
                            if (count($addon['admin_actions']['index']) == 2) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['index'][0], $addon['name'] . '://' . $addon['admin_actions']['index'][1]);
                            } elseif (count($addon['admin_actions']['index']) == 1) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['index'][0]);
                            } else {
                                $url = '';
                            }
                            $addon['actions'] .= '<a class="btn btn-xs btn-primary yf-modal-open" data-title="' . $addon['title'] . ' 管理" data-url="' . $url . '">管理</a> ';
                        }
                        break;
                    // 启用
                    case '1':
                        $addon['actions'] = '';
                        //是否可以升级
                        if (isset($addons_online[$addon['name']]) && version_compare($addons_online[$addon['name']]['version'], $addon['version'], 'gt')) {
                            $addon['actions']       .= '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Addons/upgrade', ['name' => $addon['name'], 'version'=>$addon['version']]) . '">升级</a> ';
                        }
                        $addon['admin_actions'] = json_decode($addon['admin_actions'], true);
                        $addon['actions']       .= '<a class="btn btn-xs btn-yellow rst-url-btn" href="' . url('admin/Addons/disable', ['id' => $addon['id']]) . '">禁用</a> ';
                        $addon['actions'] .= '<a class="btn btn-xs btn-danger confirm-rst-url-btn" data-info="如果包括数据库，将同时删除数据库！确认?" href="' . url('uninstall', ['name' => $addon['name']]) . '">卸载</a> ';
                        if (isset($addon['config']) && $addon['config'] != '' && $addon['admin_actions']['config']) {
                            if (count($addon['admin_actions']['config']) == 2) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['config'][0], $addon['name'] . '://' . $addon['admin_actions']['config'][1]);
                            } elseif (count($addon['admin_actions']['config']) == 1) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['config'][0]);
                            } else {
                                $url = '';
                            }
                            if ($url) {
                                $addon['actions'] .= '<a class="btn btn-xs btn-info yf-modal-open" data-title="' . $addon['title'] . ' 设置" data-url="' . $url . '">设置</a> ';
                            }
                        }
                        if ($addon['admin'] != '0' && $addon['admin_actions']['index']) {
                            if (count($addon['admin_actions']['index']) == 2) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['index'][0], $addon['name'] . '://' . $addon['admin_actions']['index'][1]);
                            } elseif (count($addon['admin_actions']['index']) == 1) {
                                $url = addon_url($addon['name'] . '://' . $addon['admin_actions']['index'][0]);
                            } else {
                                $url = '';
                            }
                            $addon['actions'] .= '<a class="btn btn-xs btn-primary yf-modal-open" data-title="' . $addon['title'] . ' 管理" data-url="' . $url . '">管理</a> ';
                        }
                        break;
                    // 未知
                    default:
                        $addon['actions'] = '';
                        break;
                }
                if($addon['status']!=-2) {
                    //title字段添加链接
                    if ($addon['url']) {
                        $addon['title'] = '<a class="title" href="'.$addon['url'].'" data-toggle="tooltip" title="" target="_blank" data-original-title="查看插件介绍和帮助">'.$addon['title'].'</a>';
                    }
                    //author添加qq链接
                    if ($addon['qq']) {
                        $addon['author'] = '<a href="//wpa.qq.com/msgrd?v=3&uin='.$addon['qq'].'&site=yfcmf.net&menu=yes" target="_blank" data-toggle="tooltip" title="" class="text-primary" data-original-title="点击与插件开发者取得联系">'.$addon['author'].'</a>';
                    }
                    //分类
                    $addon['category'] = isset($categorys[$addon['category_id']]) ? $categorys[$addon['category_id']] : '其它';
                }
            }
            cache('addon_all', $addons);
            return $addons;
        }
        return $result;
    }

    /**
     * 设置插件配置
     *
     * @param string $addon_name 插件名.配置名
     * @param string $value      配置值
     *
     * @return bool
     */
    public function setConfig($addon_name = '', $value = '')
    {
        $item = '';
        if (strpos($addon_name, '.')) {
            list($addon_name, $item) = explode('.', $addon_name);
        }
        $config = cache('addon_config_' . $addon_name);
        if (!$config) {
            $config = self::where('name', $addon_name)->value('config');
            if (!$config) {
                return false;
            }
            $config = json_decode($config, true);
        }
        if ($item === '') {
            // 批量更新
            if (!is_array($value) || empty($value)) {
                // 值的格式错误，必须为数组
                return false;
            }
            $config = array_merge($config, $value);
        } else {
            // 更新单个值
            $config[$item] = $value;
        }
        if (false === self::where('name', $addon_name)->setField('config', $config)) {
            return false;
        }
        //更新缓存
        cache('addon_config_' . $addon_name, $config);
        return true;
    }

    /**
     * 获取插件市场数据
     * @param bool $filter 过滤本地
     * @return array
     */
    public static function getAddonsOnlines($filter = true)
    {
        $options = [
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'X-REQUESTED-WITH: XMLHttpRequest'
            ]
        ];
        $addons_online = [];
        $categorys = [];
        $ret = \Http::sendRequest(config('yfcmf.api_addon.url') . '/lists', ['type' => 1], 'GET', $options);
        if ($ret['ret']) {
            if (substr($ret['msg'], 0, 1) == '{') {
                $json = (array) json_decode($ret['msg'], true);
                $addons_online = $json['data'];
                $categorys = $json['categorys'];
            }
        }
        if ($addons_online && $categorys) {
            foreach ($addons_online as $key => &$value) {
                if ($filter) {
                    $addon_name = $key;
                    $addon_class = get_addon_class($addon_name);
                    if (!class_exists($addon_class)) {
                        $addons_online[$key]['actions'] = '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Addons/installOnline', ['name' => $addon_name, 'version'=>$value['version']]) . '">安装</a>';
                        //title字段添加链接
                        if ($value['url']) {
                            $value['title'] = '<a class="title" href="'.$value['url'].'" data-toggle="tooltip" title="" target="_blank" data-original-title="查看插件介绍和帮助">'.$value['title'].'</a>';
                        }
                        //author添加qq链接
                        if ($value['qq']) {
                            $value['author'] = '<a href="//wpa.qq.com/msgrd?v=3&uin='.$value['qq'].'&site=yfcmf.net&menu=yes" target="_blank" data-toggle="tooltip" title="" class="text-primary" data-original-title="点击与插件开发者取得联系">'.$value['author'].'</a>';
                        }
                        //分类
                        $value['category'] = isset($categorys[$value['category_id']]) ? $categorys[$value['category_id']] : '其它';
                    } else {
                        unset($addons_online[$key]);
                    }
                } else {
                    $value['category'] = isset($categorys[$value['category_id']]) ? $categorys[$value['category_id']] : '其它';
                }
            }
        }
        return ['lists'=>$addons_online, 'categorys'=>$categorys];
    }
    /**
     * 启用插件
     * @param   int  $id   插件id
     * @return  boolean
     */
    public function enable($id)
    {
        $rst = $this->where('id', $id)->setField('status', 1);
        if ($rst !== false) {
            cache('addon_all', null);
            cache('hook_addons', null);
            return true;
        } else {
            return false;
        }
    }
    /**
     * 禁用插件
     * @param   int  $id   插件id
     * @return  boolean
     */
    public function disable($id)
    {
        $rst = $this->where('id', $id)->setField('status', 0);
        if ($rst !== false) {
            cache('addon_all', null);
            cache('hook_addons', null);
            return true;
        } else {
            return false;
        }
    }
    /**
     * 安装插件
     *
     * @param   string  $addon_name   插件名称
     * @return  boolean
     * @throws \Exception
     */
    public function install($addon_name)
    {
        if (!$addon_name) {
            exception('安装出错');
        }
        $addon_class = get_addon_class($addon_name);
        if (!class_exists($addon_class)) {
            exception('插件不存在！');
        }
        // 实例化插件
        $addon = new $addon_class();
        // 插件预安装
        if (!$addon->install()) {
            exception('插件预安装失败!');
        }

        // 添加钩子
        $hook_addon_model = new HookAddonModel();
        if (isset($addon->hooks) && $addon->hooks) {
            if (!$hook_addon_model->addHooks($addon->hooks, $addon_name)) {
                exception('安装插件钩子时出现错误');
            }
            cache('hook_addons', null);
        }
        // 执行安装插件sql文件
        $sql_file = realpath(Env::get('runtime_path') . 'addons' . DIRECTORY_SEPARATOR . strtolower($addon_name) . '/install.sql');
        if (file_exists($sql_file)) {
            if (isset($addon->database_prefix) && $addon->database_prefix != '') {
                db_restore_file($sql_file, $addon->database_prefix);
            } else {
                db_restore_file($sql_file);
            }
        }
        // 插件配置信息
        $addon_info = $addon->info;
        // 并入插件配置值
        $addon_info['config'] = $addon->getConfigValue()? : [];
        //插件管理操作
        if (property_exists($addon, 'admin_actions')) {
            $addon_info['admin_actions'] = $addon->admin_actions;
        }
        // 将插件信息写入数据库
        if (self::create($addon_info)) {
            cache('addon_all', null);
            return true;
        } else {
            exception('插件安装失败');
        }
    }
    /**
     * 卸载插件
     *
     * @param   string  $addon_name   插件名称
     * @return  boolean
     * @throws \Exception
     */
    public function uninstall($addon_name)
    {
        if (!$addon_name) {
            exception('插件不存在！');
        }
        $class = get_addon_class($addon_name);
        if (!class_exists($class)) {
            exception('插件不存在！');
        }
        // 实例化插件
        $addon = new $class;
        // 插件预卸
        if (!$addon->uninstall()) {
            exception('插件预卸载失败!');
        }
        // 卸载插件自带钩子
        $hook_addon_model = new HookAddonModel();
        if (isset($addon->hooks) && $addon->hooks) {
            if (false === $hook_addon_model->deleteHooks($addon_name)) {
                exception('卸载插件钩子时出现错误');
            }
            cache('hook_addons', null);
        }
        // 执行卸载插件sql文件
        $sql_file = realpath(Env::get('runtime_path') . 'addons' . DIRECTORY_SEPARATOR . strtolower($addon_name) . '/uninstall.sql');
        if (file_exists($sql_file)) {
            if (isset($addon->database_prefix) && $addon->database_prefix != '') {
                db_restore_file($sql_file, $addon->database_prefix);
            } else {
                db_restore_file($sql_file);
            }
        }
        // 删除插件信息
        if ($this->where('name', $addon_name)->delete()) {
            cache('addon_all', null);
            return true;
        } else {
            exception('插件卸载失败');
        }
    }
    /**
     * 远程安装插件
     *
     * @param   string  $name   插件名称
     * @param   array   $params 参数
     * @return  boolean
     * @throws \Exception
     */
    public function installOnline($name, $params = [])
    {
        if (!$name) {
            exception('安装出错');
        }
        $class = get_addon_class($name);
        if (class_exists($class)) {
            exception('本地已存在此插件');
        }
        // 远程下载插件
        $tmpFile = $this->download($name, $params);
        // 解压插件
        $addonDir = $this->unzip($name);
        // 移除临时文件
        @unlink($tmpFile);
        if ($this->install($name)) {
            return true;
        } else {
            @rmdirs($addonDir);
            exception('安装错误');
        }
    }
    /**
     * 远程下载插件
     *
     * @param   string  $name   插件名称
     * @param   array   $params 扩展参数
     * @return  string
     * @throws \Exception
     */
    public static function download($name, $params = [])
    {
        $name = strtolower($name);
        $addonTmpDir = Env::get('runtime_path') . 'addons' . DIRECTORY_SEPARATOR;
        if (!is_dir($addonTmpDir)) {
            @mkdir($addonTmpDir, 0755, true);
        }
        $tmpFile = $addonTmpDir . $name . ".zip";
        $options = [
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'X-REQUESTED-WITH: XMLHttpRequest'
            ]
        ];
        $ret = \Http::sendRequest(config('yfcmf.api_addon.url') . '/download', array_merge(['name' => $name, 'type'=>2], $params), 'GET', $options);
        if ($ret['ret']) {
            if (substr($ret['msg'], 0, 1) == '{') {
                $json = (array) json_decode($ret['msg'], true);
                if ($json['data'] && isset($json['data']['url'])) {
                    array_pop($options);
                    $ret = \Http::sendRequest($json['data']['url'], [], 'GET', $options);
                    if (!$ret['ret']) {
                        exception($json['msg'], $json['code'], $json['data']);
                    }
                } else {
                    exception($json['msg'], $json['code'], $json['data']);
                }
            }
            if ($write = fopen($tmpFile, 'w')) {
                fwrite($write, $ret['msg']);
                fclose($write);
                return $tmpFile;
            }
            exception("没有权限写入临时文件");
        }
        exception("无法下载远程文件");
    }
    /**
     * 解压插件
     *
     * @param   string  $name   插件名称
     * @return  string
     * @throws \Exception
     */
    public static function unzip($name)
    {
        $name = strtolower($name);
        $file = Env::get('runtime_path') . 'addons' . DIRECTORY_SEPARATOR . $name . '.zip';
        $dir = Env::get('root_path') . 'addons' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive;
            if ($zip->open($file) !== true) {
                exception('Unable to open the zip file');
            }
            if (!$zip->extractTo($dir)) {
                $zip->close();
                exception('Unable to extract the file');
            }
            $zip->close();
            return $dir;
        }
        exception("无法执行解压操作，请确保ZipArchive安装正确");
    }
    /**
     * 升级插件
     *
     * @param   string  $name   插件名
     * @param   array   $params 扩展参数
     * @return    bool
     * @throws \Exception
     */
    public function upgrade($name, $params = [])
    {
        if (!$name) {
            exception('安装出错');
        }
        $class = get_addon_class($name);
        if (!class_exists($class)) {
            exception('本地不存在此插件');
        }
        // 读取数据库插件表
        $addons = self::where('name', $name)->find();
        $config = [];
        if (!$addons || $addons['status']==0) {
            //未安装或禁用中 实例化插件
            $addon = new $class;
            $config = $addon->getConfigValue();
        } else {
            //使用中
            exception('请先禁用插件');
        }
        // 远程下载插件
        $tmpFile = $this->download($name, $params);
        // 解压插件
        $this->unzip($name);
        // 移除临时文件
        @unlink($tmpFile);
        if (!$addons) {
            //安装
            $this->install($name);
        } elseif ($addons['status']==0) {
            if ($config) {
                // 还原配置
                $this->setConfig($name, $config);
            }
            // 执行升级sql文件
            $sql_file = realpath(Env::get('runtime_path') . 'addons' . DIRECTORY_SEPARATOR . strtolower($name) . '/update.sql');
            if (file_exists($sql_file)) {
                if (isset($addon->database_prefix) && $addon->database_prefix != '') {
                    db_restore_file($sql_file, $addon->database_prefix);
                } else {
                    db_restore_file($sql_file);
                }
            }
            //更新插件版本
            $this->where('name', $name)->update(['version'=>$params['version'], 'update_time'=>time()]);
        }
        return true;
    }
}
