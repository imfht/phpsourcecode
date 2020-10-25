<?php
namespace App\Service;

/**
 * 权限规则表服务类
 * @package App\Service
 */
class SysAuthRule
{
    /**
     * 权限规则模型
     * @var \App\Model\SysAuthRule
     */
    private $sysAuthRuleModel;
    public function __construct()
    {
        $this->sysAuthRuleModel = model('SysAuthRule');
    }

    /**
     * 保存规则数据
     * @param $data
     * @return bool|int
     * @throws \Exception
     */
    public function saveData($data)
    {
        $id = isset($data['ruleId']) ? (int) $data['ruleId'] : 0;
        $saveData = [
            'ruleName' => $data['ruleName'],
            'url' => $data['url'],
            'parentId' => $data['parentId'],
            'condition' => $data['condition'],
            'isPublic' => $data['isPublic'] ?? 0,
            'isOpen' => $data['isOpen'] ?? 1,
        ];
        //判断是否重名
        $existsWhere = ['parentId'=>$saveData['parentId'], 'ruleName'=>$saveData['ruleName'],'isDel'=>0];
        $id && $existsWhere['ruleId !'] = $id;
        $findExists = $this->sysAuthRuleModel->exists($existsWhere);
        if ($findExists){
                throw new \Exception('该层级已存在同名权限');
        }
        if ($id){//修改
            return $this->sysAuthRuleModel->set($id, $saveData);
        }else{//添加
            //排序最大值
            $maxOrderNum = $this->sysAuthRuleModel->getMax('orderNum');
            $saveData['orderNum'] = $maxOrderNum + 1;
            $saveData['addUserId'] =$data['addUserId'];
            $saveData['addTime'] = time();
            return $this->sysAuthRuleModel->put($saveData);
        }
    }

    /**
     * 设置顺序
     * @param $parentId
     * @param $ruleId
     * @param $position
     * @return bool
     * @throws \Exception
     */
    public function setOrderNum($parentId, $ruleId, $position)
    {
        $authRuleList = $this->sysAuthRuleModel->getAuthRuleListByParentId($parentId);
        $orderKeyList = [];
        //第一个首先插入
        if($position == 0){
            $orderKeyList[] = $ruleId;
        }
        if ($authRuleList){
            $countAuthRule = count($authRuleList);
            foreach ($authRuleList as $k => $v){
                //如果插入之前顺序恰当，将当前规则id强制加入
                if($k == $position){
                    $orderKeyList[] = $ruleId;
                }
                //非当前规则id时：按顺序排序规则ID
                if ($v['ruleId'] != $ruleId){
                    $orderKeyList[] = $v['ruleId'];
                }
            }
            //插入最后
            if($position == $countAuthRule - 1){
                $orderKeyList[] = $ruleId;
            }
        }
        if ($orderKeyList){
            $this->sysAuthRuleModel->start();
            try{
                foreach ($orderKeyList as $orderNum => $ruleId){
                    $this->sysAuthRuleModel->set($ruleId, ['orderNum' => $orderNum, 'parentId'=>$parentId]);
                }
                $this->sysAuthRuleModel->commit();
                return true;
            }catch (\Exception $e){
                $this->sysAuthRuleModel->rollback();
                throw new \Exception($e->getMessage());
            }
        }
    }
}