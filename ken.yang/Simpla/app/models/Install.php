<?php

/*
 * 安装模块
 */

class Install extends Eloquent {

    /**
     * 获取最低配置要求
     */
    public static function _getLowestEnvironment() {
        return array(
            'os' => '不限制',
            'version' => '5.4',
            'mcrypt' => '必须',
            //'mysql' => '4.2',
            //'pdo_mysql' => '必须',
            'upload' => '不限制',
            'space' => '50M',
            'gd' => '2.0');
    }

    /**
     * 获取当前配置环境
     */
    public static function _getCurrentEnvironment() {
        $lowestEnvironment = self::_getLowestEnvironment();
        $rootPath = '/';
        $space = floor(@disk_free_space($rootPath) / (1024 * 1024));
        $space = !empty($space) ? $space . 'M' : 'unknow';
        $currentUpload = ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';
        $upload_ischeck = intval($currentUpload) >= intval($lowestEnvironment['upload']) ? true : false;
        $space_ischeck = intval($space) >= intval($lowestEnvironment['space']) ? true : false;
        $version_ischeck = version_compare(phpversion(), $lowestEnvironment['version']) < 0 ? false : true;
        if (function_exists('gd_info')) {
            $gdinfo = gd_info();
            $gd = $gdinfo['GD Version'];
            $gd_ischeck = version_compare($lowestEnvironment['gd'], $gd) < 0 ? false : true;
        } else {
            $gd_ischeck = false;
            $gd = 'unknow';
        }

        return array(
            'os_ischeck' => true,
            'version_ischeck' => $version_ischeck,
            'mcrypt_ischeck' => 1, //mcrypt php 扩展必须安装
            'mcrypt' => 1,
            //'mysql_ischeck' => $mysql_ischeck,
            //'pdo_mysql_ischeck' => $pdo_mysql_ischeck,
            'upload_ischeck' => $upload_ischeck,
            'space_ischeck' => $space_ischeck,
            'gd_ischeck' => $gd_ischeck,
            'gd' => $gd,
            'os' => PHP_OS,
            'version' => phpversion(),
            //'mysql' => $mysql,
            //'pdo_mysql' => $pdo_mysql_ischeck,
            'upload' => $currentUpload,
            'space' => $space);
    }

    /**
     * 获取推荐配置环境
     */
    public static function _getRecommendEnvironment() {
        return array(
            'os' => '类UNIX',
            'version' => '>5.4.x',
            'mcrypt' => '必须',
            //'mysql' => '>5.x.x',
            //'pdo_mysql' => '必须',
            'upload' => '>2M',
            'space' => '>50M',
            'gd' => '>2.0.28');
    }

}
