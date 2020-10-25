<?php
namespace Modules\Core\Library;

use Core\Config;
use Core\File;

class Module
{
    public static function uninstall($name)
    {
        global $di;
        $flash = $di->getShared('flash');
        $name = strtolower($name);
        $modulesList = Config::get('modules');
        if (isset($modulesList[$name])) {
            if (self::disable($name)) {
                $fileState = File::rm('Modules/' . ucfirst($name));
                if ($fileState === true) {
                    $flash->success('停用成功');
                    return true;
                } else {
                    $flash->error('停用失败，模块目录删除失败：' . $fileState);
                    return false;
                }
            } else {
                $flash->error('停用失败');
                return false;
            }
        } else {
            $flash->error('停用失败，模块没有被启用');
        }
        return false;
    }

    public static function lookDisable($name)
    {
        global $di;
        $enabledModules = Config::get('modules');
        if (!isset($enabledModules[$name])) {
            $di->getShared('flash')->error('模块 ' . $name . ' 不能被卸载，因为她没有启用');
            return false;
        }

        $modulesList = Module::listInfo();
        $needModules = [];
        foreach ($enabledModules as $module => $moduleInfo) {
            if (isset($modulesList[$module]) && isset($modulesList[$module]['require'][$name])) {
                $needModules[$name] = $name;
            }
        }
        if (!empty($needModules)) {
            $needModulesString = implode('，', $needModules);
            $di->getShared('flash')->error('模块不能被卸载，因为她需要为以下软件提供支持：' . $needModulesString);
            return false;
        }
        return true;
    }

    public static function lookEnable($name)
    {
        global $di;
        $enabledModules = Config::get('modules');
        $modulesList = self::listInfo();
        if (!isset($modulesList[$name])) {
            $di->getShared('flash')->error('模块不能被安装，因为模块不存在');
            return false;
        }
        $noModules = [];
        $dependentModules = $modulesList[$name]['require'];
        $conflictModules = [];

        //检查软件依赖是否满足
        $i = 0;
        do {
            $value = $dependentModules[$i];
            echo $value . '<br />';
            if (!isset($modulesList[$value])) {
                $noModules[] = $value;
            } else {
                $dependentModules += array_merge($dependentModules, $modulesList[$value]['require']);
            }
            //$needModules = array_unique($needModules);
            $i++;
        } while (isset($dependentModules[$i]));

        if (!empty($noModules)) {
            $noModulesString = implode('，', $noModules);
            $di->getShared('flash')->error('模块不能被安装，因为下面依赖模块不存在：' . $noModulesString);
            return false;
        }
        //检查软件依赖是否满足结束

        //检查是否存在冲突
        foreach ($modulesList[$name]['conflict'] as $value) {
            if (isset($enabledModules[$value])) {
                $conflictModules[] = $value;
            }
        }
        foreach ($dependentModules as $value) {
            if (isset($enabledModules[$value])) {
                $conflictModules[] = $value;
            }
        }

        if (!empty($conflictModules)) {
            $conflictModulesString = implode('，', $conflictModules);
            $di->getShared('flash')->error('模块不能被安装，和以下已安装软件存在冲突：' . $conflictModulesString);
            return false;
        }
        //检查是否存在冲突结束

        return $dependentModules;
    }

    public static function enable($module)
    {
        global $di;
        $lookStartUsing = self::lookEnable($module);
        $modulesList = Config::get('modules');
        $flash = $di->getShared('flash');
        if ($lookStartUsing === false) {
            return false;
        }

        foreach ($lookStartUsing as $name) {
            $name = strtolower($name);
            $state = false;
            $activeFunction = '\Modules\\' . ucfirst($name) . '\Install::enable';
            if (isset($modulesList[$name])) {
                continue;
            }
            if (method_exists('\Modules\\' . ucfirst($name) . '\Install', 'enable')) {
                $state = call_user_func($activeFunction);
            }
            if ($state === false) {
                $flash->error('模块启用失败');
                return false;
            }
            $modulesList[$name] = ucfirst($name);
        }

        if (Config::set('modules', $modulesList)) {
            $flash->success('模块启用成功');
            return true;
        }
        //清除缓存
        $flash->error('模块启用失败');
        return false;
    }

    public static function disable($name)
    {
        global $di;
        $lookStopUsing = self::lookDisable($name);
        if ($lookStopUsing === false) {
            return false;
        }
        $modulesList = Config::get('modules');
        $flash = $di->getShared('flash');
        $name = strtolower($name);
        $state = false;
        $activeFunction = '\Modules\\' . ucfirst($name) . '\Install::disable';
        if (!isset($modulesList[$name])) {
            $flash->notice('模块是停用状态，无法停用');
            return 2;
        }
        if (method_exists('\Modules\\' . ucfirst($name) . '\Install', 'disable')) {
            $state = call_user_func($activeFunction);
        }
        if ($state === false) {
            $flash->error('模块停用失败');
            return false;
        }
        unset($modulesList[$name]);
        if (Config::set('modules', $modulesList)) {
            $flash->success('模块停用成功');
            return true;
        }
        //清除缓存
        return $state;
    }

    public static function listInfo()
    {
        $config = Config::get('config');
        $modulesList = array();
        $dirList = File::listDir('Modules/');
        foreach ($dirList as $key => $value) {
            if (file_exists(ROOT_DIR . $value) && file_exists(ROOT_DIR . $value . 'Info.php')) {
                require_once ROOT_DIR . $value . 'Info.php';
                if (isset($settings)) {
                    $modulesList[strtolower($key)] = $settings;
                    unset($settings);
                }
            }
        }

        return $modulesList;
    }
}
