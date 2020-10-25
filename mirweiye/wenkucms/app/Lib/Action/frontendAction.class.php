<?php
/**
 * 前台控制器基类
 *
 * @author andery
 */
class frontendAction extends baseAction {
    protected $visitor = null;
    public function _initialize($state = true) {
        parent::_initialize();
        //网站状态
        if (!C('wkcms_site_status')) {
            header('Content-Type:text/html; charset=utf-8');
            exit(C('wkcms_closed_reason'));
        }
        $actionname = ACTION_NAME;
        if ($actionname != 'doccon') {
            $cssnum = 1;
        }
        $modulename = $this->getActionName();
        //dump(ACTION_NAME);
        $this->assign('modulename', $modulename);
        $this->assign('actionname', $actionname);
        $this->assign('cssnum', $cssnum);
        //初始化访问者
        $this->_init_visitor();
        global $userinfo;
        $userinfo = $this->visitor->info;
        $this->assign('uid', $userinfo['uid']);
        //第三方登陆模块
        $this->_assign_oauth();
        //网站主导航与底部导航加载
        if (false === $navlist = F('nav_list')) {
            $navlist = D('nav')->nav_cache();
        }
        //dump($navlist);
        $this->assign('navlist', $navlist);
        if ($state) {
            $cate = D('doc_cate')->where(array('pid' => 0, 'status' => 1))->select();
            if ($cate) {
                $this->assign('cate', $cate);
            }
        }
        
        //网站导航选中
        $this->assign('nav_curr', '');
    }

    /**
     * 初始化访问者
     */
    private function _init_visitor() {
        $this->visitor = new user_visitor();
        $this->assign('visitor', $this->visitor->info);
    }
    /**
     * 第三方登陆模块
     */
    private function _assign_oauth() {
        if (false === $oauth_list = F('oauth_list')) {
            $oauth_list = D('oauth')->oauth_cache();
        }
        $this->assign('oauth_list', $oauth_list);
    }
    /**
     * SEO设置
     */
    protected function _config_seo($seo_info = array(), $data = array()) {
        $page_seo = array('title' => C('wkcms_site_title'), 'keywords' => C('wkcms_site_keyword'), 'description' => C('wkcms_site_description'));
        $page_seo = array_merge($page_seo, $seo_info);
        //开始替换
        $searchs = array('{site_name}', '{site_title}', '{site_keywords}', '{site_description}');
        $replaces = array(C('wkcms_site_name'), C('wkcms_site_title'), C('wkcms_site_keyword'), C('wkcms_site_description'));
        preg_match_all("/\{([a-z0-9_-]+?)\}/", implode(' ', array_values($page_seo)), $pageparams);
        if ($pageparams) {
            foreach ($pageparams[1] as $var) {
                $searchs[] = '{' . $var . '}';
                $replaces[] = $data[$var] ? strip_tags($data[$var]) : '';
            }
            //符号
            $searchspace = array('((\s*\-\s*)+)', '((\s*\,\s*)+)', '((\s*\|\s*)+)', '((\s*\t\s*)+)', '((\s*_\s*)+)');
            $replacespace = array('-', ',', '|', ' ', '_');
            foreach ($page_seo as $key => $val) {
                $page_seo[$key] = trim(preg_replace($searchspace, $replacespace, str_replace($searchs, $replaces, $val)), ' ,-|_');
            }
        }
        return $page_seo;
    }
    /**
     * 连接用户中心
     */
    protected function _user_server() {
        $passport = new passport(C('wkcms_integrate_code'));
        return $passport;
    }
    /**
     * 前台分页统一
     */
    protected function _pager($count, $pagesize) {
        $pager = new Page($count, $pagesize);
        $pager->rollPage = 5;
        //$pager->setConfig('prev', '<');
        $pager->setConfig('theme', '%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %first% %prePage% %linkPage% %nextPage% %downPage% %end%');
        return $pager;
    }
    /**
     * 列表页面
     */
    public function index() {
        $map = $this->_search();
        $mod = D($this->_name);
        !empty($mod) && $this->_list($mod, $map);
        if ($this->_name == 'user_role') {
            $this->_list($mod, $map, 'score', 'asc');
        }
        $this->display();
    }
    /**
     * 列表处理
     *
     * @param obj $model  实例化后的模型
     * @param bool $list_relation 是否存在关联模型
     * @param array $map  条件数据
     * @param string $sort_by  排序字段
     * @param string $order_by  排序方法
     * @param string $field_list 显示字段
     * @param intval $pagesize 每页数据行数
     */
    protected function _list($list_relation, $model, $map = array(), $sort_by = '', $order_by = '', $field_list = '*', $pagesize = 15) {
        //排序
        $data = array();
        $mod_pk = $model->getPk();
        if ($this->_request("sort", 'trim')) {
            $sort = $this->_request("sort", 'trim');
        } else if (!empty($sort_by)) {
            $sort = $sort_by;
        } else if ($this->sort) {
            $sort = $this->sort;
        } else {
            $sort = $mod_pk;
        }
        if ($this->_request("order", 'trim')) {
            $order = $this->_request("order", 'trim');
        } else if (!empty($order_by)) {
            $order = $order_by;
        } else if ($this->order) {
            $order = $this->order;
        } else {
            $order = 'DESC';
        }
        //如果需要分页
        if ($pagesize) {
            $count = $model->where($map)->count($mod_pk);
            $pager = $this->_pager($count, $pagesize);
        }
        $select = $model->field($field_list)->where($map)->order($sort . ' ' . $order);
        $list_relation && $select->relation(true);
        if ($pagesize) {
            $select->limit($pager->firstRow . ',' . $pager->listRows);
            $page = $pager->show();
            $data['page'] = $page;
        }
        $list = $select->select();
        if ($model->getTablename() == C('DB_PREFIX') . 'doc_con') {
            foreach ($list as $key => $value) {
                $ratyarr = getraty($value['id'], 1);
                // dump($ratyarr);
                if ($ratyarr['raty'] != null) {
                    $list[$key]['raty'] = $ratyarr['raty'] / 10;
                } else {
                    $list[$key]['raty'] = 0;
                }
            }
        }
        //dump($list);
        $data['list'] = $list;
        $data['list_table'] = 1;
        return $data;
    }
}
