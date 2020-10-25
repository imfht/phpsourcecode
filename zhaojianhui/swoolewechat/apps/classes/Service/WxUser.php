<?php
namespace App\Service;

use Swoole;

/**
 * 微信用户相关服务类
 * @package App\Service
 */
class WxUser
{
    /**
     * @var \App\Model\WxUser
     */
    private $wxUserModel;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->wxUserModel = model('WxUser');
    }

    /**
     * 同步所有用户信息到本地
     */
    public function syncOnline()
    {
        $nextOpenId = '';
        do{
            $users = Swoole::$php->easywechat->user->lists($nextOpenId);
            $users = $users->toArray();
            if (isset($users['next_openid']) && !empty($users['next_openid'])){
                $nextOpenId = $users['next_openid'];
            }else{
                $nextOpenId = '';
            }
            if (isset($users['data']['openid']) && !empty($users['data']['openid'])){
                foreach ($users['data']['openid'] as $openId){
                    $this->syncUser($openId);
                }
            }
        }while(!empty($nextOpenId));

        return true;
    }
    /**
     * 同步微信用户信息
     * @param $openId
     * @return bool|int
     */
    public function syncUser($openId)
    {
        $userInfo         = Swoole::$php->easywechat->user->get($openId);
        if ($userInfo) {
            if ($userInfo->subscribe == 1) {
                $saveData = [
                    'openId'        => $userInfo->openid,
                    'unionId'       => $userInfo->unionid,
                    'nickName'      => $userInfo->nickname,
                    'sex'           => (int) $userInfo->sex,
                    'country'       => $userInfo->country,
                    'province'      => $userInfo->province,
                    'city'          => $userInfo->city,
                    'language'      => $userInfo->language,
                    'headimgurl'    => $userInfo->headimgurl,
                    'subscribeTime' => (int) $userInfo->subscribe_time,
                    'remark'        => $userInfo->remark,
                    'groupId'       => (int) $userInfo->groupid,
                    'tagidList'     => json_encode((array) $userInfo->tagid_list),
                ];
            }
            $saveData['subscribe'] = (int) $userInfo->subscribe;
        }
        $findOne = $this->wxUserModel->getone(['openId'=>$openId]);
        if ($findOne){
            return $this->wxUserModel->set($findOne['userId'], $saveData);
        }else{
            $saveData['openId'] = $openId;
            $saveData['firstSubscribeTime'] = (int) $userInfo->subscribe_time;
            $saveData['createTime'] = time();
            return $this->wxUserModel->put($saveData);
        }
    }
    /**
     * 设置用户备注
     * @param $openId
     * @param string $remark
     * @return bool
     * @throws \Exception
     */
    public function setRemark($userId, $remark = '')
    {
        $this->wxUserModel->start();
        try{
            $findUser = $this->wxUserModel->getone(['userId'=>$userId]);
            if (!$findUser){
                throw new \Exception('微信用户不存在');
            }
            $rs = Swoole::$php->easywechat->user->remark($findUser['openId'], $remark);
            if (!$rs){
                throw new \Exception('线上设置用户备注失败');
            }
            $localRs = $this->wxUserModel->set($findUser['userId'], ['remark' => $remark]);
            if (!$localRs){
                throw new \Exception('本地设置备注失败');
            }
            $this->wxUserModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxUserModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 批量拉黑用户
     * @param array $userIds
     * @return bool
     * @throws \Exception
     */
    public function setBatchBlock($userIds = [])
    {
        if (!$userIds){
            throw new \Exception('请选择要拉黑的用户ID');
        }
        $this->wxUserModel->start();
        try{
            $userList = $this->wxUserModel->gets([
                'select' => 'userId,openId',
                'where' => "`userId` in (".implode(',', $userIds).") AND `isBlock`=0"
            ]);
            if (!$userList){
                throw new \Exception('您所选择拉黑的用户无效');
            }
            $userIds = array_column($userList, 'userId');
            $openIdList = array_column($userList, 'openId');
            $rs = Swoole::$php->easywechat->user->batchBlock($openIdList);
            if ($rs['errcode'] != 0){
                throw new \Exception('线上拉黑用户失败');
            }
            $rsLocal = $this->wxUserModel->sets(['isBlock'=>1], [
                'where' => "`userId` in (".implode(',', $userIds).")"
            ]);
            if (!$rsLocal){
                throw new \Exception('本地拉黑用户失败');
            }
            $this->wxUserModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxUserModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 批量取消拉黑用户
     * @param array $userIds
     * @return bool
     * @throws \Exception
     */
    public function setBatchUnblock($userIds = [])
    {
        if (!$userIds){
            throw new \Exception('请选择要取消拉黑的用户ID');
        }
        $this->wxUserModel->start();
        try{
            $userList = $this->wxUserModel->gets([
                'select' => 'userId,openId',
                'where' => "`userId` in (".implode(',', $userIds).") AND `isBlock`=1"
            ]);
            if (!$userList){
                throw new \Exception('您所选择取消拉黑的用户无效');
            }
            $userIds = array_column($userList, 'userId');
            $openIdList = array_column($userList, 'openId');
            $rs = Swoole::$php->easywechat->user->batchUnblock($openIdList);
            if ($rs['errcode'] != 0){
                throw new \Exception('线上取消拉黑用户失败');
            }
            $rsLocal = $this->wxUserModel->sets(['isBlock'=>0], [
                'where' => "`userId` in (".implode(',', $userIds).")"
            ]);
            if (!$rsLocal){
                throw new \Exception('本地取消拉黑用户失败');
            }
            $this->wxUserModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxUserModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 批量设置用户分组
     * @param $userIds
     * @param int $wxGroupId
     * @return bool
     * @throws \Exception
     */
    public function setUsersGroup($userIds, $wxGroupId = 0)
    {
        if (!$userIds){
            throw new \Exception('请选择要移动分组的用户ID');
        }
        $this->wxUserModel->start();
        try{
            $userList = $this->wxUserModel->gets([
                'select' => 'userId,openId',
                'where' => "`userId` in (".implode(',', $userIds).") AND `subscribe`=1"
            ]);
            if (!$userList){
                throw new \Exception('您选择设置分组的用户无效');
            }
            $userIds = array_column($userList, 'userId');
            $openIdList = array_column($userList, 'openId');
            $rs = Swoole::$php->easywechat->user_group->moveUsers($openIdList, $wxGroupId);
            if ($rs['errcode'] != 0){
                throw new \Exception('线上设置用户分组失败');
            }
            $rsLocal = $this->wxUserModel->sets(['groupId'=>$wxGroupId], [
                'where' => "`userId` in (".implode(',', $userIds).")"
            ]);
            if (!$rsLocal){
                throw new \Exception('本地设置用户分组失败');
            }
            $this->wxUserModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxUserModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 设置用户标签
     * @param $userId
     * @param $wxTagIds
     * @return bool
     * @throws \Exception
     */
    public function setUserTag($userId, $wxTagIds)
    {
        $userData = $this->wxUserModel->getone([
            'where'=>"`userId`=$userId",
            'select' => 'openId,tagidList',
        ]);
        if (!$userData){
            throw new \Exception('用户不存在');
        }
        $userTagIds = $userData['tagidList'] ? json_decode($userData['tagidList']) : [];
        //相同的标签ID列表
        $sameTagIds = array_intersect($userTagIds, $wxTagIds);
        //删除的标签ID列表
        $removeTagIds = array_diff($userTagIds, $sameTagIds);
        //新增的标签ID列表
        $newTagIds = array_diff($wxTagIds, $sameTagIds);
        //处理用户标签
        if ($removeTagIds){
            foreach ($removeTagIds as $removeTagId){
                Swoole::$php->easywechat->user_tag->batchUntagUsers([$userData['openId']], $removeTagId);
            }
        }
        if ($newTagIds){
            foreach ($newTagIds as $newTagId){
                Swoole::$php->easywechat->user_tag->batchTagUsers([$userData['openId']], $newTagId);
            }
        }
        $this->wxUserModel->set($userId, ['tagidList'=>json_encode($wxTagIds)]);
        return true;
    }
}