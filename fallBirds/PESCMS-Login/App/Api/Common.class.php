<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Api;

abstract class Common extends \Core\Controller\Controller {

    /**
     * 回调信息JSON格式
     * @param type $msg 信息
     * @param type $data 数据
     * @param type $status 状态码 | 默认为 0
     */
    protected function returnMsg($msg, $data = '', $status = '0') {
        echo json_encode(array('msg' => $msg, 'data' => $data, 'status' => $status));
        exit;
    }

}
