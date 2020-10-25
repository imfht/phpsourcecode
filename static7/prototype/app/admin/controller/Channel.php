<?php

namespace app\admin\controller;

use think\Loader;
use think\Url;
use think\Cache;

/**
 * Description of Channel
 * 导航管理
 * @author static7
 */
class Channel extends Admin {
    /**
     * 导航列表
     * @param int $pid 父级ID
     * @author staitc7 <static7@qq.com>
     */

    public function index($pid = 0) {
        $Channel = Loader::model('Channel');
        $data = $Channel->channelList($pid);
        $father = $Channel->father($pid); //查询父级ID
        $value = ['data' => $data['data'] ?? null, 'pid' => $pid, 'father' => $father, 'page' => $data['page']];
        $this->view->metaTitle = '导航列表';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 编辑导航
     * @param int $id 导航ID
     * @author staitc7 <static7@qq.com>
     */

    public function edit($id = 0) {
        $info = Loader::model('Channel')->edit($id);
        $value['info'] = $info ?? null;
        $this->view->metaTitle = '导航详情';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 新增导航
     * @param int $pid 父级导航ID
     * @author staitc7 <static7@qq.com>
     */

    public function add($pid = 0) {
        $value['pid'] = (int)$pid ?? 0;
        $this->view->metaTitle = '新增导航';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 用户更新或者添加导航
     * @author staitc7 <static7@qq.com>
     */

    public function renew() {
        $Channel = Loader::model('Channel');
        $info = $Channel->renew();
        if ($Channel->getError()) {
            return $this->error($info);
        }
        Cache::rm('menu_list');
        return $this->success('操作成功', Url::build('index', ['pid' => $info['pid'] ?? 0]));
    }

}
