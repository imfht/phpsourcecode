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
        $data = $admin_config->handleConfig();
        if (!$data) {
            $data['LIMIT_IMAGE'] = 10;
            $data['FORUM_BLOCK_SIZE'] = 4;
            $data['CACHE_TIME']=300;
        }

        $admin_config->title('论坛基本设置')
            ->keyInteger('LIMIT_IMAGE', '帖子图片解析数量限制', '超过数量限制就不会被解析出来，不填则默认为10张')
            ->keyInteger('FORUM_BLOCK_SIZE', '论坛板块列表板块所占尺寸', '默认为4,，值可填1到12,共12块，数值代表每个板块所占块数，一行放3个板块则为4，一行放4个板块则为3')
            ->keyInteger('CACHE_TIME','板块数据缓存时间','默认300秒')
            ->buttonSubmit('', '保存')->data($data);
        $admin_config->display();
    }

    public function forum($page = 1, $r = 20)
    {
        //读取数据
        $map = array('status' => array('GT', -1));
        $model = M('Forum');
        $list = $model->where($map)->page($page, $r)->order('sort asc')->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$v) {
            $v['post_count'] = D('ForumPost')->where(array('forum_id' => $v['id']))->count();
        }

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title('板块管理')
            ->buttonNew(U('Forum/editForum'))
            ->setStatusUrl(U('Forum/setForumStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->buttonSort(U('Forum/sortForum'))
            ->keyId()->keyLink('title', '标题', 'Forum/post?forum_id=###')
            ->keyCreateTime()->keyText('post_count', '主题数量')->keyStatus()->keyDoActionEdit('editForum?id=###')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function type()
    {
        $list = D('Forum/ForumType')->getTree();
        $treeBuilder = new AdminTreeListBuilder();

        $treeBuilder->buttonNew(U('addtype'));
        $treeBuilder->title('论坛分类管理')->setLevel(1)
            ->setModel('type')
            ->disableMerge()
            ->disableMove()
            ->data($list);
        $treeBuilder->display();
    }

    public function setTypeStatus($ids=array(),$status=1){
        if(is_array($ids)){
            $map['id']=array('in',implode(',',$ids));
        }else{
            $map['id']=$ids;
        }
        $result= D('Forum/ForumType')->where($map)->setField('status',$status);
        $this->success('设置成功。'.'影响了'.$result.'条记录。');
    }


    public function addType()
    {
        $aId = I('id', 0, 'intval');
        if (IS_POST) {
            $aPid = I('pid', 0, 'intval');
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
                $result = M('ForumType')->save($type);
            } else {
                $result = M('ForumType')->add($type);
            }
            if ($result) {
                $this->success('成功。');
            } else {
                $this->error('出错。');
            }


        }


        $type = M('ForumType')->find($aId);
        if (!$type) {
            $type['status'] = 1;
            $type['sort'] = 1;
        }
        $configBuilder = new AdminConfigBuilder();
        $configBuilder->title('编辑分类');
        $configBuilder->keyId()
            ->keyText('title', '分类名')
            ->keyInteger('sort', '排序')
            ->keyStatus()
            ->buttonSubmit()
            ->buttonBack();


        $configBuilder->data($type);
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
            ->title('板块回收站')
            ->setStatusUrl(U('Forum/setForumStatus'))->buttonRestore()->buttonClear('forum')
            ->keyId()->keyLink('title', '标题', 'Forum/post?forum_id=###')
            ->keyCreateTime()->keyText('post_count', '帖子数量')
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
        $builder->title('贴吧排序')
            ->data($list)
            ->buttonSubmit(U('doSortForum'))->buttonBack()
            ->display();
    }

    public function setForumStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Forum', $ids, $status);
    }

    public function doSortForum($ids)
    {
        $builder = new AdminSortBuilder();
        $builder->doSort('Forum', $ids);
    }

    public function editForum($id = null, $title = '', $create_time = 0, $status = 1, $allow_user_group = 0, $logo = 0)
    {
        if (IS_POST) {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //生成数据
            $data = array('title' => $title, 'create_time' => $create_time, 'status' => $status, 'allow_user_group' => $allow_user_group, 'logo' => $logo
            , 'type_id' => I('type_id', 1, 'intval'), 'background' => I('background', 0, 'intval'), 'description' => I('description', '', 'op_t'));

            //写入数据库
            $model = M('Forum');
            if ($isEdit) {
                $data['id'] = $id;
                $data = $model->create($data);
                $result = $model->where(array('id' => $id))->save($data);
                if (!$result) {
                    $this->error('编辑失败');
                }
            } else {
                $data = $model->create($data);
                $result = $model->add($data);
                if (!$result) {
                    $this->error('创建失败');
                }
            }

            S('forum_list', null);
            //返回成功信息
            $this->success($isEdit ? '编辑成功' : '保存成功');

        } else {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //如果是编辑模式，读取贴吧的属性
            if ($isEdit) {
                $forum = M('Forum')->where(array('id' => $id))->find();
            } else {
                $forum = array('create_time' => time(), 'post_count' => 0, 'status' => 1);
            }
            $types = M('ForumType')->where(array('status' => 1))->select();
            foreach ($types as $t) {
                $type_id_array[$t['id']] = $t['title'];
            }


            //显示页面
            $builder = new AdminConfigBuilder();
            $builder
                ->title($isEdit ? '编辑贴吧' : '新增贴吧')
                ->keyId()->keyTitle()
                ->keyTextArea('description', '板块描述', '可用Html语法')
                ->keySelect('type_id', '分类板块', '选择板块所在分类', $type_id_array)
                ->keyMultiUserGroup('allow_user_group', '允许发帖的用户组')
                ->keySingleImage('logo', '板块图标', '用于显示的封面755px*130px')
                ->keySingleImage('background', '板块背景', '板块背景图')
                ->keyStatus()
                ->keyCreateTime()
                ->data($forum)
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
                $v['top'] = '版内置顶';
            } else if ($v['is_top'] == 2) {
                $v['top'] = '全局置顶';
            } else {
                $v['top'] = '不置顶';
            }
        }
        //读取板块基本信息
        if ($forum_id) {
            $forum = M('Forum')->where(array('id' => $forum_id))->find();
            $forumTitle = ' - ' . $forum['title'];
        } else {
            $forumTitle = '';
        }

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('帖子管理' . $forumTitle)
            ->setStatusUrl(U('Forum/setPostStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyLink('title', '标题', 'Forum/reply?post_id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', '最后回复时间')->key('top', '是否置顶')->keyStatus()->keyDoActionEdit('editPost?id=###')
            ->setSearchPostUrl()->search('标题', 'title')->search('内容', 'content')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
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


        $builder->title('帖子回收站')
            ->setStatusUrl(U('Forum/setPostStatus'))->buttonRestore()->buttonClear('ForumPost')
            ->keyId()->keyLink('title', '标题', 'Forum/reply?post_id=###')
            ->keyCreateTime()->keyUpdateTime()->keyTime('last_reply_time', '最后回复时间')->keyBool('is_top', '是否置顶')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function editPost($id = null, $id = null, $title = '', $content = '', $create_time = 0, $update_time = 0, $last_reply_time = 0, $is_top = 0)
    {
        if (IS_POST) {
            //判断是否为编辑模式
            $isEdit = $id ? true : false;

            //写入数据库
            $model = M('ForumPost');
            $data = array('title' => $title, 'content' => $content, 'create_time' => $create_time, 'update_time' => $update_time, 'last_reply_time' => $last_reply_time, 'is_top' => $is_top);
            if ($isEdit) {
                $result = $model->where(array('id' => $id))->save($data);
            } else {
                $result = $model->keyDoActionEdit($data);
            }
            //如果写入不成功，则报错
            if (!$result) {
                $this->error($isEdit ? '编辑失败' : '创建成功');
            }
            //返回成功信息
            $this->success($isEdit ? '编辑成功' : '创建成功');
        } else {
            //判断是否在编辑模式
            $isEdit = $id ? true : false;

            //读取帖子内容
            if ($isEdit) {
                $post = M('ForumPost')->where(array('id' => $id))->find();
            } else {
                $post = array();
            }

            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? '编辑帖子' : '新建帖子')
                ->keyId()->keyTitle()->keyEditor('content', '内容')->keyRadio('is_top', '置顶', '选择置顶形式', array(0 => '不置顶', 1 => '本版置顶', 2 => '全局置顶'))->keyCreateTime()->keyUpdateTime()
                ->keyTime('last_reply_time', '最后回复时间')
                ->buttonSubmit(U('editPost'))->buttonBack()
                ->data($post)
                ->display();
        }

    }

    public function setPostStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('ForumPost', $ids, $status);
    }

    public function reply($page = 1, $post_id = null, $r = 20)
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

        $builder->title('回复管理')
            ->setStatusUrl(U('setReplyStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()->keyTruncText('content', '内容', 50)->keyCreateTime()->keyUpdateTime()->keyStatus()->keyDoActionEdit('editReply?id=###')
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

        $builder->title('回复回收站')
            ->setStatusUrl(U('setReplyStatus'))->buttonRestore()->buttonClear('ForumPostReply')
            ->keyId()->keyTruncText('content', '内容', 50)->keyCreateTime()->keyUpdateTime()->keyStatus()
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
            $data = array('content' => $content, 'create_time' => $create_time, 'update_time' => $update_time, 'status' => $status);
            $model = M('ForumPostReply');
            if ($isEdit) {
                $result = $model->where(array('id' => $id))->save($data);
            } else {
                $result = $model->add($data);
            }

            //如果写入出错，则显示错误消息
            if (!$result) {
                $this->error($isEdit ? '编辑失败' : '创建失败');
            }

            //返回成功消息
            $this->success($isEdit ? '编辑成功' : '创建成功', U('reply'));

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

            //显示页面
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? '编辑回复' : '创建回复')
                ->keyId()->keyEditor('content', '内容')->keyCreateTime()->keyUpdateTime()->keyStatus()
                ->data($reply)
                ->buttonSubmit(U('editReply'))->buttonBack()
                ->display();
        }

    }

}
