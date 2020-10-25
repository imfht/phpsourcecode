<?php

/**
 * 公共API
 */

namespace app\member\api;

class OuterApi extends \dux\kernel\Api {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 图片验证码
     */
    protected function getImgCode() {
        return new \dux\lib\Vcode(90, 37, 4, '', 'code');
    }

    /**
     * 获取验证图片
     */
    public function verifyImg() {
        $this->getImgCode()->showImage();
    }

}