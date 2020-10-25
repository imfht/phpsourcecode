<?php
namespace App\Service;
/**
 * 系统用户组服务类
 * @package App\Service
 */
class SysUserGroup
{
    /**
     * @var \App\Model\SysUserGroup
     */
    private $sysUserGroupModel;
    public function __construct()
    {
        $this->sysUserGroupModel = model('SysUserGroup');
    }
    /**
     * 保存菜单排序数据
     * @param $sortData
     * @return bool
     */
    public function saveSort($sortData)
    {
        $this->sysUserGroupModel->start();
        try{
            $this->saveSortData($sortData);
            $this->sysUserGroupModel->commit();
            return true;
        }catch (\Exception $e){
            $this->sysUserGroupModel->rollback();
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
                $id && $this->sysUserGroupModel->set($id, ['orderNum' => $k, 'parentId' => $parentId]);
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
        $findExists = $this->sysUserGroupModel->exists($existsWhere);
        if ($findExists){
            throw new \Exception('该层级已存在同名分组');
        }
        if ($id){//修改
            return $this->sysUserGroupModel->set($id, $saveData);
        }else{//添加
            //排序最大值
            $maxOrderNum = $this->sysUserGroupModel->getMax('orderNum');
            $saveData['orderNum'] = $maxOrderNum + 1;
            $saveData['ruleIds'] = serialize([]);
            $saveData['addUserId'] =$data['addUserId'];
            $saveData['addTime'] = time();
            return $this->sysUserGroupModel->put($saveData);
        }
    }
}