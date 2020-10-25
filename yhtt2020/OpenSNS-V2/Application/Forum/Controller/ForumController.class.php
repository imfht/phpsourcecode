<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminSortBuilder;
use Admin\Builder\AdminTreeListBuilder;

class ForumController extends AdminController
{

    public function index()
    {
        redirect(U('forum'));
    }

    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        if (IS_POST) {
            S('forum_recommand_forum', null);
            S('forum_hot_forum', null);
            S('forum_suggestion_posts', null);
        }
        $data = $admin_config->handleConfig();

        if (!$data) {
            $data['LIMIT_IMAGE'] = 10;
            $data['FORUM_BLOCK_SIZE'] = 4;
            $data['CACHE_TIME'] = 300;
        }


        $admin_config->title(L('_FORUM_SETTINGS_'))
            ->data($data)
            ->keyInteger('LIMIT_IMAGE', L('_POST_PARSE_NUMBER_'), L('_POST_PARSE_NUMBER_VICE_'))
            //->keyInteger('FORUM_BLOCK_SIZE', '论坛板块列表板块所占尺寸', '默认为4,，值可填1到12,共12块，数值代表每个板块所占块数，一行放3个板块则为4，一行放4个板块则为3')
            ->keyInteger('CACHE_TIME', L('_BLOCK_DATA_CACHE_TIME_'), L('_BLOCK_DATA_CACHE_TIME_DEFAULT_'))
            //->keyText('SUGGESTION_POSTS', L('_HOME_RECOMMEND_POST_'))
            ->keyAutoComplete('SUGGESTION_POSTS', L('_HOME_RECOMMEND_POST_'), '', array('url' => U('Forum/getJson'), 'delimiter' => '|'))
            ->keyText('HOT_FORUM', L('_BLOCK_HOT_'), L('_DIVIDE_COMMA_'))->keyDefault('HOT_FORUM', '1,2,3')
            ->keyText('RECOMMAND_FORUM', L('_BLOCK_RECOMMEND_'), L('_DIVIDE_COMMA_'))->keyDefault('RECOMMAND_FORUM', '1,2,3')
            ->keyInteger('FORM_POST_SHOW_NUM_INDEX', L('_FORUM_HOME_PER_PAGE_COUNT_'), '')->keyDefault('FORM_POST_SHOW_NUM_INDEX', '5')
            ->keyInteger('FORM_POST_SHOW_NUM_PAGE', L('_PER_PAGE_COUNT_'), L('_PER_PAGE_COUNT_VICE_') . L('_COMMA_'))->keyDefault('FORM_POST_SHOW_NUM_PAGE', '10')
            ->keyText('FORUM_SHOW_TITLE', L('_TITLE_NAME_'), L('_HOME_BLOCK_TITLE_'))->keyDefault('FORUM_SHOW_TITLE', L('_BLOCK_FORUM_'))
            ->keyText('FORUM_SHOW', L('_BLOCK_SHOW_'), L('_BLOCK_SHOW_TIP_'))
            ->keyText('FORUM_SHOW_CACHE_TIME', L('_CACHE_TIME_'), L('_BLOCK_DATA_CACHE_TIME_DEFAULT_'))->keyDefault('FORUM_SHOW_CACHE_TIME', '600')
            ->keyText('FORUM_POST_SHOW_TITLE', L('_TITLE_NAME_'), L('_HOME_BLOCK_TITLE_'))->keyDefault('FORUM_POST_SHOW_TITLE', L('_POST_HOT_'))
            ->keyText('FORUM_POST_SHOW_NUM', L('_POST_SHOWS_'))->keyDefault('FORUM_POST_SHOW_NUM', 5)
            ->keyRadio('FORUM_POST_ORDER', L('_POST_SORT_FIELD_'), '', array('update_time' => L('_UPDATE_TIME_'), 'last_reply_time' => L('_LAST_REPLY_TIME_'), 'view_count' => L('_VIEWS_'), 'reply_count' => L('_REPLIES_')))->keyDefault('FORUM_POST_ORDER', 'last_reply_time')
            ->keyRadio('FORUM_POST_TYPE', L('_POST_SORT_MODE_'), '', array('asc' => L('_ASC_'), 'desc' => L('_DESC_')))->keyDefault('FORUM_POST_TYPE', 'desc')
            ->keyText('FORUM_POST_CACHE_TIME', L('_BLOCK_SHOW_'), L('_BLOCK_SHOW_TIP_'))->keyDefault('FORUM_POST_CACHE_TIME', '600')
            ->group(L('_SETTINGS_BASIC_'), 'LIMIT_IMAGE,FORUM_BLOCK_SIZE,CACHE_TIME,SUGGESTION_POSTS,HOT_FORUM,RECOMMAND_FORUM,FORM_POST_SHOW_NUM_INDEX,FORM_POST_SHOW_NUM_PAGE')
            ->group(L('_HOME_DISPLAY_BOARD_SETTING_'), 'FORUM_SHOW_TITLE,FORUM_SHOW,FORUM_SHOW_CACHE_TIME')
            ->group(L('_HOME_DISPLAY_POST_SETTINGS_'), 'FORUM_POST_SHOW_TITLE,FORUM_POST_SHOW_NUM,FORUM_POST_ORDER,FORUM_POST_TYPE,NEWS_SHOW_CACHE_TIME');

