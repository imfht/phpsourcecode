<?php

/**
 * 规格属性
 */

namespace app\shop\middle;

class SpecMiddle extends \app\base\middle\BaseMiddle {


    protected function data() {
        $classId = intval($this->params['class_id']);
        $app = html_clear($this->params['app']);
        if(empty($classId)) {
            return $this->stop('栏目未指定！');
        }
        if(empty($app)) {
            return $this->stop('应用未指定！');
        }
        $list = target($app.'/'.$app.'Class')->loadCrumbList($classId);
        $groupId = [];
        foreach ($list as $vo) {
            if($vo['spec_group_id']) {
                $groupId = $vo['spec_group_id'];
                break;
            }
        }
        if(empty($groupId)) {
            return $this->run();
        }
        $info = target('shop/ShopSpecGroup')->getInfo($groupId);
        if(empty($info['spec_ids'])) {
            return $this->run();
        }
        $specList = target('shop/ShopSpec')->loadList([
            '_sql' => 'spec_id in ('.$info['spec_ids'].')'
        ]);
        return $this->run($specList);
    }


}