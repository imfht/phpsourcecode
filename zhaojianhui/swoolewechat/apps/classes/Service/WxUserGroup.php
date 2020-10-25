<?php
namespace App\Service;

use Swoole;
/**
 * 微信用户组服务类
 * @package App\Service
 */
class WxUserGroup
{
    /**
     * @var \App\Model\WxUserGroup
     */
    private $wxUserGroupModel;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->wxUserGroupModel = model('WxUserGroup');
    }

    /**
     * 同步线上用户组数据
     * @return bool
     * @throws \Exception
     */
    public function syncOnline()
    {
        $onlineGroups = Swoole::$php->easywechat->user_group->lists();
        $onlineGroups = $onlineGroups->toArray();
        if (!isset($onlineGroups['groups']) || empty($onlineGroups['groups'])){
            throw new \Exception('线上用户组数据为空');
        }
        $this->wxUserGroupModel->start();
        try{
            foreach ($onlineGroups['groups'] as $groupData){
                $findGroup = $this->wxUserGroupModel->getone(['wxGroupId'=>$groupData['id']]);
                $saveData = [
                    'groupName' => $groupData['name'],
                    'userCount' => $groupData['count'],
                    'isDel' => 0,
                ];
                if (!empty($findGroup)){
                    $this->wxUserGroupModel->set($findGroup['groupId'], $saveData);
                }else{
                    $saveData['wxGroupId'] = $groupData['id'];
                    $saveData['addUserId'] = Swoole::$php->user->getUid();
                    $saveData['addTime'] = time();
                    $this->wxUserGroupModel->put($saveData);
                }
            }
            $this->wxUserGroupModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxUserGroupModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * 保存菜单排序数据
     * @param $sortData
     * @return bool
     */
    public function saveSort($sortData)
    {
        $this->wxUserGroupModel->start();
        try{
            $this->saveSortData($sortData);
            $this->wxUserGroupModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxUserGroupModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 保存排序数据
     * @param $list
     * @param int $parentId
     * @return bool
     */
    private function saveSortData($list, $parentId = 0)
    {
        if ($list) {
            foreach ($list as $k => $v) {
                $id = isset($v['id']) && $v['id'] ? (int)$v['id'] : 0;
                $id && $this->wxUserGroupModel->set($id, ['orderNum' => $k, 'parentId' => $parentId]);
                if (isset($v['children']) && $v['children']) {
                    $this->saveSortData($v['children'], $id);
                }
            }
            return true;
        }
    }
    /**
     * 保存菜单数据
     * @param $data
     */
    public function saveData($data = [])
    {
        $id = (int) $data['groupId'];
        $saveData = [
            'groupName' => $data['groupName'],
            'parentId' => $data['parentId'],
        ];
        //判断是否重名
        $existsWhere = ['parentId'=>$saveData['parentId'], 'groupName'=>$saveData['groupName'],'isDel'=>0];
        $id && $existsWhere['groupId !'] = $id;
        $findExists = $this->wxUserGroupModel->exists($existsWhere);
        if ($findExists){
            throw new \Exception('该层级已存在同名分组');
        }
        $this->wxUserGroupModel->start();
        try{
            if ($id){//修改
                $findData = $this->wxUserGroupModel->get($id);
                if (empty($findData)){
                    throw new \Exception('分组数据不存在');
                }
                $upOnline = Swoole::$php->easywechat->user_group->update($findData['wxGroupId'], $saveData['groupName']);
                $upOnline = $upOnline->toArray();
                if ($upOnline['errcode'] != 0){
                    throw new \Exception('线上编辑分组失败:'.$upOnline['errmsg']);
                }

                $upLocal = $this->wxUserGroupModel->set($id, $saveData);
                if (!$upLocal){
                    throw new \Exception('更新本地微信分组失败');
                }
            }else{//添加
                //排序最大值
                $maxOrderNum = $this->wxUserGroupModel->getMax('orderNum');
                $saveData['orderNum'] = $maxOrderNum + 1;
                $saveData['addUserId'] =$data['addUserId'];
                $saveData['addTime'] = time();
                $upOnline = Swoole::$php->easywechat->user_group->create($saveData['groupName']);
                $upOnline = $upOnline->toArray();
                if (!$upOnline){
                    throw new \Exception('线上创建分组失败');
                }
                $saveData['wxGroupId'] = isset($upOnline['group']['id']) ? $upOnline['group']['id'] : 0;

                $upLocal = $this->wxUserGroupModel->put($saveData);
                if (!$upLocal){
                    throw new \Exception('本地创建微信分组失败');
                }
            }
            return true;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 删除分组
     * @param $groupId
     * @return bool
     * @throws \Exception
     */
    public function del($groupId)
    {
        $findData = $this->wxUserGroupModel->get($groupId);
        if (!$findData){
            throw new \Exception('用户组不存在');
        }
        $upOnline = Swoole::$php->easywechat->user_group->delete($findData['wxGroupId']);
        $onlineGroupData = $upOnline->toArray();
        if (!$onlineGroupData){
            throw new \Exception('删除线上失败');
        }
        $upLocal = $this->wxUserGroupModel->set($findData['groupId'], ['isDel'=>1]);
        if (!$upLocal){
            throw new \Exception('删除本地失败');
        }
        return true;
    }
}