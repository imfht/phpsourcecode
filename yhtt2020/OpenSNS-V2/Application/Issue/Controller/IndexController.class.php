<?php


namespace Issue\Controller;

use Think\Controller;


class IndexController extends Controller
{
    /**
     * 业务逻辑都放在 WeiboApi 中
     * @var
     */
    public function _initialize()
    {
        if(D('Common/Module')->isInstalled('Mob')) {
            $sign = modC('JUMP_MOB', 0, 'mob');
            if(is_mobile() && ($sign == 0)) {
                redirect('Mob/Issue/index');
            }
        }
        
        $tree = D('Issue')->getTree();
        $this->assign('tree', $tree);
        
        $sub_menu =
            array(
                'left' =>
                    array(
                        array('tab' => 'home', 'title' =>L('_HOME_'), 'href' => U('Issue/index/index')),
                    ),
            );
        if (check_auth('addIssueContent')) {
            $sub_menu['right'] = array(
                array('tab' => 'post', 'title' => L('_RELEASE_'), 'href' => '#frm-post-popup','a_class'=>'open-popup-link')
            );
        }
        foreach ($tree as $cat) {
            if ($cat['_']) {
                $children = array();
                $children[] = array('tab' => 'cat_' . $cat['id'], 'title' => L('_ALL_'), 'href' => U('Issue/index/index', array('issue_id' => $cat['id'])));
                foreach ($cat['_'] as $child) {
                    $children[] = array('tab' => 'cat_' . $cat['id'], 'title' => $child['title'], 'href' => U('Issue/index/index', array('issue_id' => $child['id'])));
                }

            }
            $menu_item = array('children' => $children, 'tab' => 'cat_' . $cat['id'], 'title' => $cat['title'], 'href' => U('Issue/Index/index', array('issue_id' => $cat['id'])));
            $sub_menu['left'][] = $menu_item;
            unset($children);
        }
        $sub_menu['first']=array('title'=>L('_MODULE_'));
        $this->assign('sub_menu', $sub_menu);

    }

    public function index($page = 1, $issue_id = 0)
    {
        //设置展示方式 列表；瀑布流
        $aDisplay_type=I('display_type','','text');
        $cookie_type=cookie('issue_display_type');
        if($aDisplay_type==''){
            if($cookie_type){
                $aDisplay_type=$cookie_type;
            }else{
                $aDisplay_type=modC('DISPLAY_TYPE','list','Issue');
                cookie('issue_display_type',$aDisplay_type);
            }
        }else{
            if($cookie_type!=$aDisplay_type){
                cookie('issue_display_type',$aDisplay_type);
            }
        }
        $this->assign('display_type',$aDisplay_type);
        //设置展示方式 列表；瀑布流 end

        $issue_id = intval($issue_id);
        $issue = D('Issue')->find($issue_id);
        if (!$issue_id == 0) {
            $issue_id = intval($issue_id);
            $issues = D('Issue')->where("id=%d OR pid=%d", array($issue_id, $issue_id))->limit(999)->select();
            $ids = array();
            foreach ($issues as $v) {
                $ids[] = $v['id'];
            }
            $map['issue_id'] = array('in', implode(',', $ids));
        }
        $map['status'] = 1;
        $content = D('IssueContent')->where($map)->order('create_time desc')->page($page, 16)->select();
        $totalCount = D('IssueContent')->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['issue'] = D('Issue')->field('id,title')->find($v['issue_id']);
            if($aDisplay_type=='masonry'){
                $cover = M('Picture')->where(array('status' => 1))->getById($v['cover_id']);
                $c_path=$cover['path'];
                $tag='ttp:';
                if(!strpos($c_path,$tag))
                    $c_path='.'.$cover['path'];
                $imageinfo = getimagesize($c_path);
                $v['cover_height']=round($imageinfo[1]*255/$imageinfo[0]);
                $v['cover_height']=$v['cover_height']?$v['cover_height']:253;
            }
        }
        unset($v);
        $this->assign('contents', $content);
        $this->assign('totalPageCount', $totalCount);
        $this->assign('top_issue', $issue['pid'] == 0 ? $issue['id'] : $issue['pid']);

