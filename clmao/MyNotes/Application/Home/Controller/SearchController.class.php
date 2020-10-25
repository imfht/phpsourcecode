<?php

namespace Home\Controller;

use Think\Controller;

class SearchController extends Controller {

    public function index() {
        $flag = I('get.flag');
        $key = I('get.key');
        if (empty($flag) || empty($key)) {
            $this->redirect('/');
        } else if ($flag == 'index') {
            $this->actionName = $key . ' | 搜索结果 | ' . getSiteOption('siteName');
            $this->categoryName = '';
            $this->contentName = $key;
            $data = D('content')->getPage(0,1,0,$key);
            $this->assign('list', $data['list']); // 赋值数据集
            $this->assign('page', $data['page']); // 赋值分页输出
            $this->display('Index/index');
        }
    }

}
