<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


class IssueController extends AdminController
{
    protected $issueModel;

    function _initialize()
    {
        $this->issueModel = D('Issue/Issue');
        parent::_initialize();
    }

    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();
        $data['NEED_VERIFY'] = $data['NEED_VERIFY'] ? $data['NEED_VERIFY'] : 0;
        $data['DISPLAY_TYPE'] = $data['DISPLAY_TYPE'] ? $data['DISPLAY_TYPE'] : 'list';
        $data['ISSUE_SHOW_TITLE'] = $data['ISSUE_SHOW_TITLE'] ? $data['ISSUE_SHOW_TITLE'] : L('_ISSUE_HOTTEST_');
        $data['ISSUE_SHOW_COUNT'] = $data['ISSUE_SHOW_COUNT'] ? $data['ISSUE_SHOW_COUNT'] : 4;
        $data['ISSUE_SHOW_ORDER_FIELD'] = $data['ISSUE_SHOW_ORDER_FIELD'] ? $data['ISSUE_SHOW_ORDER_FIELD'] : 'view_count';
        $data['ISSUE_SHOW_ORDER_TYPE'] = $data['ISSUE_SHOW_ORDER_TYPE'] ? $data['ISSUE_SHOW_ORDER_TYPE'] : 'desc';
        $data['ISSUE_SHOW_CACHE_TIME'] = $data['ISSUE_SHOW_CACHE_TIME'] ? $data['ISSUE_SHOW_CACHE_TIME'] : '600';
        $admin_config->title(L('_ISSUE_BASIC_SETTINGS_'))
            ->keyBool('NEED_VERIFY', L('_AUDIT_CONTRIBUTE_'), L('_AUDIT_DEFAULT_NO_NEED_'))
            ->keyRadio('DISPLAY_TYPE', L('_DISPLAY_DEFAULT_'), L('_DISPLAY_DEFAULT_VICE_'),array('list'=>L('_LIST_'),'masonry'=>L('_MASONRY_')))
            ->buttonSubmit('', L('_SAVE_'))->data($data);
        $admin_config->keyText('ISSUE_SHOW_TITLE', L('_TITLE_NAME_'), L('_TITLE_NAME_VICE_'));
        $admin_config->keyText('ISSUE_SHOW_COUNT', L('_ISSUE_SHOW_NUMBER_'), L('_ISSUE_SHOW_NUMBER_VICE_'));
        $admin_config->keyRadio('ISSUE_SHOW_ORDER_FIELD', L('_SORT_VALUE_'), L('_TIP_SORT_TYPE_'), array('view_count' => L('_VIEWS2_'), 'reply_count' => L('_REPLIES_'), 'create_time' => L('_PUBLISH_TIME_'), 'update_time' => L('_UPDATE_TIME_')));
        $admin_config->keyRadio('ISSUE_SHOW_ORDER_TYPE', L('_SORT_TYPE_'), L('_TIP_SORT_TYPE_'), array('desc' => L('_COUNTER_'), 'asc' => L('_DIRECT_')));
        $admin_config->keyText('ISSUE_SHOW_CACHE_TIME', L('_CACHE_TIME_'), L('_TIP_CACHE_TIME_'));
        $admin_config->group(L('_BASIC_CONF_'), 'NEED_VERIFY,DISPLAY_TYPE')->group(L('_HOME_SHOW_CONF_'), 'ISSUE_SHOW_COUNT,ISSUE_SHOW_TITLE,ISSUE_SHOW_ORDER_TYPE,ISSUE_SHOW_ORDER_FIELD,ISSUE_SHOW_CACHE_TIME');

        $admin_config->groupLocalComment(L('_LOCAL_COMMENT_CONF_'),'issueContent');



