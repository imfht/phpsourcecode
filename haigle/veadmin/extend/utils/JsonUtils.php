<?php
namespace utils;


class JsonUtils
{
    /**
     * [msgSuccess 带data数据返回json]
     * @author [王东海] [991382548@qq.com]
     * return json
     */
    public function msgSuccess($data, $msg = null)
    {
        return json(['code' => 666, 'msg' => $msg?$msg:"请求成功", "data" => $data]);
    }

    /**
     * [msgError 带data数据返回json]
     * @author [王东海] [991382548@qq.com]
     * return json
     */
    public function msgError($data = null, $msg = null)
    {
        return json(['code' => 999, 'msg' => $msg?$msg:"请求失败", "data" => ""]);
    }

    /**
     * [success 成功返回json]
     * @author [王东海] [991382548@qq.com]
     * return json
     */
    public function success($msg = null)
    {
        return json(['code' => 200, 'msg' => $msg?$msg:"请求成功"]);
    }

    /**
     * [error 失败返回json]
     * @author [王东海] [991382548@qq.com]
     * return json
     */
    public function error($msg = null)
    {
        return json(['code' => 500, 'msg' => $msg?$msg:"请求失败"]);
    }
}