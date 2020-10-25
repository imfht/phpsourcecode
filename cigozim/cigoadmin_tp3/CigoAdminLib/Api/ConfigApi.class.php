<?php

namespace CigoAdminLib\Api;

class ConfigApi
{
    public function getCacheList()
    {
        $dataList = M('SystemConfig')->where(array('cache-flag' => 1))
            ->order('sort desc, create_time desc')->getField('flag, config, value');
        return $dataList;
    }
}
