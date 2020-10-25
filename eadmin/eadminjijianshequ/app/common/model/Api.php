<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 接口模型
 */
class Api extends ModelBase
{

    /**
     * 请求数据获取器
     */
    public function getRequestDataAttr($request_data)
    {

        return json_decode($request_data, true);
    }

    /**
     * 响应数据获取器
     */
    public function getResponseDataAttr($response_data)
    {

        return json_decode($response_data, true);
    }

    /**
     * API分组获取器
     */
    public function getGroupNameAttr($group_id)
    {


        return $this->setname('ApiGroup')->getDataValue(['id' => $group_id], 'name');
    }

    /**
     * 请求类型获取器
     */
    public function getRequestTypeTextAttr($request_type)
    {

        return $request_type ? 'GET' : 'POST';
    }

    /**
     * API状态获取器
     */
    public function getApiStatusTextAttr($api_status)
    {

        $array = parse_config_array('api_status_option');

        return $array[$api_status];
    }

    /**
     * API研发者获取器
     */
    public function getDeveloperTextAttr($developer)
    {

        $array = parse_config_array('team_developer');

        return $array[$developer];
    }
}
