<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Api as LogicApi;

/**
 * API管理控制器
 */
class Api extends AdminBase
{

    // API逻辑
    private static $apiLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$apiLogic = get_sington_object('apiLogic', LogicApi::class);
    }

    /**
     * API列表
     */
    public function apiList()
    {
        $clist = self::$apiLogic->getApiList([], true, 'sort desc, id desc');


        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);


        return $this->fetch('api_list');
    }

    /**
     * API添加
     */
    public function apiAdd()
    {

        IS_POST && $this->jump(self::$apiLogic->apiEdit($this->param));

        $this->apiAssignGroupList('group_list');

        $info['request_data_json']  = $this->getApiDataFieldDefault();
        $info['response_data_json'] = $this->getApiDataFieldDefault(false);


        $this->assign('edit', 'no');
        $this->assign('info', $info);
        $this->assign('api_data_type_option', self::$apiLogic->getApiDataOption());

        return $this->fetch('api_edit');
    }

    /**
     * 获取API数据字段默认值
     */
    public function getApiDataFieldDefault($mark = 'request_data')
    {

        return $mark == 'request_data' ? json_encode([['', 0, 0, '']]) : json_encode([['', 0, '']]);
    }

    /**
     * API编辑
     */
    public function apiEdit()
    {

        IS_POST && $this->jump(self::$apiLogic->apiEdit($this->param));

        $this->apiAssignGroupList('group_list');

        $info = self::$apiLogic->getApiInfo(['id' => $this->param['id']]);

        !empty($info['request_data']) ? $info['request_data_json'] = json_encode(relevance_arr_to_index_arr(json_decode($info['request_data']))) : $info['request_data_json'] = $this->getApiDataFieldDefault();
        !empty($info['response_data']) ? $info['response_data_json'] = json_encode(relevance_arr_to_index_arr(json_decode($info['response_data']))) : $info['response_data_json'] = $this->getApiDataFieldDefault(false);
        $this->assign('edit', 'yes');

        $this->assign('info', $info);
        $this->assign('api_data_type_option', self::$apiLogic->getApiDataOption());

        return $this->fetch('api_edit');
    }

    /**
     * Assign API 分组
     */
    public function apiAssignGroupList($name = 'list')
    {

        $clist = self::$apiLogic->getApiGroupList([], true, 'sort desc');

        $this->assign($name, $clist['data']);

        $this->assign('page', $clist['page']);
    }

    /**
     * API删除
     */
    public function apiDel($id = 0)
    {

        $this->jump(self::$apiLogic->apiDel(['id' => $id]));
    }

    /**
     * API分组列表
     */
    public function apiGroupList()
    {

        $this->apiAssignGroupList();

        return $this->fetch('api_group_list');
    }

    /**
     * API分组添加
     */
    public function apiGroupAdd()
    {

        IS_POST && $this->jump(self::$apiLogic->apiGroupEdit($this->param));

        return $this->fetch('api_group_edit');
    }

    /**
     * API分组编辑
     */
    public function apiGroupEdit()
    {

        IS_POST && $this->jump(self::$apiLogic->apiGroupEdit($this->param));

        $info = self::$apiLogic->getApiGroupInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('api_group_edit');
    }

    /**
     * API分组删除
     */
    public function apiGroupDel($id = 0)
    {

        $this->jump(self::$apiLogic->apiGroupDel(['id' => $id]));
    }
}