        $this->assign('issue_id', $issue_id);
        $this->setTitle(L('_MODULE_'));
        $this->display();
    }

    public function doPost($id = 0, $cover_id = 0, $title = '', $content = '', $issue_id = 0, $url = '')
    {
        if (!check_auth('addIssueContent')) {
            $this->error(L('_AUTHORITY_LACK_'));
        }
        $issue_id = intval($issue_id);
        if (!is_login()) {
            $this->error(L('_FIRST_LOGIN_'));
        }
        if (!$cover_id) {
            $this->error(L('_NEED_COVER_'));
        }
        if (trim(op_t($title)) == '') {
            $this->error(L('_NEED_TITLE_'));
        }
        if (trim(op_h($content)) == '') {
            $this->error(L('_NEED_CONTENT_'));
        }
        if ($issue_id == 0) {
            $this->error(L('_NEED_CATEGORY_'));
        }
        if (trim(op_h($url)) == '') {
            $this->error(L('_NEED_WEBSITE_'));
        }
        $content = D('IssueContent')->create();
        $content['content'] = filter_content($content['content']);
        $content['title'] = op_t($content['title']);
        $content['url'] = op_t($content['url']); //新增链接框
        $content['issue_id'] = $issue_id;

        if ($id) {
            $content_temp = D('IssueContent')->find($id);
            if (!check_auth('editIssueContent')) { //不是管理员则进行检测
                if ($content_temp['uid'] != is_login()) {
                    $this->error(L('_FORBID_TO_OTHER_'));
                }
            }
            $content['uid'] = $content_temp['uid']; //权限矫正，防止被改为管理员
            $rs = D('IssueContent')->save($content);
            if ($rs) {
                $this->success(L('_SUCCESS_EDIT_'), U('issueContentDetail', array('id' => $content['id'])));
            } else {
                $this->success(L('_FAIL_EDIT_'), '');
            }
        } else {
            if (modC('NEED_VERIFY', 0) && !is_administrator()) //需要审核且不是管理员
            {
                $content['status'] = 0;
                $tip = L('_TIP_AUDIT_');
                $user = query_user(array('nickname'), is_login());
                $admin_uids = explode(',', C('USER_ADMINISTRATOR'));
                foreach ($admin_uids as $admin_uid) {
                    D('Common/Message')->sendMessage($admin_uid, $title = L('_WARN_CONTRIBUTE_'),"{$user['nickname']}".L('_PLEASE_AUDIT_'),  'Admin/Issue/verify', array(),is_login(), 2);
                }
            }
            $rs = D('IssueContent')->add($content);
            if ($rs) {
                $this->success(L('_SUCCESS_CONTRIBUTE_') . $tip, 'refresh');
            } else {
                $this->success(L('_FAIL_CONTRIBUTE_'), '');
            }
        }


    }

    public function issueContentDetail($id = 0)
    {


        $issue_content = D('IssueContent')->find($id);
        if (!$issue_content) {
            $this->error('404 not found');
        }
        D('IssueContent')->where(array('id' => $id))->setInc('view_count');
        $issue = D('Issue')->find($issue_content['issue_id']);

        $this->assign('top_issue', $issue['pid'] == 0 ? $issue['id'] : $issue['pid']);
        $this->assign('issue_id', $issue['id']);
        $issue_content['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $issue_content['uid']);
        $this->assign('content', $issue_content);
        $this->setTitle('{$content.title|op_t}' . '——'.L('_MODULE_'));
        $this->setKeywords($issue_content['title']);
        $this->display();
    }

    public function selectDropdown($pid)
    {
        $issues = D('Issue')->where(array('pid' => $pid, 'status' => 1))->limit(999)->select();
        exit(json_encode($issues));


    }

    public function edit($id)
    {
        if (!check_auth('addIssueContent') && !check_auth('editIssueContent')) {
            $this->error(L('_ERROR_SORRY_'));
        }
        $issue_content = D('IssueContent')->find($id);
        if (!$issue_content) {
            $this->error('404 not found');
        }
        if (!check_auth('editIssueContent')) { //不是管理员则进行检测
            if ($issue_content['uid'] != is_login()) {
                $this->error('404 not found');
            }
        }

        $issue = D('Issue')->find($issue_content['issue_id']);

        $this->assign('top_issue', $issue['pid'] == 0 ? $issue['id'] : $issue['pid']);
        $this->assign('issue_id', $issue['id']);
        $issue_content['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $issue_content['uid']);
        $this->assign('content', $issue_content);
        $this->display();
    }
}