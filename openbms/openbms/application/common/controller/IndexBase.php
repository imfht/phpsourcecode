<?php

namespace app\common\controller;

class IndexBase extends Base
{
    protected function _initialize()
    {
        parent::_initialize();
        !config('website_status') && die(config('colse_explain'));
        $config = cache('db_config_data');
        if (!$config) {
            $config = [];
            foreach (model('config')->select() as $v) {
                $config[$v['group']][$v['name']] = $v['value'];
            }
            cache('db_config_data', $config);
        }
        config($config);
    }
}