        $admin_config->display();
    }

    public function issue()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $attr1 = $attr;
        $attr1['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 1));
        $attr0 = $attr;
        $attr0['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 0));
        $tree = D('Issue/Issue')->getTree(0, 'id,title,sort,pid,status');
        $builder->title(L('_ISSUE_MANAGE_'))
            ->buttonNew(U('Issue/add'))
            ->data($tree)
            ->display();
    }

    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            $issue = $this->issueModel->create();
            if($issue == false){
                $this->error($this->issueModel->getError());
            }
            if ($id != 0) {
                if ($this->issueModel->save($issue)) {
                    $this->success(L('_SUCCESS_EDIT_'));
                } else {
                    $this->error(L('_FAIL_EDIT_'));
                }
            } else {
                if ($this->issueModel->add($issue)) {
                    $this->success(L('_SUCCESS_ADD_'));
                } else {
                    $this->error(L('_FAIL_ADD_'));
                }
            }


        } else {
            $builder = new AdminConfigBuilder();
            $issues = $this->issueModel->select();
            $opt = array();
            foreach ($issues as $issue) {
                $opt[$issue['id']] = $issue['title'];
            }
            if ($id != 0) {
                $issue = $this->issueModel->find($id);
            } else {
                $issue = array('pid' => $pid, 'status' => 1);
            }


            $builder->title(L('_CATEGORY_ADD_'))->keyId()->keyText('title', L('_TITLE_'))->keySelect('pid',L('_FATHER_CLASS_'), L('_FATHER_CLASS_SELECT_'), array('0' =>L('_TOP_CLASS_')) + $opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($issue)
                ->buttonSubmit(U('Issue/add'))->buttonBack()->display();
        }

    }

    public function issueTrash($page = 1, $r = 20, $model = '')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $model = $this->issueModel;
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder->title(L('_ISSUE_TRASH_'))
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear('Issue/Issue')
            ->keyId()->keyText('title', L('_TITLE_'))->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function operate($type = 'move', $from = 0)
    {
        $builder = new AdminConfigBuilder();
        $from = D('Issue')->find($from);

        $opt = array();
        $issues = $this->issueModel->select();
        foreach ($issues as $issue) {
            $opt[$issue['id']] = $issue['title'];
        }
        if ($type === 'move') {

            $builder->title(L('_CATEGORY_MOVE_'))->keyId()->keySelect('pid',L('_FATHER_CLASS_'), L('_FATHER_CLASS_SELECT_'), $opt)->buttonSubmit(U('Issue/add'))->buttonBack()->data($from)->display();
        } else {

            $builder->title(L('_CATEGORY_COMBINE_'))->keyId()->keySelect('toid', L('_CATEGORY_T_COMBINE_'), L('_CATEGORY_T_COMBINE_SELECT_'), $opt)->buttonSubmit(U('Issue/doMerge'))->buttonBack()->data($from)->display();
        }

    }

    public function doMerge($id, $toid)
    {
        $effect_count = D('IssueContent')->where(array('issue_id' => $id))->setField('issue_id', $toid);
        D('Issue')->where(array('id' => $id))->setField('status', -1);
        $this->success(L('_SUCCESS_CATEGORY_COMBINE_') . $effect_count . L('_CONTENT_GE_'), U('issue'));
        //TODO 实现合并功能 issue
    }

    public function contents($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 1);
        $model = M('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        $builder->title(L('_CONTENT_MANAGE_'))
            ->setStatusUrl(U('setIssueContentStatus'))->buttonDisable('', L('_AUDIT_UNSUCCESS_'))->buttonDelete()
            ->keyId()->keyLink('title', L('_TITLE_'), 'Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function verify($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 0);
        $model = M('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        $builder->title(L('_CONTENT_AUDIT_'))
            ->setStatusUrl(U('setIssueContentStatus'))->buttonEnable('', L('_AUDIT_SUCCESS_'))->buttonDelete()
            ->keyId()->keyLink('title', L('_TITLE_'), 'Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setIssueContentStatus()
    {
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        if ($status == 1) {
            foreach ($ids as $id) {
                $content = D('IssueContent')->find($id);
                D('Common/Message')->sendMessage($content['uid'],$title = L('_MESSAGE_AUDIT_ISSUE_CONTENT_'), L('_MESSAGE_AUDIT_ISSUE_CONTENT_VICE_'),  'Issue/Index/issueContentDetail', array('id' => $id), is_login(), 2);
                /*同步微博*/
                /*  $user = query_user(array('nickname', 'space_link'), $content['uid']);
                  $weibo_content = '管理员审核通过了@' . $user['nickname'] . ' 的内容：【' . $content['title'] . '】，快去看看吧：' ."http://$_SERVER[HTTP_HOST]" .U('Issue/Index/issueContentDetail',array('id'=>$content['id']));
                  $model = D('Weibo/Weibo');
                  $model->addWeibo(is_login(), $weibo_content);*/
                /*同步微博end*/
            }

        }
        $builder->doSetStatus('IssueContent', $ids, $status);

    }

    public function contentTrash($page = 1, $r = 10, $model = '')
    {
        //读取微博列表
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        $map = array('status' => -1);
        $model = D('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder->title(L('_CONTENT_TRASH_'))
            ->setStatusUrl(U('setIssueContentStatus'))->buttonRestore()->buttonClear('IssueContent')
            ->keyId()->keyLink('title', L('_TITLE_'), 'Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }
}
