<?php
namespace Modules\Core\Library;

use Core\Config;
use Core\File;

class Theme
{
    public static function listInfo()
    {
        $config = Config::get('config');
        $modulesList = array();
        $dirList = File::listDir('Themes/');
        foreach ($dirList as $key => $value) {
            if (file_exists(ROOT_DIR . $value) && file_exists(ROOT_DIR . $value . 'Info.php')) {
                require_once ROOT_DIR . $value . 'Info.php';
                if (isset($settings)) {
                    $modulesList[$key] = $settings;
                    unset($settings);
                }
            }
        }
        return $modulesList;
    }

    public static function enable($theme, $controllers)
    {
        global $di;
        $enableThemes = Config::get('themes');
        $ucfirstTheme = ucfirst($theme);
        $ucfirstControllers = ucfirst($controllers);
        if ($enableThemes[$ucfirstControllers] == $ucfirstTheme) {
            $di->getShared('flash')->notice('主题已经启用，无需再次启用');
            return true;
        }
        if (file_exists(ROOT_DIR . 'Themes/' . $ucfirstTheme) && file_exists(ROOT_DIR . 'Themes/' . ucfirst($theme) . '/Info.php')) {
            require_once ROOT_DIR . 'Themes/' . $ucfirstTheme . '/Info.php';
            if (isset($settings)) {
                if (isset($settings['controllers'][$controllers])) {
                    //禁用已启用主题
                    if ($enableThemes[$ucfirstControllers]) {
                        self::disable($enableThemes[$ucfirstControllers]);
                    }

                    //启用新主题
                    $activeFunction = '\Themes\\' . $ucfirstTheme . '\Install::enable';
                    if (method_exists('\Themes\\' . $ucfirstTheme . '\Install', 'enable')) {
                        $state = call_user_func($activeFunction);
                        if ($state) {
                            $enableThemes[$ucfirstControllers] = $ucfirstTheme;
                            if (Config::set('themes', $enableThemes)) {
                                $di->getShared('flash')->success('主题已经成功启用');
                                return true;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        }
        $di->getShared('flash')->error('主题启用失败');
        return false;
    }

    public static function disable($theme, $controllers)
    {
        global $di;
        $enableThemes = Config::get('themes');
        $ucfirstTheme = ucfirst($theme);
        $ucfirstControllers = ucfirst($controllers);
        if ($enableThemes[$ucfirstControllers] != $ucfirstTheme) {
            $di->getShared('flash')->notice('主题没有被启用，无需禁用');
            return true;
        }
        if (file_exists(ROOT_DIR . 'Themes/' . $ucfirstTheme) && file_exists(ROOT_DIR . 'Themes/' . $ucfirstTheme . '/Info.php')) {
            require_once ROOT_DIR . 'Themes/' . $ucfirstTheme . '/Info.php';
            if (isset($settings)) {
                if (isset($settings['controllers'][$controllers])) {
                    $activeFunction = '\Themes\\' . $ucfirstTheme . '\Install::disable';
                    if (method_exists('\Themes\\' . $ucfirstTheme . '\Install', 'disable')) {
                        $state = call_user_func($activeFunction);
                        if ($state) {
                            $enableThemes[$ucfirstControllers] = '';
                            if (Config::set('themes', $enableThemes)) {
                                $di->getShared('flash')->success('主题已经成功禁用');
                                return true;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        }
        $di->getShared('flash')->error('主题启用失败');
        return false;
    }

    public static function uninstall($theme)
    {
        global $di;
        $disableTheme = true;
        $ucfirstTheme = ucfirst($theme);
        $flash = $di->getShared('flash');
        $themesList = Config::get('themes');
        if (array_search($ucfirstTheme,$themesList)) {
            $disableTheme = self::disable($theme);
        }
        if ($disableTheme) {
            $fileState = File::rm('Themes/' . $ucfirstTheme);
            if ($fileState === true) {
                $flash->success('卸载成功');
                return true;
            } else {
                $flash->error('卸载失败，主题目录删除失败：' . $fileState);
                return false;
            }
        } else {
            $flash->error('卸载失败，主题没有被启用');
        }
        return false;
    }
}