        $admin_config->buttonSubmit('', L('_SAVE_'))->display();
    }


    public function getJson()
    {

        $aQ = I('q', '', 'text');

        $where['title'] = array('like', '%' . $aQ . '%');
        $where['id'] = $aQ;
        $where['_logic'] = 'or';
        $map['_complex'] = $where;
        $map['status'] = 1;

        $list = M('ForumPost')->where($map)->order('id desc')->field('id,title')->select();
        foreach ($list as &$v) {
            $v['name'] = $v['id'] . '[' . $v['title'] . ']';
            unset($v['title']);
        }
        unset($v);

        $this->ajaxReturn($list);
    }

    public function forum($page = 1, $r = 20)
    {
        //读取数据
        $map = array('status' => array('GT', -1));
        $model = M('Forum');
        $list = $model->where($map)->page($page, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();
        $groups = M('AuthGroup')->select();
        foreach ($groups as $v) {
            $group_name[$v['id']] = $v['title'];
        }
        foreach ($list as &$v) {
            $v['post_count'] = D('ForumPost')->where(array('forum_id' => $v['id']))->count();
            $user_group_ids = explode(',', $v['allow_user_group']);
            foreach ($user_group_ids as &$gid) {
                $gid = $group_name[$gid];
            }
            $v['allow_group_text'] = implode('、', $user_group_ids);
        }

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title(L('_BLOCK_MANAGE_'))
            ->buttonNew(U('Forum/editForum'))
            ->setStatusUrl(U('Forum/setForumStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->buttonSort(U('Forum/sortForum'))
            ->keyId()->keyLink('title', L('_TITLE_'), 'Forum/post?forum_id=###')
            ->keyText('allow_group_text', '允许发帖的权限组')
            ->keyCreateTime()->keyText('post_count', L('_THEME_COUNT_'))->keyStatus()->keyDoActionEdit('editForum?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function type()
    {
        $list = D('Forum/ForumType')->getTree();
        $map = array('status' => array('GT', -1), 'type_id' => array('gt', 0));
        $forums = M('Forum')->where($map)->order('sort asc')->field('id as forum_id,title,sort,type_id as pid,status')->select();
        $list = array_merge($list, $forums);
        $list = list_to_tree($list, 'id', 'pid', 'child', 0);
        $this->assign('list', $list);
        $this->display(T('Application://Forum@Forum/type'));
    }

    public function setTypeStatus($ids = array(), $status = 1, $temp = 0)
    {
        $result = null;
        $map['id'] = $ids;
        if ($temp) {
            $result = M('Forum')->where($map)->setField('status', $status);
        } else {
            $result = D('Forum/ForumType')->where($map)->setField('status', $status);
        }
        $this->success(L('_SUCCESS_SETTING_') . L('_PERIOD_') . L('_SUCCESS_EFFECT_') . $result . L('_SUCCESS_RECORD_') . L('_PERIOD_'));
    }

    public function addType()
    {
        $aId = I('id', 0, 'intval');
        $aPid = I('pid', 0, 'intval');
        if (IS_POST) {
            $aPid = I('get.pid', 0, 'intval');
            $aSort = I('sort', 0, 'intval');
            $aStatus = I('status', -2, 'intval');
            $aTitle = I('title', '', 'op_t');
            if ($aId != 0)
                $type['id'] = $aId;

            $type['sort'] = $aSort;
            $type['pid'] = $aPid;
            if ($aStatus != -2)
                $type['status'] = $aStatus;
            $type['title'] = $aTitle;
            if ($aId != 0) {
                if ($aPid == 0) {
                    $result = M('ForumType')->save($type);
                } else {
                    $result = M('Forum')->save($type);
                }
                //$result = M('ForumType')->save($type);
            } else {
                $result = M('ForumType')->add($type);
            }
            if ($result) {
                $this->success(L('_SUCCESS_OPERATE_') . L('_EXCLAMATION_'));
            } else {
                $this->error(L('_FAIL_OPERATE_') . L('_EXCLAMATION_'));
            }


        }
        if ($aPid == 0) {
            $type = M('ForumType')->find($aId);
        } else {
            $type = M('Forum')->find($aId);
        }
        //$type = M('ForumType')->find($aId);
        if (!$type) {
            $type['status'] = 1;
            $type['sort'] = 1;
        }
        $configBuilder = new AdminConfigBuilder();
        $configBuilder->title(L('_CATEGORY_EDIT_'));
        $configBuilder->keyId()
            ->keyText('title', L('_CATEGORY_NAME_'))
            ->keyInteger('sort', L('_SORT_'))
            ->keyStatus()
            ->data($type)
            ->buttonSubmit()
            ->buttonBack();


        //$configBuilder->data($type);
        $configBuilder->display();

    }

    public function forumTrash($page = 1, $r = 20, $model = '')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取回收站中的数据
        $map = array('status' => '-1');
        $model = M('Forum');
        $list = $model->where($map)->page($page, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder
            ->title(L('_BLOCK_TRASH_'))
            ->setStatusUrl(U('Forum/setForumStatus'))->buttonRestore()->buttonClear('forum')
            ->keyId()->keyLink('title', L('_TITLE_'), 'Forum/post?forum_id=###')
            ->keyCreateTime()->keyText('post_count', L('_POST_NUMBER_'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function sortForum()
    {
        //读取贴吧列表
        $list = M('Forum')->where(array('status' => array('EGT', 0)))->order('sort asc')->select();

        //显示页面
        $builder = new AdminSortBuilder();
        $builder->title(L('_POST_BAR_SORT_'))
            ->data($list)
            ->buttonSubmit(U('doSortForum'))->buttonBack()
            ->display();
    }

    public function setForumStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Forum', $ids, $status);
        D('Forum/Forum')->cleanAllForumsCache();

    }

    public function doSortForum($ids)
    {
        $builder = new AdminSortBuilder();
        $builder->doSort('Forum', $ids);
        D('Forum/Forum')->cleanAllForumsCache();
    }

    public function editForum($id = null, $title = '', $create_time = 0, $status = 1, $allow_user_group = 0, $logo = 0, $type_id = 0)
    {
        if (IS_POST) {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;
            $model = M('Forum');
            if (I('quick_edit', 0, 'intval')) {
                //生成数据
                $data = array('title' => $title, 'sort' => I('sort', 0, 'intval'));
                //写入数据库
                $result = $model->where(array('id' => $id))->save($data);
                if ($result === false) {
                    $this->error(L('_FAIL_EDIT_'));
                }
            } else {
                //生成数据
                $data = array('title' => $title, 'create_time' => $create_time, 'status' => $status, 'allow_user_group' => $allow_user_group, 'logo' => $logo, 'admin' => I('admin', 1, 'intval')
                , 'type_id' => I('type_id', 1, 'intval'), 'background' => I('background', 0, 'intval'), 'description' => I('description', '', 'op_t'));

                if ($data['title'])
                //写入数据库
                if ($isEdit) {
                    if ($data['title']==''){
                        $this->error('标题不能为空');
                    }
                    if ($data['admin']==''){
                        $this->error('版主不能为空');
                    }
                    $data['id'] = $id;
                    $data = $model->create($data);
                    $result = $model->where(array('id' => $id))->save($data);
                    if ($result === false) {
                        $this->error(L('_FAIL_EDIT_'));
                    }
                } else {
                    if ($data['title']==''){
                        $this->error('标题不能为空');
                    }
                    if ($data['admin']==''){
                        $this->error('版主不能为空');
                    }
                    $data = $model->create($data);
                    $result = $model->add($data);
                    if (!$result) {
                        $this->error(L('_ERROR_CREATE_FAIL_'));
                    }
                }
            }
            S('forum_list', null);
            D('Forum/Forum')->cleanAllForumsCache();
            //返回成功信息
            $this->success($isEdit ? L('_SUCCESS_EDIT_') : L('_SUCCESS_SAVE_'));
        } else {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //如果是编辑模式，读取贴吧的属性
            if ($isEdit) {
                $forum = M('Forum')->where(array('id' => $id))->find();
            } else {
                $forum = array('create_time' => time(), 'post_count' => 0, 'status' => 1, 'type_id' => $type_id);
            }
            $types = M('ForumType')->where(array('status' => 1))->select();
            $type_id_array[0] = L('_NO_CATEGORY_');
            foreach ($types as $t) {
                $type_id_array[$t['id']] = $t['title'];
            }
            //显示页面
            $builder = new AdminConfigBuilder();
            $builder
                ->title($isEdit ? L('_POST_BAR_EDIT_') : L('_POST_BAR_ADD_'))
                ->data($forum)
                ->keyId()->keyTitle()
                ->keyText('admin', L('_BOARD_MASTER_'), '只能输入一个uid，如：1')
                ->keyTextArea('description', L('_BOARD_DESC_'), L('_BOARD_DESC_VICE_'))
                ->keySelect('type_id', L('_BOARD_CATEGORY_'), L('_BOARD_CATEGORY_VICE_'), $type_id_array)
                ->keyMultiUserGroup('allow_user_group', L('_USER_GROUP_TO_POST_'))
                ->keySingleImage('logo', L('_BOARD_ICON_'), L('_BOARD_ICON_VICE_'))
                ->keySingleImage('background', L('_BOARD_BG_'), L('_BOARD_BG_VICE_'))
                ->keyStatus()
                ->keyCreateTime()
                ->buttonSubmit(U('editForum'))->buttonBack()
                ->display();
        }

    }


    public function post($page = 1, $forum_id = null, $r = 20, $title = '', $content = '')
    {
        //读取帖子数据
        $map = array('status' => array('EGT', 0));
        if ($title != '') {
            $map['title'] = array('like', '%' . $title . '%');
        }
        if ($content != '') {
            $map['content'] = array('like', '%' . $content . '%');
        }
        if ($forum_id) $map['forum_id'] = $forum_id;
        $model = M('ForumPost');
        $list = $model->where($map)->order('last_reply_time desc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$v) {
            if ($v['is_top'] == 1) {
                $v['top'] = L('_STICK_IN_BLOCK_');
            } else if ($v['is_top'] == 2) {
                $v['top'] = L('_STICK_GLOBAL_');
            } else {
                $v['top'] = L('_STICK_NOT_');
            }
        }
        //读取板块基本信息
        if ($forum_id) {
            $forum = M('Forum')->where(array('id' => $forum_id))->find();
            $forumTitle = ' - ' . $forum['title'];
        } else {
            $forumTitle = '';
        }

        $forum1 = M('Forum')->field('id,title')->select();
        array_unshift($forum1, array('id' => '', 'title' => '全部'));
        for ($i = 0; $i < count($forum1); $i++) {
            $forum1[$i]['value'] = $forum1[$i]['title'];
        }
        unset($i);
//dump($forum1);exit;
        //显示后台页面T('Forum@default/Forum/changePlate'
        $builder = new AdminListBuilder();
        $builder->title(L('_POST_MANAGE_') . $forumTitle)
            ->setStatusUrl(U('Forum/setPostStatus'))->buttonEnable()->buttonDisable()->buttonDelete()->buttonModalPopup(U('Forum/changePlate', array('forum_id' => $map['forum_id'])), array(), L('_MIGRATING_NOTE_'), array('data-title' => L('_MIGRATING_NOTE_TO_ANOTHER_PLATE_'), 'target-form' => 'ids'))
            ->selectPlateForm('spf', get, U('Admin/Forum/post'))->select($title = '筛选：', $name = 'forum_id', 'select', '', '', '', $forum1)
            ->keyId()->keyLink('title', L('_TITLE_'), 'Forum/index/detail?id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', L('_LAST_REPLY_TIME_'))->key('top', L('_STICK_YES_OR_NOT_'), 'text')->keyStatus()->keyDoActionEdit('editPost?id=###')
            ->setSearchPostUrl(U('Admin/Forum/post'))->search(L('_TITLE_'), 'title')->search(L('_CONTENT_'), 'content')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function changePlate()
    {
        if (IS_POST) {
            $aIds = I('post.ids');
            $aForum = I('post.forum', 0, 'intval');
            $ids = explode(',', $aIds);
            $map['id'] = array('in', $ids);
            $data['forum_id'] = $aForum;
            $res = M('forum_post')->where($map)->save($data);
            if ($res === false) {
                $result['info'] = L('_OPERATION_FAILED_');
            } else {
                $result['status'] = 1;
            }
            $this->ajaxReturn($result);

        } else {
            $aIds = I('get.ids');
            $ids = implode(',', $aIds);
            $map['status'] = 1;
            $forum_list = M('forum')->where($map)->field('id,title as value')->select();
            $this->assign('forum_list', $forum_list);
            $this->assign('ids', $ids);
            $this->display(T('Application://Forum@Forum/changeplate'));
        }

    }

    public function postTrash($page = 1, $r = 20)
    {
        //显示页面
        $builder = new AdminListBuilder();
        $builder->clearTrash('ForumPost');
        //读取帖子数据
        $map = array('status' => -1);
        $model = M('ForumPost');
        $list = $model->where($map)->order('last_reply_time desc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();


        $builder->title(L('_REPLY_VIEW_MORE_'))
            ->setStatusUrl(U('Forum/setPostStatus'))->buttonRestore()->buttonClear('ForumPost')
            ->keyId()->keyLink('title', L('_TITLE_'), 'Forum/reply?post_id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', L('_LAST_REPLY_TIME_'))->keyBool('is_top', L('_STICK_YES_OR_NOT_'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function editPost($id = null,  $title = '', $content = '', $create_time = 0, $update_time = 0, $last_reply_time = 0, $is_top = 0)
    {

        if (IS_POST) {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //写入数据库
            $model = M('ForumPost');
            $data = array('title' => $title, 'content' => filter_content($content), 'create_time' => $create_time, 'update_time' => $update_time, 'last_reply_time' => $last_reply_time, 'is_top' => $is_top);
            if ($isEdit) {
                $result = $model->where(array('id' => $id))->save($data);
            } else {
                $result = $model->keyDoActionEdit($data);
            }
            //如果写入不成功，则报错
            if ($result === false) {
                $this->error($isEdit ? L('_FAIL_EDIT_') : L('_TIP_CREATE_SUCCESS_'));
            }
            //返回成功信息
            $this->success($isEdit ? L('_SUCCESS_EDIT_') : L('_TIP_CREATE_SUCCESS_'));
        } else {
            //判断是否在编辑模式

            $isEdit = $id ? true : false;

            //读取帖子内容
            if ($isEdit) {
                $post = M('ForumPost')->where(array('id' => $id))->find();
            } else {
                $post = array();
            }
            //编辑器配置
            $config = get_editor_config('FORUM_ADMIN_ADD', '', 1) ;
            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? L('_POST_EDIT_') : L('_POST_ADD_'))
                ->keyId()->keyTitle()->keyEditor('content', L('_CONTENT_'), '', $config)->keyRadio('is_top', L('_STICK_'), L('_STICK_STYLE_SELECT_'), array(0 => L('_STICK_NOT_'), 1 => L('_STICK_IN_BLOCK_'), 2 => L('_STICK_GLOBAL_')))->keyCreateTime()->keyUpdateTime()
                ->keyTime('last_reply_time', L('_LAST_REPLY_TIME_'))
                ->buttonSubmit(U('editPost'))->buttonBack()
                ->data($post)
                ->display();
        }

    }

    public function showPlate($ids)
    {

        $id = array_unique((array)$ids);


    }

    public function setPostStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('ForumPost', $ids, $status);
    }


    public function replymanager($page = 1, $post_id = null, $r = 20)
    {
        $builder = new AdminListBuilder();

        //读取回复列表
        $map = array('status' => array('EGT', 0));
        if ($post_id) $map['post_id'] = $post_id;
        $model = M('ForumPostReply');
        $list = $model->where($map)->order('create_time asc')->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$reply) {
            $reply['content'] = op_t($reply['content']);
        }
        unset($reply);
        //显示页面

        $builder->title(L('_REPLY_MANAGER_'))
            ->setStatusUrl(U('setReplyStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyTruncText('content', L('_CONTENT_'), 50)->keyCreateTime()->keyUpdateTime()->keyStatus()->keyDoActionEdit('editReply?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function replyTrash($page = 1, $r = 20, $model = '')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取回复列表
        $map = array('status' => -1);
        $model = M('ForumPostReply');
        $list = $model->where($map)->order('create_time asc')->page($page, $r)->select();
        foreach ($list as &$reply) {
            $reply['content'] = op_t($reply['content']);
        }
        unset($reply);
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder->title(L('_REPLY_TRASH_'))
            ->setStatusUrl(U('setReplyStatus'))->buttonRestore()->buttonClear('ForumPostReply')
            ->keyId()->keyTruncText('content', L('_CONTENT_'), 50)->keyCreateTime()->keyUpdateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setReplyStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('ForumPostReply', $ids, $status);
    }

    public function editReply($id = null, $content = '', $create_time = 0, $update_time = 0, $status = 1)
    {
        if (IS_POST) {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //写入数据库
            $data = array('content' => filter_content($content), 'create_time' => $create_time, 'update_time' => $update_time, 'status' => $status);
            $model = M('ForumPostReply');
            if ($isEdit) {
                $result = $model->where(array('id' => $id))->save($data);
            } else {
                $result = $model->add($data);
            }

            //如果写入出错，则显示错误消息
            if ($result === false) {
                $this->error($isEdit ? L('_FAIL_EDIT_') : L('_TIP_CREATE_SUCCESS_'));
            }
            //返回成功信息
            $this->success($isEdit ? L('_SUCCESS_EDIT_') : L('_TIP_CREATE_SUCCESS_'), U('reply'));

        } else {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //读取回复内容
            if ($isEdit) {
                $model = M('ForumPostReply');
                $reply = $model->where(array('id' => $id))->find();
            } else {
                $reply = array('status' => 1);
            }
            $config = get_editor_config('FORUM_ADMIN_REPLY' , '', 1) ;
            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? L('_REPLY_EDIT_') : L('_REPLY_CREATE_'))
                ->keyId()->keyEditor('content', L('_CONTENT_'), '', $config)->keyCreateTime()->keyUpdateTime()->keyStatus()
                ->data($reply)
                ->buttonSubmit(U('editReply'))->buttonBack()
                ->display();
        }

    }


}
