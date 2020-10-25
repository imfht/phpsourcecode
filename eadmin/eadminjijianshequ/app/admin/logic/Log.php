<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 行为日志逻辑
 */
class Log extends AdminBase
{


    /**
     * 获取日志列表
     */
    public function getLogList($where = [], $field = true, $order = '')
    {

        return $this->setname('ActionLog')->getDataList($where, $field, $order);
    }

    /**
     * 日志删除
     */
    public function logDel($where = [])
    {

        return $this->setname('ActionLog')->dataDel($where, '日志删除成功', true);
    }

    /**
     * 日志添加
     */
    public function logAdd($name = '', $describe = '')
    {

        $member_info = session('member_info');

        $request = request();

        $data['member_id'] = $member_info['id'];
        $data['username']  = $member_info['username'];
        $data['ip']        = $request->ip();
        $data['url']       = $request->url();
        $data['status']    = DATA_NORMAL;
        $data['name']      = $name;
        $data['describe']  = $describe;

        $url = es_url('logList');

        return $this->setname('ActionLog')->dataAdd($data, false, $url, '日志添加成功');
    }
}
