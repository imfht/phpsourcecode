<?php

namespace App\Service;

class WxMenu
{
    /**
     * @var \App\Model\WxMenu
     */
    private $wxMenuModel;
    /**
     * 菜单类别列表.
     *
     * @var
     */
    private $menuTypeList = [
        'click'              => '点击类型',
        'view'               => '网页类型',
        'miniprogram'        => '小程序',
        'scancode_push'      => '扫码推事件',
        'scancode_waitmsg'   => '扫码带提示',
        'pic_sysphoto'       => '系统拍照发图',
        'pic_photo_or_album' => '拍照或者相册发图',
        'pic_weixin'         => '微信相册发图',
        'location_select'    => '发送位置',
        'media_id'           => '图片',
        'view_limited'       => '图文消息',
    ];
    /**
     * 客户端版本列表.
     *
     * @var
     */
    private $clientPlatformTypeList=[
        1 => 'IOS',
        2 => 'Android',
        3 => 'Others',
    ];
    /**
     * 语言列表.
     *
     * @var
     */
    private $languageList = [
        'zh_CN' => '简体中文',
        'zh_TW' => '繁体中文TW',
        'zh_HK' => '繁体中文HK',
        'en'    => '英文',
        'id'    => '印尼',
        'ms'    => '马来 ',
        'es'    => '西班牙',
        'ko'    => '韩国',
        'it'    => '意大利',
        'ja'    => '日本',
        'pl'    => '波兰',
        'pt'    => '葡萄牙',
        'ru'    => '俄国',
        'th'    => '泰文',
        'vi'    => '越南',
        'ar'    => '阿拉伯语',
        'hi'    => '北印度',
        'he'    => '希伯来',
        'tr'    => '土耳其',
        'de'    => '德语',
        'fr'    => '法语',
    ];

    /**
     * 构造函数.
     */
    public function __construct()
    {
        $this->wxMenuModel = model('WxMenu');
    }

    /**
     * 菜单类别列表.
     */
    public function getMenuTypeList()
    {
        return $this->menuTypeList;
    }

    /**
     * 客户端版本列表.
     */
    public function getClientPlatformTypeList()
    {
        return $this->clientPlatformTypeList;
    }

    /**
     * 获取语言列表.
     *
     * @return array
     */
    public function getLanguageList()
    {
        return $this->languageList;
    }

    /**
     * 保存菜单排序数据.
     *
     * @param $sortData
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function saveSort($sortData)
    {
        $this->wxMenuModel->start();
        try {
            $this->saveSortData($sortData);
            $this->wxMenuModel->commit();

            return true;
        } catch (\Exception $e) {
            $this->wxMenuModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 同步线上数据.
     */
    public function syncOnline()
    {
        return true;
    }

    /**
     * 根据不同菜单类型生成不同菜单数据
     * @param $menuData
     * @return mixed
     */
    private function getFormatMenuData($menuData)
    {
        $formatMenuData = [
            'name' => $menuData['menuName'],
            'type' => $menuData['menuType'],
        ];
        switch ($menuData['menuType']){
            case 'view':
                $formatMenuData['url'] = $menuData['url'];
                break;
            case 'click':
            case 'scancode_push':
            case 'scancode_waitmsg':
            case 'pic_sysphoto':
            case 'pic_photo_or_album':
            case 'pic_weixin':
            case 'location_select':
                $formatMenuData['key'] = $menuData['key'];
                break;
            case 'miniprogram':
                $formatMenuData['url'] = $menuData['url'];
                $formatMenuData['appid'] = $menuData['appid'];
                $formatMenuData['pagepath'] = $menuData['pagepath'];
                break;
            case 'media_id':
            case 'view_limited':
                $formatMenuData['media_id'] = $menuData['media_id'];
                break;
        }
        return $formatMenuData;
    }

