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
        $tree = D('Issue')->getTree();
        $this->assign('tree', $tree);
    }

    public function index($page = 1, $issue_id = 0)
    {
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
        }
        unset($v);
        $this->assign('contents', $content);
        $this->assign('totalPageCount', $totalCount);
        $this->assign('top_issue', $issue['pid'] == 0 ? $issue['id'] : $issue['pid']);

        $this->assign('issue_id', $issue_id);
        $this->setTitle('专辑');
        $this->display();
    }

    public function doPost($id = 0, $cover_id = 0, $title = '', $content = '', $issue_id = 0, $url = '')
    {
        if (!check_auth('addIssueContent')) {
            $this->error('抱歉，您不具备投稿权限。');
        }
        $issue_id = intval($issue_id);
        if (!is_login()) {
            $this->error('请登陆后再投稿。');
        }
        if (!$cover_id) {
            $this->error('请上传封面。');
        }
        if (trim(op_t($title)) == '') {
            $this->error('请输入标题。');
        }
        if (trim(op_h($content)) == '') {
            $this->error('请输入内容。');
        }
        if ($issue_id == 0) {
            $this->error('请选择分类。');
        }
        if (trim(op_h($url)) == '') {
            $this->error('请输入网址。');
        }
        $content = D('IssueContent')->create();
        $content['content'] = op_h($content['content']);
        $content['title'] = op_t($content['title']);
        $content['url'] = op_t($content['url']); //新增链接框
        $content['issue_id'] = $issue_id;

        if ($id) {
            $content_temp = D('IssueContent')->find($id);
            if (!check_auth('editIssueContent')) { //不是管理员则进行检测
                if ($content_temp['uid'] != is_login()) {
                    $this->error('不可操作他人的内容。');
                }
            }
            $content['uid'] = $content_temp['uid']; //权限矫正，防止被改为管理员
            $rs = D('IssueContent')->save($content);
            if ($rs) {
                $this->success('编辑成功。', U('issueContentDetail', array('id' => $content['id'])));
            } else {
                $this->success('编辑失败。', '');
            }
        } else {
            if (modC('NEED_VERIFY', 0) && !is_administrator()) //需要审核且不是管理员
            {
                $content['status'] = 0;
                $tip = '但需管理员审核通过后才会显示在列表中，请耐心等待。';
                $user = query_user(array('nickname'), is_login());
                $admin_uids = explode(',', C('USER_ADMINISTRATOR'));
                foreach ($admin_uids as $admin_uid) {
                    D('Common/Message')->sendMessage($admin_uid, "{$user['nickname']}向专辑投了一份稿件，请到后台审核。", $title = '专辑投稿提醒', U('Admin/Issue/verify'), is_login(), 2);
                }
            }
            $rs = D('IssueContent')->add($content);
            if ($rs) {
                $this->success('投稿成功。' . $tip, 'refresh');
            } else {
                $this->success('投稿失败。', '');
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
        $this->setTitle('{$content.title|op_t}' . '——专辑');
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
            $this->error('抱歉，您不具备投稿权限。');
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