<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/3
 * Time: 13:56
 */

namespace traits;


use fastwork\facades\Response;

trait JsonResult
{


    public function error($data = '', $msg = '操作失败')
    {
        return $this->result(0, $msg, $data);

    }

    public function success($data = '', $msg = '操作成功')
    {
        return $this->result(1, $msg, $data);
    }

    public function result($code, $msg = '', $data = '')
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        return Response::json($result);
    }
}