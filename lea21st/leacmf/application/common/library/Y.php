<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/2/5
 * Time: 10:32
 */

namespace app\common\library;

class Y
{
    /**
     * 返回数据格式化
     * @return mixed
     */
    public static final function json()
    {
        $res       = new \stdClass();
        $res->code = 0;
        $res->msg  = 'success';
        $res->data = new \stdClass();

        $field = func_get_args();

        switch (count($field)) {
            case 0:
                break;
            case 1:
                if (is_scalar($field[0])) {
                    $res->msg = $field[0];
                } else {
                    $res->data = $field[0];
                }
                break;
            case 2:
                $res->code = $field[0];
                $res->msg  = $field[1];
                break;
            case 3:
                $res->code = $field[0];
                $res->msg  = $field[1];
                $res->data = $field[2];
                break;
            default:
                $res->code = 500;
                $res->msg  = 'fail';
        }

        $res->code = intval($res->code);

        //将返回结果统一为字符串，方便客户端操作
        if (!empty($res->data)) {
            if (!is_array($res->data)) {
                $res->data = json_decode(json_encode($res->data), true);
            }
            array_walk_recursive($res->data, function (&$val) {
                $val = strval($val);
            });
        }
        $code = $res->code >= 200 && $res->code <= 500 ? $res->code : 200;
        return json($res, $code)->send();
    }

}