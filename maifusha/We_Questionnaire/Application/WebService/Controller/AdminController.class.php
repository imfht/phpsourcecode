<?php
namespace WebService\Controller;
use WebService\Controller\BaserestController;

/**
 * 后台管理所用资源服务
 */
class AdminController extends BaserestController
{
    /**
     * 获取关注用户列表
     */
    public function listSubscribers()
    {
        $api = A('Weixin/User', 'Api');
        $idList = $api->getSubscriberIDs();

        foreach ($idList as $openid) {
            $info = $api->getUserInfo($openid);

            $info['headimgurl'] = substr_replace($info['headimgurl'], '64', -1,1);
            $info['subscribe_time'] = date('Y-m-d', $info['subscribe_time']);
            $info['sex'] = ($info['sex'] == 1) ? '男': '女';

            $data[$openid] = $info;
        }

        $this->response($data, 'json');
    }

    /**
     * 获取问卷列表
     */
    public function listQuestionnaires()
    {
        $data = M('Questionnaires')->order('create_date desc')->getField('id,type,name,create_date,expire_date');
        $this->response($data, 'json');
    }
}