<?php
// +----------------------------------------------------------------------
// | tp5_vue_authbuild
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace tpvue\admin\hooks;


use think\facade\App;
use think\facade\Config;
use think\facade\Env;

class ConfigHook
{
    public function run($params)
    {
        defined('PUBLIC_URL') or define('PUBLIC_URL', isset($params['module'][0]) ? $params['module'][0] : config('default_module'));

        $new_config = [];
        //头信息

        $module_public_url = Env::get('APP_PATH', '/') . 'static/' . PUBLIC_URL;
        $new_config['view_replace_str']['__MODULE__'] = $module_public_url;
        $new_config['view_replace_str']['__MODULE_IMG__'] = $module_public_url . '/images';
        $new_config['view_replace_str']['__MODULE_CSS__'] = $module_public_url . '/css';
        $new_config['view_replace_str']['__MODULE_JS__'] = $module_public_url . '/js';
        $new_config['view_replace_str']['__MODULE_LIBS__'] = $module_public_url . '/libs';

        Config::set($new_config);


    }

}