<?php
namespace App\Service;

use Swoole;

/**
 * 微信用户标签相关服务类
 * @package App\Service
 */
class WxUserTag
{
    /**
     * @var \App\Model\WxUserTag
     */
    private $wxUserTagModel;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->wxUserTagModel = model('WxUserTag');
    }

    /**
     * 同步所有用户信息到本地
     */
    public function syncOnline()
    {
        $onlineTags = Swoole::$php->easywechat->user_tag->lists();
        $onlineTags = $onlineTags->toArray();
        if (!isset($onlineTags['tags']) || empty($onlineTags['tags'])){
            throw new \Exception('线上标签为空');
        }
        $this->wxUserTagModel->start();
        try{
            foreach ($onlineTags['tags'] as $tagData){
                $findTag = $this->wxUserTagModel->getone(['wxTagId'=>$tagData['id']]);
                $saveData = [
                    'tagName' => $tagData['name'],
                    'userCount' => $tagData['count'],
                    'isDel' => 0,
                ];
                if (!empty($findTag)){
                    $this->wxUserTagModel->set($findTag['tagId'], $saveData);
                }else{
                    $saveData['wxTagId'] = $tagData['id'];
                    $saveData['addUserId'] = Swoole::$php->user->getUid();
                    $saveData['addTime'] = time();
                    $this->wxUserTagModel->put($saveData);
                }
            }
            $this->wxUserTagModel->commit();
            return true;
        }catch (\Exception $e){
            $this->wxUserTagModel->rollback();
            throw new \Exception($e->getMessage());
        }

        return true;
    }
    /**
     * 保存规则数据
     * @param $data
     * @return bool|int
     * @throws \Exception
     */
    public function saveData($data)
    {
        $id = isset($data['tagId']) ? (int) $data['tagId'] : 0;
        $saveData = [
            'parentId' => $data['parentId'],
            'tagName' => $data['tagName'],
        ];
        //判断是否重名
        $existsWhere = ['parentId'=>$saveData['parentId'], 'tagName'=>$saveData['tagName'],'isDel'=>0];
        $id && $existsWhere['tagId !'] = $id;
        $findExists = $this->wxUserTagModel->exists($existsWhere);
        if ($findExists){
            throw new \Exception('该层级已存在同名标签');
        }
        $this->wxUserTagModel->start();
        try{
            if ($id){//修改
                $findData = $this->wxUserTagModel->get($id);
                if (empty($findData)){
                    throw new \Exception('标签数据不存在');
                }
                $upOnline = Swoole::$php->easywechat->user_tag->update($findData['wxTagId'], $saveData['tagName']);
                $upOnline = $upOnline->toArray();
                if ($upOnline['errcode'] != 0){
                    throw new \Exception('线上编辑用户标签失败:'.$upOnline['errmsg']);
                }

                $upLocal = $this->wxUserTagModel->set($id, $saveData);
                if (!$upLocal){
                    throw new \Exception('更新本地用户标签失败');
                }
            }else{//添加
                //排序最大值
                $maxOrderNum = $this->wxUserTagModel->getMax('orderNum');
                $saveData['orderNum'] = $maxOrderNum + 1;
                $saveData['addUserId'] =$data['addUserId'];
                $saveData['addTime'] = time();
                $upOnline = Swoole::$php->easywechat->user_tag->create($saveData['tagName']);
                $upOnline = $upOnline->toArray();
                if (!$upOnline){
                    throw new \Exception('线上创建分组失败');
                }
                $saveData['wxTagId'] = isset($upOnline['tag']['id']) ? $upOnline['tag']['id'] : 0;

                $upLocal = $this->wxUserTagModel->put($saveData);
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
     * 设置顺序
     * @param $parentId
     * @param $tagId
     * @param $position
     * @return bool
     * @throws \Exception
     */
    public function setOrderNum($parentId, $tagId, $position)
    {
        $tagList = $this->wxUserTagModel->getAuthRuleListByParentId($parentId);
        $orderKeyList = [];
        //第一个首先插入
        if($position == 0){
            $orderKeyList[] = $tagId;
        }
        if ($tagList){
            $countAuthtag = count($tagList);
            foreach ($tagList as $k => $v){
                //如果插入之前顺序恰当，将当前规则id强制加入
                if($k == $position){
                    $orderKeyList[] = $tagId;
                }
                //非当前规则id时：按顺序排序规则ID
                if ($v['tagId'] != $tagId){
                    $orderKeyList[] = $v['tagId'];
                }
            }
            //插入最后
            if($position == $countAuthtag - 1){
                $orderKeyList[] = $tagId;
            }
        }
        if ($orderKeyList){
            $this->wxUserTagModel->start();
            try{
                foreach ($orderKeyList as $orderNum => $tagId){
                    $this->wxUserTagModel->set($tagId, ['orderNum' => $orderNum, 'parentId'=>$parentId]);
                }
                $this->wxUserTagModel->commit();
                return true;
            }catch (\Exception $e){
                $this->wxUserTagModel->rollback();
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * 删除标签
     * @param $tagId
     * @return bool
     * @throws \Exception
     */
    public function del($tagId)
    {
        $findData = $this->wxUserTagModel->get($tagId);
        if (!$findData){
            throw new \Exception('用户标签不存在');
        }
        $upOnline = Swoole::$php->easywechat->user_tag->delete($findData['wxTagId']);
        $upOnline = $upOnline->toArray();
        if (!$upOnline){
            throw new \Exception('删除线上失败');
        }
        $upLocal = $this->wxUserTagModel->set($findData['tagId'], ['isDel'=>1]);
        if (!$upLocal){
            throw new \Exception('删除本地失败');
        }
        return true;
    }

    /**
     * 微信标签ID集转换为名称集
     * @param array $wxTagIds
     * @return array
     */
    public function wxTagIdsToNames($wxTagIds = []){
        $tagList = $this->wxUserTagModel->getUserTagList();
        if ($tagList){
            $tagIdToNameMap = array_combine(array_column($tagList, 'wxTagId'), array_column($tagList, 'tagName'));
        }else{
            $tagIdToNameMap = [];
        }
        $names = [];
        if ($tagIdToNameMap && $wxTagIds){
            foreach ($wxTagIds as $wxTagId){
                isset($tagIdToNameMap[$wxTagId]) && $names[] = $tagIdToNameMap[$wxTagId];
            }
        }
        return $names;
    }
    /**
     * 本地tagId集转换为微信tagId集
     * @param array $tagIds
     * @return array
     */
    public function tagIdsToWxTagIds($tagIds = [])
    {
        $tagList = $this->wxUserTagModel->getUserTagList();
        if ($tagList){
            $tagIdToWxTagIdMap = array_combine(array_column($tagList, 'tagId'), array_column($tagList, 'wxTagId'));
        }else{
            $tagIdToWxTagIdMap = [];
        }
        $wxTagIds = [];
        if ($tagIdToWxTagIdMap && $tagIds){
            foreach ($tagIds as $tagId){
                isset($tagIdToWxTagIdMap[$tagId]) && $wxTagIds[] = $tagIdToWxTagIdMap[$tagId];
            }
        }
        return $wxTagIds;
    }

    /**
     * 微信tagId集转换为本地TagId集
     * @param array $wxTagIds
     * @return array
     */
    public function wxTagIdsToTagIds($wxTagIds = [])
    {
        $tagList = $this->wxUserTagModel->getUserTagList();
        if ($tagList){
            $wxTagIdToTagIdMap = array_combine(array_column($tagList, 'wxTagId'), array_column($tagList, 'tagId'));
        }else{
            $wxTagIdToTagIdMap = [];
        }
        $tagIds = [];
        if ($wxTagIdToTagIdMap && $wxTagIds){
            foreach ($wxTagIds as $wxTagId){
                isset($wxTagIdToTagIdMap[$wxTagId]) && $tagIds[] = $wxTagIdToTagIdMap[$wxTagId];
            }
        }
        return $tagIds;
    }
}