    /**
     * 格式话菜单列表
     * @param $menuList
     * @return array
     */
    private function getFormatMenuList($menuList)
    {
        //二维数组排序
        $orderNumKey = [];
        $menuIdKey = [];
        foreach ($menuList as $v){
            $orderNumKey[] = $v['orderNum'];
            $menuIdKey[] = $v['menuId'];
        }
        array_multisort($orderNumKey, SORT_ASC, $menuIdKey, SORT_ASC, $menuList);

        //树结构菜单列表
        $tree          = new \App\Common\Tree('menuId', 'parentId', 'child');
        $tree->nameKey = 'menuName';
        $tree->load($menuList);
        $treeList = $tree->deepTree(0);
        $formMenuList = [];
        if ($treeList){
            foreach ($treeList as $menuData){
                $formMenuData = [];
                if (isset($menuData['child']) && $menuData['child']){
                    $formMenuData['name'] = $menuData['menuName'];
                    foreach ($menuData['child'] as $childMenuData){
                        $formMenuData['sub_button'][] = $this->getFormatMenuData($childMenuData);
                    }
                }else{
                    $formMenuData = $this->getFormatMenuData($menuData);
                }
                $formMenuList[] = $formMenuData;
            }
        }
        return $formMenuList;
    }

    /**
     * 获取菜单列表数据
     * @return array
     */
    private function getRuleGroupMenuList()
    {
        //菜单原始列表
        $menuList = $this->wxMenuModel->getMenuList();
        $normalMenuList = [];
        $conditionalMenuList = [];
        //抽离出普通菜单和个性化菜单
        foreach ($menuList as $menuData){
            if ($menuData['isConditional'] && $menuData['matchrule']){
                $conditionalMenuList[] = $menuData;
            }else{
                $normalMenuList[] = $menuData;
            }
        }
        //按照不同的个性化规则分成不同的菜单组合，这样避免重复创建菜单
        $ruleGroupMenuList = [];
        $ruleGroupMenuList['default']['list'] = $normalMenuList;
        foreach ($conditionalMenuList as $menuData){
            $matchRule = $menuData['matchrule'];
            $key = md5(json_encode($matchRule));
            //如果为创建个性化，则用普通菜单初始化，并且把个性化菜单放进来
            if (!array_key_exists($key, $ruleGroupMenuList)){
                $ruleGroupMenuList[$key]['list'] = $normalMenuList;
                $ruleGroupMenuList[$key]['matchrule'] = $matchRule;
            }
            $ruleGroupMenuList[$key]['list'][] = $menuData;
        }
        //格式化
        foreach ($ruleGroupMenuList as $k => $v){
            $v['list'] = $this->getFormatMenuList($v['list']);
            $ruleGroupMenuList[$k] = $v;
        }
        return $ruleGroupMenuList;
    }
    /**
     * 推送菜单配置到线上.
     */
    public function pushOnline()
    {
        $ruleGroupMenuList = $this->getRuleGroupMenuList();
        \Swoole::$php->easywechat->menu->destroy();

        if ($ruleGroupMenuList){
            foreach ($ruleGroupMenuList as $menuData){
                if (isset($menuData['matchrule']) && $menuData['matchrule']) {
                    \Swoole::$php->easywechat->menu->add($menuData['list'], $menuData['matchrule']);
                } else {
                    \Swoole::$php->easywechat->menu->add($menuData['list']);
                }
            }
            return true;
        }
        throw new \Exception('菜单为空');
    }

