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
use app\common\model\Addon as AddonModel;
use app\admin\model\AuthRule as AuthRuleModel;
use think\Db;

/**
 * 模块模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class Module extends Model
{
    protected $autoWriteTimestamp = true;
    protected $json = ['config'];
    protected $except_module = ['common', 'admin', 'home', 'api', 'install', 'cms', 'user'];

    /**
     * 获取所有模块的名称和标题
     * @author rainfer <rainfer520@qq.com>
     * @return mixed
     */
    public function getModule()
    {
        $modules = cache('modules');
        if (!$modules) {
            $modules = self::where('status', '>=', 0)->order('id')->column('name,title');
            cache('modules', $modules);
        }
        return $modules;
    }
    /**
     * 获取系统模块
     * @author rainfer <rainfer520@qq.com>
     * @return mixed
     */
    public function getSysModules()
    {
        $modules = cache('sys_modules');
        $map = [
            ['status', '>=', 0],
            ['system_module', '=', 1]
        ];
        if (!$modules) {
            $modules = self::where($map)->order('id')->column('*', 'name');
            cache('sys_modules', $modules);
        }
        return $modules;
    }
    /**
     * 从文件获取模块菜单
     * @param string $name 模块名称
     * @author rainfer <rainfer520@qq.com>
     * @return array|mixed
     */
    public static function getMenusFromFile($name = '')
    {
        $menus = [];
        if ($name != '' && is_file(Env::get('app_path') . $name . '/menus.php')) {
            // 从菜单文件获取
            $menus = include Env::get('app_path') . $name . '/menus.php';
        }
        return $menus;
    }
    /**
     * 从配置文件获取模块信息
     * @param string $name 模块名称
     * @author rainfer <rainfer520@qq.com>
     * @return array|mixed
     */
    public static function getInfoFromFile($name = '')
    {
        $info = [];
        if ($name != '') {
            if (is_file(Env::get('app_path') . $name . '/info.php')) {
                $info = include Env::get('app_path') . $name . '/info.php';
            }
        }
        return $info;
    }
    /**
     * 检查模块信息是否完整(必要的信息)
     * @param array $info 模块模块信息
     * @author rainfer <rainfer520@qq.com>
     * @return bool
     */
    private function checkInfo($info)
    {
        $default_item = ['name', 'title', 'author', 'version', 'nav_url'];
        foreach ($default_item as $item) {
            if (!isset($info[$item]) || $info[$item] == '') {
                return false;
            }
        }
        return true;
    }
    /**
     * 获取模块配置信息
     * @param string $name 模型名
     * @param string $item 指定返回的模块配置项,多个用,隔开
     * @author rainfer <rainfer520@qq.com>
     * @return mixed
     */
    public function getConfig($name = '', $item = '')
    {
        $name = $name == '' ? request()->module() : $name;
        $config = cache('module_config_'.$name);
        if (!$config) {
            $config = self::where('name', $name)->value('config');
            if (!$config) {
                return [];
            }
            $config = json_decode($config, true);
            cache('module_config_'.$name, $config);
        }
        if (!empty($item)) {
            $items = explode(',', $item);
            if (count($items) == 1) {
                return isset($config[$item]) ? $config[$item] : '';
            }
            $result = [];
            foreach ($items as $item) {
                $result[$item] = isset($config[$item]) ? $config[$item] : '';
            }
            return $result;
        }
        return $config;
    }
    /**
     * 设置模块配置信息
     * @param string $name 模块名.配置名
     * @param string $value 配置值
     * @author rainfer <rainfer520@qq.com>
     * @return bool
     */
    public function setConfig($name = '', $value = '')
    {
        $item = '';
        if (strpos($name, '.')) {
            list($name, $item) = explode('.', $name);
        }
        // 获取缓存
        $config = cache('module_config_'.$name);
        if (!$config) {
            $config = self::where('name', $name)->value('config');
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
        if (false === self::where('name', $name)->setField('config', $config)) {
            return false;
        }
        cache('module_config_'.$name, $config);
        return true;
    }
    /**
     * 获取本地所有模块信息
     * @author rainfer <rainfer520@qq.com>
     * @return array|bool
     */
    public function getAll()
    {
        $result = cache('module_all');
        if (!$result) {
            $dirs = array_map('basename', glob(Env::get('app_path') .'*', GLOB_ONLYDIR));
            if ($dirs === false || !file_exists(Env::get('app_path'))) {
                $this->error = '模块目录不可读或者不存在';
                return false;
            }
            // 不读取模块信息的目录
            $except_module = $this->except_module ? : [];
            // 正常模块(包括已安装和未安装)
            $dirs = array_diff($dirs, $except_module);
            // 读取数据库模块表
            $modules = $this->order('sort asc,id desc')->column('*', 'name');
            // 读取未安装的模块
            foreach ($dirs as $module) {
                //未安装
                if (!isset($modules[$module])) {
                    // 获取模块信息
                    $info = self::getInfoFromFile($module);

                    $modules[$module]['name'] = $module;

                    // 模块信息缺失
                    if (empty($info) || !$this->checkInfo($info)) {
                        $modules[$module]['status'] = '-2';
                        continue;
                    }

                    // 模块未安装
                    $modules[$module] = $info;
                    $modules[$module]['status'] = '-1'; // 模块未安装
                }
            }
            //获取模块市场数据
            $modules_online = self::getModulesOnlines(false);
            // 处理状态及模块按钮
            foreach ($modules as &$module) {
                // 系统核心模块
                if (isset($module['system_module']) && $module['system_module'] == '1') {
                    $module['actions'] = '<button class="btn btn-sm btn-noborder btn-danger" type="button" disabled>不可操作(系统模块)</button>';
                    continue;
                }
                switch ($module['status']) {
                    case '-2': // 模块信息缺失
                        $module['actions'] = '<button class="btn btn-sm btn-noborder btn-danger" type="button" disabled>不可操作(损坏)</button>';
                        break;
                    case '-1': // 未安装
                        $module['actions'] = '';
                        //是否可以升级
                        if (isset($modules_online[$module['name']]) && version_compare($modules_online[$module['name']]['version'], $module['version'], 'gt')) {
                            $module['actions'] .= '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Modules/upgrade', ['name' => $module['name'], 'version'=>$module['version']]) . '">升级</a> ';
                        }
                        $module['actions'] .= '<a class="btn btn-sm btn-noborder btn-success rst-url-btn" href="'.url('admin/Modules/install', ['name' => $module['name']]).'">安装</a>';
                        break;
                    case '0': // 禁用
                        $module['actions'] = '';
                        //是否可以升级
                        if (isset($modules_online[$module['name']]) && version_compare($modules_online[$module['name']]['version'], $module['version'], 'gt')) {
                            $module['actions'] .= '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Modules/upgrade', ['name' => $module['name'], 'version'=>$module['version']]) . '">升级</a> ';
                        }
                        $module['actions'] .= '<a class="btn btn-sm btn-noborder btn-success rst-url-btn" href="'.url('admin/Modules/enable', ['id' => $module['id']]).'">启用</a> ';
                        $module['actions'] .= '<a class="btn btn-sm btn-noborder btn-danger confirm-rst-url-btn" data-info="如果包括数据表，将同时删除数据表！确认?" href="'.url('admin/Modules/uninstall', ['name' => $module['name']]).'">卸载</a> ';
                        break;
                    case '1': // 启用
                        $module['actions'] = '<a class="btn btn-sm btn-noborder btn-warning rst-url-btn" href="'.url('admin/Modules/disable', ['id' => $module['id']]).'">禁用</a> ';
                        $module['actions'] .= '<a class="btn btn-sm btn-noborder btn-danger confirm-rst-url-btn" data-info="如果包括数据表，将同时删除数据表！确认?" href="'.url('admin/Modules/uninstall', ['name' => $module['name']]).'">卸载</a> ';
                        break;
                    default: // 未知
                        $module['actions'] = '';
                        break;
                }
            }
            cache('module_all', $modules);
            return $modules;
        }
        return $result;
    }
    /**
     * 获取模块市场数据
     * @param bool $filter 过滤本地
     * @return array
     */
    public static function getModulesOnlines($filter = true)
    {
        $options = [
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'X-REQUESTED-WITH: XMLHttpRequest'
            ]
        ];
        $modules_online = [];
        $ret = \Http::sendRequest(config('yfcmf.api_addon.url') . '/lists', ['type' => 2], 'GET', $options);
        if ($ret['ret']) {
            if (substr($ret['msg'], 0, 1) == '{') {
                $json = (array) json_decode($ret['msg'], true);
                $modules_online = $json['data'];
            }
        }
        if ($filter) {
            //过滤本地或判断是否可以升级
            if ($modules_online) {
                //过滤本地已安装的模块
                foreach ($modules_online as $key => &$value) {
                    $module_name = $key;
                    if (!is_dir(Env::get('app_path') . $module_name)) {
                        $modules_online[$key]['actions'] = '<a class="btn btn-xs btn-success rst-url-btn" href="' . url('admin/Modules/installOnline', ['name' => $module_name, 'version'=>$value['version']]) . '">安装</a>';
                        //title字段添加链接
                        if ($value['url']) {
                            $value['title'] = '<a class="title" href="'.$value['url'].'" data-toggle="tooltip" title="" target="_blank" data-original-title="查看模块介绍和帮助">'.$value['title'].'</a>';
                        }
                        //author添加qq链接
                        if ($value['qq']) {
                            $value['author'] = '<a href="//wpa.qq.com/msgrd?v=3&uin='.$value['qq'].'&site=yfcmf.net&menu=yes" target="_blank" data-toggle="tooltip" title="" class="text-primary" data-original-title="点击与模块开发者取得联系">'.$value['author'].'</a>';
                        }
                    } else {
                        unset($modules_online[$key]);
                    }
                }
            }
        }
        return $modules_online;
    }
    /**
     * 安装模块
     *
     * @param   string  $name   模块名称
     * @return  boolean
     * @throws \Exception
     */
    public function install($name)
    {
        // 模块配置信息
        $module_info = $this->getInfoFromFile($name);

        // 执行安装文件
        $install_file = realpath(Env::get('app_path') . $name . '/install.php');
        if (file_exists($install_file)) {
            @include($install_file);
        }

        // 执行安装模块sql文件
        $sql_file = realpath(Env::get('app_path') . $name . '/sql/install.sql');
        if (file_exists($sql_file)) {
            if (isset($module_info['database_prefix']) && $module_info['database_prefix'] != '') {
                db_restore_file($sql_file, $module_info['database_prefix']);
            } else {
                db_restore_file($sql_file);
            }
        }

        // 添加菜单
        $menus = $this->getMenusFromFile($name);
        if (is_array($menus) && !empty($menus)) {
            $auth_rule_model = new AuthRuleModel();
            if (false === $auth_rule_model->addMenus($menus, $name)) {
                exception('菜单添加失败，请重新安装');
            }
        }

        // 检查是否有模块设置信息
        if (isset($module_info['config']) && !empty($module_info['config'])) {
            $module_info['config'] = parse_config($module_info['config']);
        }

        // 将模块信息写入数据库
        $allowField = ['name','title','icon','description','author','author_url','config','access','version','identifier','status', 'nav_url'];

        if ($this->allowField($allowField)->save($module_info)) {
            if(is_dir(Env::get('app_path') . $name . '/public')) {
                // 复制静态资源目录
                copydirs(Env::get('app_path') . $name . '/public', Env::get('root_path') . 'public/' . $name);
                // 删除静态资源目录
                rmdirs(Env::get('app_path') . $name . '/public');
            }
            cache('modules', null);
            cache('module_all', null);
        } else {
            self::where('module', $name)->delete();
            exception('模块安装失败');
        }
        return true;
    }
    /**
     * 卸载模块
     *
     * @param   string  $name   模块名称
     * @param  int $clear  清除数据
     * @return  boolean
     * @throws \Exception
     */
    public function uninstall($name, $clear = 0)
    {
        // 模块配置信息
        $module_info = $this->getInfoFromFile($name);

        // 执行卸载文件
        $uninstall_file = realpath(Env::get('app_path') . $name . '/uninstall.php');
        if (file_exists($uninstall_file)) {
            @include($uninstall_file);
        }

        // 执行卸载模块sql文件
        if ($clear == 1) {
            $sql_file = realpath(Env::get('app_path') . $name . '/sql/uninstall.sql');
            if (file_exists($sql_file)) {
                if (isset($module_info['database_prefix']) && $module_info['database_prefix'] != '') {
                    db_restore_file($sql_file, $module_info['database_prefix']);
                } else {
                    db_restore_file($sql_file);
                }
            }
        }

        // 删除菜单
        $auth_rule_model = new AuthRuleModel();
        if (false === $auth_rule_model->where('module', $name)->delete()) {
            exception('菜单删除失败，请重新卸载');
        }

        // 还原模块文件
        if ($this->where('name', $name)->delete()) {
            if(is_dir(Env::get('root_path') . 'public/' . $name)) {
                // 复制静态资源目录
                copydirs(Env::get('root_path') . 'public/' . $name, Env::get('app_path') . $name . '/public');
                // 删除静态资源目录
                rmdirs(Env::get('root_path') . 'public/' . $name);
            }
            cache('modules', null);
            cache('module_all', null);
        } else {
            exception('模块卸载失败');
        }
        return true;
    }
    /**
     * 检查依赖
     * @param string $type 类型：module/addon
     * @param array $data 检查数据
     * @author rainfer <rainfer520@qq.com>
     * @return array
     */
    public function checkNeeds($type = '', $data = [])
    {
        $need = [];
        foreach ($data as $key => $value) {
            $value[2] = isset($value[2]) ? $value[2] : '=';
            // 当前版本
            if ($type == 'module') {
                $curr_version = $this->where('name', $value[0])->value('version');
            } else {
                $addon_model = new AddonModel();
                $curr_version = $addon_model->where('name', $value[0])->value('version');
            }
            // 比对版本
            $result = version_compare($curr_version, $value[1], $value[2]);
            $need[$key] = [
                $type => $value[0],
                'version' => $curr_version ? $curr_version : '未安装',
                'version_need' => $value[2].$value[1],
                'result' => $result ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>'
            ];
        }
        return $need;
    }
    /**
     * 检查依赖
     * @param string $type 类型：module/addon
     * @param array $data 检查数据
     * @author rainfer <rainfer520@qq.com>
     * @return boolean
     */
    private function check($type = '', $data = [])
    {
        foreach ($data as $key => $value) {
            $value[2] = isset($value[2]) ? $value[2] : '=';
            // 当前版本
            if ($type == 'module') {
                $curr_version = $this->where('name', $value[0])->value('version');
            } else {
                $addon_model = new AddonModel();
                $curr_version = $addon_model->where('name', $value[0])->value('version');
            }
            // 比对版本
            $result = version_compare($curr_version, $value[1], $value[2]);
            if (!$result) {
                return false;
            }
        }
        return true;
    }
    /**
     * 启用模块
     * @param   int  $id   模块id
     * @return  boolean
     * @throws
     */
    public function enable($id)
    {
        $auth_rule_model = new AuthRuleModel();
        $module = $this->find($id);
        $sys_modules = $this->getSysModules();
        if (isset($sys_modules[$module['name']])) {
            return false;
        }
        // 启动事务
        Db::startTrans();
        try {
            self::where('id', $id)->setField('status', 1);
            //模块菜单启用
            $auth_rule_model->where('module', $module['name'])->setField('status', 1);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }
    /**
     * 禁用模块
     * @param   int  $id   模块id
     * @return  boolean
     * @throws
     */
    public function disable($id)
    {
        $auth_rule_model = new AuthRuleModel();
        $module = $this->find($id);
        $sys_modules = $this->getSysModules();
        if (isset($sys_modules[$module['name']])) {
            return false;
        }
        // 启动事务
        Db::startTrans();
        try {
            self::where('id', $id)->setField('status', 0);
            //模块菜单启用
            $auth_rule_model->where('module', $module['name'])->setField('status', 0);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }
    /**
     * 远程安装模块
     *
     * @param   string  $name   模块名称
     * @param   array   $params 参数
     * @return  boolean
     * @throws \Exception
     */
    public function installOnline($name, $params = [])
    {
        if (!$name) {
            exception('安装出错');
            return false;
        }
        if (is_dir(Env::get('app_path') . $name)) {
            exception('本地已存在此模块名的文件夹');
            return false;
        }
        // 远程下载模块
        $tmpFile = $this->download($name, $params);
        // 解压模块
        $moduleDir = $this->unzip($name);
        // 移除临时文件
        @unlink($tmpFile);
        // 模块配置信息
        $module_info = $this->getInfoFromFile($name);

        // 检查模块依赖
        if (isset($module_info['need_module']) && !empty($module_info['need_module'])) {
            if (!$this->check('module', $module_info['need_module'])) {
                exception('不满足前置模块要求');
                return false;
            }
        }
        // 检查模块依赖
        if (isset($module_info['need_addon']) && !empty($module_info['need_addon'])) {
            if (!$this->check('addon', $module_info['need_addon'])) {
                exception('不满足前置模块要求');
                return false;
            }
        }
        // 检查数据表
        $table_check = true;
        if (isset($module_info['tables']) && !empty($module_info['tables'])) {
            foreach ($module_info['tables'] as $table) {
                if (Db::query("SHOW TABLES LIKE '".config('database.prefix')."{$table}'")) {
                    $table_check = false;
                    break;
                }
            }
        }
        if (!$table_check) {
            exception('存在同名数据表');
            return false;
        }
        //安装
        if ($this->install($name)) {
            return true;
        } else {
            @rmdirs($moduleDir);
            exception('安装错误');
            return false;
        }
    }
    /**
     * 远程下载模块
     *
     * @param   string  $name   模块名称
     * @param   array   $params 扩展参数
     * @return  string
     * @throws \Exception
     */
    public static function download($name, $params = [])
    {
        $name = strtolower($name);
        $moduleTmpDir = Env::get('runtime_path') . 'modules' . DIRECTORY_SEPARATOR;
        if (!is_dir($moduleTmpDir)) {
            @mkdir($moduleTmpDir, 0755, true);
        }
        $tmpFile = $moduleTmpDir . $name . ".zip";
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
                        return '';
                    }
                } else {
                    exception($json['msg'], $json['code'], $json['data']);
                    return '';
                }
            }
            if ($write = fopen($tmpFile, 'w')) {
                fwrite($write, $ret['msg']);
                fclose($write);
                return $tmpFile;
            } else {
                exception("没有权限写入临时文件");
                return '';
            }
        } else {
            exception("无法下载远程文件");
            return '';
        }
    }
    /**
     * 解压模块
     *
     * @param   string  $name   模块名称
     * @return  string
     * @throws \Exception
     */
    public static function unzip($name)
    {
        $name = strtolower($name);
        $file = Env::get('runtime_path') . 'modules' . DIRECTORY_SEPARATOR . $name . '.zip';
        $dir = Env::get('app_path') . $name . DIRECTORY_SEPARATOR;
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
        } else {
            exception("无法执行解压操作，请确保ZipArchive安装正确");
            return '';
        }
    }
    /**
     * 升级模块
     *
     * @param   string  $name   模块名
     * @param   array   $params 扩展参数
     * @return    bool
     * @throws \Exception
     */
    public function upgrade($name, $params = [])
    {
        if (!$name) {
            exception('安装出错');
        }
        if (!is_dir(Env::get('app_path') . $name)) {
            exception('本地不存在此模块');
        }
        // 读取数据库模块表
        $module = self::where('name', $name)->find();
        $config = [];
        if (!$module || $module['status']==0) {
            //未安装或禁用中 实例化模块
            $config = $module ? json_decode($module['config'], true) :[];
        } else {
            //使用中
            exception('请先禁用模块');
        }
        // 远程下载模块
        $tmpFile = $this->download($name, $params);
        // 解压模块
        $this->unzip($name);
        // 移除临时文件
        @unlink($tmpFile);
        if (!$module) {
            //安装
            $this->install($name);
        } elseif ($module['status']==0) {
            // 模块配置信息
            $module_info = $this->getInfoFromFile($name);
            if ($config) {
                // 还原配置
                $this->setConfig($name, $config);
            }
            // 执行升级sql文件
            $sql_file = realpath(Env::get('app_path') . $name . '/sql/update.sql');
            if (file_exists($sql_file)) {
                if (isset($module_info['database_prefix']) && $module_info['database_prefix'] != '') {
                    db_restore_file($sql_file, $module_info['database_prefix']);
                } else {
                    db_restore_file($sql_file);
                }
            }
            //更新模块版本
            $this->where('name', $name)->update(['version'=>$params['version'], 'update_time'=>time()]);
        }
        return true;
    }
}
