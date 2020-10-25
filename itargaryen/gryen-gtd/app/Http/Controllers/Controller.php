<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 异步请求，json 结果处理.
     * @param $code
     * @param $msg
     * @param mixed $data
     * @return array
     */
    public function jsonResult($code, $msg, mixed $data = null)
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
    }
}
