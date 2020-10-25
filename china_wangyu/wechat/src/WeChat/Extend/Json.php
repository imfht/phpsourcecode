<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Extend;

use Throwable;

/**
 * Class Json json输出类
 * @package wechat\lib
 */
class Json extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * 请求失败
     * @param string $msg
     */
    public static function error(string $msg = '请求失败')
    {
        self::return_abnormal(400, $msg);
    }

    /**
     * 请求成功
     * @param string $msg 返回消息
     * @param array $data 返回data数据
     */
    public static function success(string $msg = '请求成功', array $data = [])
    {
        self::return_abnormal(200, $msg, $data);
    }


    /**
     * 输出JSON
     * @param int $code 状态码
     * @param string $msg   原因
     * @param array $data   输出数据
     */
    public static function return_abnormal(int $code,string $msg,array $data = [])
    {
        $code_state = $code == 200 ? 'OK' : 'Bad Request';
        $param      = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        header("HTTP/1.1 " . $code . " " . $code_state);
        header('Content-Type:application/json;charset=utf-8');
        if ($param !== null) {
            echo json_encode($param, JSON_UNESCAPED_UNICODE);
        }
        exit();
    }
}