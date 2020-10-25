<?php

/**
 * 筛选接口
 */

namespace app\site\middle;

class FliterMiddle extends \app\base\middle\BaseMiddle {

    protected function data() {
        $classId = $this->params['class_id'];
        $app = $this->params['app'];
        $contentId = intval($this->params['content_id']);
        $type = intval($this->params['type']);
        if(empty($classId)) {
            return $this->stop('栏目未指定！');
        }
        if(empty($app)) {
            return $this->stop('应用未指定！');
        }
        $list = target($app.'/'.$app.'Class')->loadCrumbList($classId);
        $filterId = 0;
        foreach ($list as $vo) {
            if($vo['filter_id']) {
                $filterId = $vo['filter_id'];
                break;
            }
        }
        $filterHtml = target('site/SiteFilter')->getHtml($filterId, $contentId, $type);
        return $this->run([
            'html' => $filterHtml
        ]);
    }

}