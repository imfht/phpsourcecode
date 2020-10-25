<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 4:19 PM
 */

namespace App\Http\Controllers;

class BaseController extends Controller
{
    /**
     * 格式化返回参数
     * @param string $data 返回数据
     * @return array
     */
    protected function success($data = '', $count = false)
    {
        $return = array(
            'code' => '0',
            'status_code' => '200',
            'msg' => '',
            'data' => true
        );
        if (is_numeric($count)) {
            $return['count'] = $count;
        }
        $return['data'] = $this->formatResponse($data);
        return response()->json($return, 200, array(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 转换返回数据
     * @param $data
     * @return array
     */
    protected function formatResponse($data)
    {
        if (is_object($data)) {
            $data = $data->toArray();
        }
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $val) {
                unset($data[$key]);
                if (is_array($val) || is_object($val)) {
                    $data[$key] = $this->formatResponse($val);
                } elseif (!isset($val)) {
                    $data[$key] = '';
                } else {
                    $data[$key] = (string)$val;//全部返回字符串
                }
            }
        }
        return $data;
    }
}
