<?php
/**
 * Created by PhpStorm.
 * User: china_wangyu@aliyun.com
 * Date: 2018/6/5
 * Time: 10:14
 *  *  *  *  ** 求职区 **
 *  期望城市： 成都
 *  期望薪资： 12k
 *
 *  个人信息
 *
 *  工作经验: 3年
 *  开发语言: PHP / Python
 *
 *  联系方式：china_wangyu@aliyun.com
 */

namespace aliyuns;
/**
 * Class Json JSON异常处理| JSON返回
 * @package aliyun
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-06-5 16:45:19
 * @version 1.0.2
 */
class Json extends \Exception
{

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        if ($code == 200){
            self::success($message);
        }else{
            self::error($message);
        }
    }

    /**
     * [error 请求失败]
     * @param  int|integer $code [description]
     * @param  string      $msg  [description]
     * @return [type]            [description]
     */
    public static function error(string $msg = '请求失败')
    {
        self::return_abnormal(400, $msg);
    }

    /**
     * [success 请求成功]
     * @param  string $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function success(string $msg = '请求成功', array $data = [])
    {
        self::return_abnormal(200, $msg, $data);
    }

    /**
     * [return_abnormal 输出异常]
     * @param  [type] $code [状态码]
     * @param  [type] $msg  [原因]
     * @param  array  $data [输出数据]
     * @return [type]       [json]
     */
    public static function return_abnormal($code, $msg, $data = [])
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

    /**
     * [return_abnormal 成功输出]
     * @param  array  $data [输出数据]
     * @return [type]       [json]
     */
    public static function successAjax(array $data = []){
        header("HTTP/1.1 " . 200 . " OK" );
        header('Content-Type:application/json;charset=utf-8');
        if ($data !== null) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

}
