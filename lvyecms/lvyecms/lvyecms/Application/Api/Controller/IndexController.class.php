<?php

// +----------------------------------------------------------------------
// | LvyeCMS
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.lvyecms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 旅烨集团 <web@alvye.cn>
// +----------------------------------------------------------------------

namespace Api\Controller;

use Common\Controller\LvyeCMS;

class IndexController extends LvyeCMS {

    public function token() {
        $token = \Libs\Util\Encrypt::authcode($_POST['token'], 'DECODE', C('CLOUD_USERNAME'));
        if (!empty($token)) {
            S($this->Cloud->getTokenKey(), $token, 3600);
            $this->success('验证通过');
            exit;
        }
        $this->error('验证失败');
    }

}
