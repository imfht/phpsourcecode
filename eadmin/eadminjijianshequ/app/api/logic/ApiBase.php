<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\logic;

use app\common\logic\LogicBase;
use app\api\error\CodeBase;

/**
 * Api基础逻辑
 */
class ApiBase extends LogicBase
{

    /**
     * API返回数据
     */
    public function apiReturn($code_data = [], $return_data = [], $return_type = 'json')
    {

        $result = null;

        if (array_key_exists(API_CODE_NAME, $code_data)) {

            !empty($return_data) && $code_data['data'] = $return_data;

            $result = $code_data;

        } else {

            $code_data_count = count($code_data);

            if (2 == $code_data_count) {

                $code_data[DATA_DISABLE]['data'] = $code_data[DATA_SUCCESS];
            }

            if (DATA_SUCCESS == $code_data_count) {

                $data = $code_data[DATA_DISABLE];

                $code_data[DATA_DISABLE] = CodeBase::$success;

                !empty($data) && $code_data[DATA_DISABLE]['data'] = $data;
            }

            $result = $code_data[DATA_DISABLE];
        }


        $return_result = $this->checkDataSign($result);

        $return_result['exe_time'] = debug('api_begin', 'api_end');

        return $return_type == 'json' ? json($return_result) : $return_result;
    }

    /**
     * 检查是否需要数据签名
     */
    public function checkDataSign($data)
    {

        $ApiModel = model('Api');

        $info = $ApiModel->getInfo(['api_url' => URL]);

        $info['is_response_sign'] && !empty($data['data']) && $data['data']['data_sign'] = data_auth_sign($data['data']);

        return $data;
    }

    /**
     * API错误终止程序
     */
    public function apiError($code_data = [])
    {

        exit(json_encode($code_data));
    }

    /**
     * API提交附加参数检查
     */
    public function checkParam($param = [])
    {

        $ApiModel = model('Api');

        $info = $ApiModel->getInfo(['api_url' => URL]);

        empty($info) && $this->apiError(CodeBase::$apiUrlError);

        if (!empty($param['appkey'])) {
            (empty($param['access_token']) || $param['access_token'] != get_access_token($param['appkey'])) && $this->apiError(CodeBase::$accessTokenError);
        } else {
            (empty($param['access_token']) || $param['access_token'] != get_access_token()) && $this->apiError(CodeBase::$accessTokenError);
        }


        $info['is_user_token'] && empty($param['user_token']) && $this->apiError(CodeBase::$userTokenError);

        if (!empty($param['access_token']))  : unset($param['access_token']); endif;

        if (!empty($param['user_token']))    : unset($param['user_token']); endif;

        if ($info['is_request_sign'])        : (empty($param['data_sign']) || data_auth_sign($param) != $param['data_sign']) && $this->apiError(CodeBase::$dataSignError); endif;
    }
}
