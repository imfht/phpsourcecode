<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Config;

/**
 * 公共接口
 */
class Common extends Api
{

    protected $noNeedLogin = ['init'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 加载初始化
     * @param string $lng 经度
     * @param string $lat 纬度
     */
    public function init()
    {
        if () {
            $lng = $this->request->request('lng');
            $lat = $this->request->request('lat');
            $content = [
                'citydata'    => Area::getCityFromLngLat($lng, $lat),
                'uploaddata'  => Config::get('upload'),
                'coverdata'   => Config::get("cover"),
            ];
            $this->success('', $content);
        } else {
            $this->error('error');
        }
    }

}
