<?php

/*
 * 系统设置表
 */

class Setting extends Eloquent {

    protected $table = 'setting';
    protected $primaryKey = 'name';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('name', 'value', 'status', 'extend');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = false;

    public function getCacheSwitch() {
        $site_cache = $this->find('site_cache');
        if ($site_cache->status) {
            //先判断是否开启缓存
            return $site_cache->value;
        } else {
            return FALSE;
        }
    }

    /**
     * 查询是否设置了缓存
     * @param string $type
     * @return boolean
     */
    public static function checkCache($type) {
        $site_cache = Setting::find('site_cache');
        $site_cache_value = $site_cache->value;     //0代表全局，1代表内容，2代表分类
        $site_cache_status = $site_cache->status; //1代表开启，0代表关闭
        $site_cache_extend = $site_cache->extend; //首页：1代表开启，0代表关闭
        if ($site_cache_status) {
            switch ($type) {
                case 'node':
                    if ($site_cache_value == 1 || $site_cache_value == 0) {
                        return TRUE;
                    }
                    break;
                case 'category':
                    if ($site_cache_value == 2 || $site_cache_value == 0) {
                        return TRUE;
                    }
                    break;
                case 'home':
                    if ($site_cache_extend == 1) {
                        return TRUE;
                    }
                    break;
                default:
                    if ($site_cache_value == 0) {
                        return TRUE;
                    }
                    break;
            }
            return FALSE;
        }
        return FALSE;
    }

    /**
     * 获取首页和分类文章输出数量限制
     * @param string $type
     * @param int $num
     * @return int
     */
    public static function get_list_num($type, $num = 10) {
        if ($type == 'home') {
            $home_list_num = Setting::find('home_list_num');
            $num = $home_list_num->value ? $home_list_num->value : $num;
        } else {
            $category_list_num = Setting::find('category_list_num');
            $num = $category_list_num->value ? $category_list_num->value : $num;
        }
        return $num;
    }

    public static function module_handle($module_name, $type, $enabled) {
        $module_json_dir = dirname(__DIR__) . '/modules/' . $module_name . '/module.json';
        //1、开启或者关闭模块
        if ($type == 'open' || $type == 'close') {
            if (file_exists($module_json_dir)) {
                //操作模块
                $module = file_get_contents($module_json_dir);
                //处理获取的json文件
                $module = json_decode($module, true);
                $module['enabled'] = $enabled;
                $module = json_encode($module);
                file_put_contents($module_json_dir, $module);
            } else {
                //只有开启模块的时候，才会走这条路
                Base::create_file($module_json_dir);
                $module = array('enabled' => 'true');
                $module = json_encode($module);
                file_put_contents($module_json_dir, $module);
            }
        }
        //当模块执行开启操作的时候，将继续执行安装模块操作
        if ($type == 'open') {
            $setting_module_status = Setting::find('module_status');
            $module_status = unserialize($setting_module_status->value);
            if (!isset($module_status[$module_name])) {
                $type = 'install';
            }
        }
        //安装或者删除模块
        if ($type == 'install' || $type == 'uninstall') {
            $install_dir = dirname(__DIR__) . '/modules/' . $module_name . '/install.php';
            if (file_exists($install_dir)) {
                require_once $install_dir;
                $function = $module_name . '_' . $type;
                if (function_exists($function)) {
                    try {
                        //开始事务
                        DB::beginTransaction();
                        $setting_module_status = Setting::find('module_status');
                        $module_status = unserialize($setting_module_status->value);
                        if ($type == 'install') {
                            $module_status[$module_name]['install'] = 1;
                        } else {
                            unset($module_status[$module_name]);
                        }

                        $setting_module_status->value = serialize($module_status);
                        $setting_module_status->save();
                        eval("\$function();");
                    } catch (Exception $e) {
                        //事务回滚
                        DB::rollback();
                        //$message = '错误的操作';
                    }
                    //提交事务
                    DB::commit();
                }
            }
        }
    }

}