    /**
     * 保存菜单数据.
     *
     * @param array $menuData
     */
    public function saveMenu($menuData = [])
    {
        $id = (int) $menuData['menuId'];
        $saveData = [
            'menuType' => $menuData['menuType'],
            'menuName' => $menuData['menuName'],
            'key' => $menuData['key'],
            'url' => $menuData['url'],
            'appid' => $menuData['appid'],
            'pagePath' => $menuData['pagePath'],
            'mediaId' => $menuData['mediaId'],
            'parentId' => (int) $menuData['parentId'],
            'isConditional' => (int) $menuData['isConditional'],
        ];
        $matchrule = [];
        if (!empty($menuData['tag_id'])){
            $matchrule['tag_id'] = (int) $menuData['tag_id'];
        }
        if (!empty($menuData['sex'])){
            $matchrule['sex'] = (int) $menuData['sex'];
        }
        if (!empty($menuData['client_platform_type'])){
            $matchrule['client_platform_type'] = $menuData['client_platform_type'];
        }
        if (!empty($menuData['country'])){
            $matchrule['country'] = $menuData['country'];
        }
        if (!empty($menuData['province'])){
            $matchrule['province'] = $menuData['province'];
        }
        if (!empty($menuData['city'])){
            $matchrule['city'] = $menuData['city'];
        }
        if (!empty($menuData['language'])){
            $matchrule['language'] = $menuData['language'];
        }
        if (!array_key_exists($saveData['menuType'], $this->menuTypeList)){
            throw new \Exception('菜单类别无效');
        }
        if (empty($saveData['menuName'])){
            throw new \Exception('请输入菜单名称');
        }
        if (!in_array($saveData['isConditional'], [0,1])){
            throw new \Exception('请选择是否为个性化菜单');
        }
        if ($saveData['isConditional'] == 1){//个性化菜单
            //首先创建普通默认菜单
            if ($saveData['parentId'] == 0){
                $existsWhere = ['parentId'=>0,'isDel'=>0];
                $findExists = $this->wxMenuModel->exists($existsWhere);
                if (!$findExists){
                    throw new \Exception('请先创建普通自定义菜单');
                }
            }
            if ($menuData['client_platform_type'] && !array_key_exists($menuData['client_platform_type'], $this->clientPlatformTypeList)){
                throw new \Exception('请选择正确的客户端版本');
            }
            if ($menuData['language'] && !array_key_exists($menuData['language'], $this->languageList)){
                throw new \Exception('请选择正常范围内语言');
            }
            if (empty($matchrule)){
                throw new \Exception('请填写个性化菜单规则');
            }
        }else{//普通菜单
            //判断是否重名
            $existsWhere = ['parentId'=>$saveData['parentId'], 'menuName'=>$saveData['menuName'],'isDel'=>0];
            $id && $existsWhere['menuId !'] = $id;
            $findExists = $this->wxMenuModel->exists($existsWhere);
            if ($findExists){
                throw new \Exception('该层级已存在同名菜单');
            }
        }
        $saveData['matchrule'] = json_encode($matchrule);
        $count = $this->wxMenuModel->count([
            'parentId'=>$saveData['parentId'],
            'isDel' => 0,
            'isConditional' => 0,
        ]);
        if ($saveData['isConditional'] == 0 && $saveData['parentId'] == 0 && $count >= 3){
            throw new \Exception('自定义菜单最多包括3个一级菜单');
        }elseif ($saveData['parentId'] > 0 && $count >= 5){
            throw new \Exception('一级菜单最多包含5个二级菜单');
        }
        if ($id){//修改
            return $this->wxMenuModel->set($id, $saveData);
        }else{//添加
            //排序最大值
            $maxOrderNum = $this->wxMenuModel->getMax('orderNum', ['isDel'=>0]);
            $saveData['orderNum'] = $maxOrderNum + 1;
            $saveData['addUserId'] = $menuData['addUserId'];
            $saveData['addTime'] = time();
            return $this->wxMenuModel->put($saveData);
        }
    }

    /**
     * 保存排序数据.
     *
     * @param $list
     * @param int $parentId
     *
     * @return bool
     */
    private function saveSortData($list, $parentId = 0)
    {
        if ($list) {
            foreach ($list as $k => $v) {
                $id = isset($v['id']) && $v['id'] ? (int) $v['id'] : 0;
                $id && $this->wxMenuModel->set($id, ['orderNum' => $k, 'parentId' => $parentId]);
                if (isset($v['children']) && $v['children']) {
                    $this->saveSortData($v['children'], $id);
                }
            }

            return true;
        }
    }
}
