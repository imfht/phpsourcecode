<?php
/**
 *
 */

namespace Group\Controller;

use Think\Controller;
use Weibo\Api\WeiboApi;

define('TOP_ALL', 2);
define('TOP_Group', 1);

class IndexController extends GroupController
{


    public function _initialize()
    {
        parent::_initialize();
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url', 'icons_html'), is_login());
        $this->assign('myInfo', $myInfo);
    }


    public function index($page = 1, $r = 20)
    {
        $page = intval($page);
        $r = intval($r);
        $order = 'create_time desc';
        //获取列表
        $group_list = D('Group/Group')->where(array('status' => 1))->page($page, $r)->order($order)->field('id')->select();
        $list_ids = getSubByKey($group_list, 'id');
        $group_list = $this->getGroupByIds($list_ids);
        //获取总数
        $totalCount = D('Group/Group')->where(array('status' => 1))->count();
        $this->assign('totalCount', $totalCount);
        $this->assign('r', $r);

        $this->assign('group_list', $group_list);

        $this->getGroupTypes();
        $this->setTitle('群组首页');
        $this->display();

    }

    public function mygroup($page = 1, $r = 20)
    {
        $page = intval($page);
        $r = intval($r);

        $this->requireLogin();
        $member = D('GroupMember')->where(array('uid' => is_login(), 'status' => 1))->field('group_id')->select();
        $group_ids = getSubByKey($member, 'group_id');
        $myattend = D('Group')->where(array('id' => array('in', $group_ids), 'status' => 1))->page($page, $r)->order('uid = ' . is_login() . ' desc ,uid asc')->field('id')->select();
        $ids = getSubByKey($myattend, 'id');
        $myattend = $this->getGroupByIds($ids);
        $totalCount = D('Group')->where(array('id' => array('in', $group_ids), 'status' => 1))->count();
        $this->assign('totalCount', $totalCount);
        $this->assign('r', $r);

        $this->assign('mygroup', $myattend);
        $this->assign('current', 'mygroup');
        $this->setTitle('我的群组');
        $this->display();

    }


    /**某个版块的帖子列表
     * @param int $id
     * @param int $page
     * @param string $order
     * @auth 陈一枭
     */
    public function group($id = 0, $page = 1, $order = '', $type = 'new', $r = 20, $title = '', $cate = '')
    {
        $id = intval($id);
        $page = intval($page);
        $order = op_t($order);
        $type = op_t($type);
        $r = intval($r);
        $title = op_t($title);
        $cate = intval($cate);
        //检查可视权限
        $this->requireGroupAllowView($id);
        D('GroupMember')->where(array('group_id' => $id, 'uid' => is_login()))->setField('last_view', time());
        //获取公告
        $this->getNotice($id);
        if ($order == 'ctime') {
            $this->assign('order', 0);
            $order = 'create_time desc';
            $type = 'post';
        } else if ($order == 'reply') {
            $order = 'last_reply_time desc';
            $type = 'post';
            $this->assign('order', 1);
        }
        //读取置顶列表
        $list_top = D('GroupPost')->where('status=1 AND is_top=1 and group_id=' . $id)->order($order)->select();
        foreach ($list_top as &$v) {
            $v['group'] = $this->getGroup($v['group_id']);

        }
        unset($v);
        // 按名称查询帖子
        if ($title != '') {
            $type = 'post';
            $map['title'] = array('like', "%{$title}%");
            $this->assign('search_key', $title);
        }
        //按分类查询帖子
        if ($cate != 0) {
            $type = 'post';
            $map['cate_id'] = $cate;
        }
        //帖子页面显示
        if ($type == 'post') {
            $r = 10;
            //读取帖子列表
            $map['status'] = 1;
            $map['group_id'] = $id;
            empty($order) &&  $order = 'create_time desc';
            $list = D('GroupPost')->where($map)->order($order)->page($page, $r)->select();
            $totalCount = D('GroupPost')->where($map)->count();
            foreach ($list as &$v) {
                $v['group'] = $this->getGroup($v['group_id']);
                $v['category'] = $this->getPostCateName($v['cate_id']);
            }
            unset($v);
        }

        if ($type == 'new') {

            $map = array('group_id' => $id);
            $list = D('GroupDynamic')->where($map)->order('create_time desc')->page($page, $r)->select();
            $totalCount = D('CircleDynamic')->where($map)->count();
        }

        //member页面显示
        if ($type == 'member') {
            $map = array('group_id' => $id);
            $list = D('GroupMember')->where($map)->order('create_time asc')->select();
            foreach ($list as &$user) {
                $user['user'] = query_user(array('avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['uid']);
                $user['isCreator'] = checkIsCreator($user['uid'], 'Group', $user['group_id']);
            }
            //获取创始人
            $creator = array_shift($list);
            $this->assign('creator', $creator);
        }
        $this->assign('list', $list);
        $this->assign('totalCount', $totalCount);
        //显示页面
        $this->assign('group_id', $id);
        if ($id != 0) {
            $group = $this->getGroup($id);
            $this->setTitle('{$group.title}');
            $this->assign('group', $group);
        } else {
            $this->setTitle('群组');
            $this->assign('group', array('title' => '群组 Group'));
        }
        $this->assignAllowPublish();
        $this->assign('list_top', $list_top);
        $this->getPostCategory($id);
        $this->assign('r', $r);
        $this->assign('type', $type);
        $this->display();
    }

    public function groups($order = 'create_time', $cate = 0, $page = 1, $r = 20, $keywords = '', $uid = 0)
    {

        $order = op_t($order);
        $cate = intval($cate);
        $page = intval($page);
        $r = intval($r);
        $keywords = op_t($keywords);
        $uid = intval($uid);


        if ($order == 'activity') {
            $order = 'activity desc';
            $this->assign('order', '按活跃度排序');
        } elseif ($order == 'member') {
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $count = $Model->query("SELECT group_id,count(group_id) as count from opensns_group_member group by group_id order by count desc");
            $ids = getSubByKey($count, 'group_id');
            $ids = implode(',', $ids);
            $order = "find_in_set( id ,'" . $ids . "') ";
            $this->assign('order', '按成员数排序');
        } else {
            $order = 'create_time desc';
            $this->assign('order', '按最新创建排序');
        }


        if ($cate != 0) {

            $gid = D('group_type')->where('pid='.$cate)->field('id')->select();
            $gids=getSubByKey($gid,'id');
            $gids[]=$cate;
            $where['type_id'] = array('in',$gids);
            $this->assign('name', $this->getGroupCateByTypeId($cate));

            $this->assign('group_cate', $cate);
            $this->assign('keyword', array(0 => 'cate', 1 => $cate));
            $this->setTitle('{$name}');
        }
        if ($keywords != '') {
            $where['title'] = array('like', '%' . $keywords . '%');
            $this->assign('name', $keywords);
            $this->assign('keyword', array(0 => 'keywords', 1 => $keywords));
        }
        if ($uid != 0) {
            $where['uid'] = $uid;
            $this->assign('name', get_nickname($uid) . '的群组');
            $this->assign('keyword', array(0 => 'uid', 1 => $uid));
        }
        $where['status'] = 1;
        $group_list = D('Group/Group')->where($where)->page($page, $r)->order($order)->field('id')->select();
        $group_ids = getSubByKey($group_list, 'id');
        $group_list = $this->getGroupByIds($group_ids);
        $totalCount = D('Group/Group')->where($where)->order('sort asc')->count();
        $this->assign('group_list', $group_list);
        $this->getGroupTypes();
        $this->assign('totalCount', $totalCount);
        $this->assign('r', $r);
        $this->display();
    }

    public function posts($post_cate = 0)
    {

        $post_cate = intval($post_cate);

        if ($post_cate != 0) {
            $where['cate_id'] = $post_cate;
            $this->assign('name', $this->getPostCateName($post_cate));
            $this->assign('postCate', $post_cate);
        }
        $where['status'] = 1;
        $post_list = D('Group/GroupPost')->where($where)->select();
        foreach ($post_list as &$val) {
            $val['group'] = $this->getGroup($val['group_id']);

        }
        $this->assign('list', $post_list);
        $this->getPostCategory();

        $this->display();
    }

    public function detail($id = 0, $page = 1, $sr = null, $sp = 1)
    {
        $id = intval($id);
        $page = intval($page);
        $sr = intval($sr);
        $sp = intval($sp);
        $limit = 10;
        //读取帖子内容
        $post = D('GroupPost')->where(array('id' => $id, 'status' => 1))->find();

        $post['group'] = $this->getGroup($post['group_id']);
        $post['category'] = $this->getPostCateName($post['cate_id']);
        $this->getNotice($post['group_id']);
        //检测群组、帖子是否存在
        if (!$post || !$this->isGroupExists($post['group_id'])) {
            $this->error('找不到该帖子');
        }
        $post['content'] = op_h($post['content'], 'html');


        $post['attachment'] = explode(',',$post['attachment']);
        $attach = D('file')->where(array('id'=>array('in',$post['attachment'] )))->field('id,name')->select();
        $post['attachment'] = $attach;
        //增加浏览次数
        D('GroupPost')->where(array('id' => $id))->setInc('view_count');
        //读取回复列表
        $map = array('post_id' => $id, 'status' => 1);
        $replyList = D('GroupPostReply')->getReplyList($map, 'create_time', $page, $limit);

        $replyTotalCount = D('GroupPostReply')->where($map)->count();
        //判断是否需要显示1楼
        if ($page == 1) {
            $showMainPost = true;
        } else {
            $showMainPost = false;
        }
        foreach ($replyList as &$reply) {
            $reply['content'] = op_h($reply['content'], 'html');

        }
        unset($reply);
        //判断是否已经收藏
        $isBookmark = D('GroupBookmark')->exists(is_login(), $id);

        $is_allow = $this->isAllowEditGroup($post['group_id']);

        $this->assign('is_allow', $is_allow);
        //显示页面
        $this->assign('group_id', $post['group_id']);
        $this->assignAllowPublish();
        $this->assign('isBookmark', $isBookmark);
        $this->assign('post', $post);
        $this->setTitle('{$post.title|op_t} —— 贴吧');
        $this->assign('limit', $limit);
        $this->assign('sr', $sr);
        $this->assign('sp', $sp);
        $this->assign('page', $page);
        $this->assign('replyList', $replyList);
        $this->assign('replyTotalCount', $replyTotalCount);
        $this->assign('showMainPost', $showMainPost);
        $this->assign('group', $this->getGroup($post['group_id']));

        $this->display();
    }

    public function delPostReply($id)
    {
        $id = intval($id);
        $this->requireLogin();
        $this->requireCanDeletePostReply($id);
        $res = D('GroupPostReply')->delPostReply($id);
        $res && $this->success($res);
        !$res && $this->error('');
    }

    public function editReply($reply_id = 0)
    {
        $reply_id = intval($reply_id);
        if ($reply_id) {
            $reply = D('group_post_reply')->where(array('id' => $reply_id, 'status' => 1))->find();
        } else {
            $this->error('参数出错！');
        }
        $this->setTitle('编辑回复 —— 群组');
        //显示页面
        $this->assign('reply', $reply);
        $this->display();
    }

    public function doReplyEdit($reply_id = 0, $content = '')
    {
        $reply_id = intval($reply_id);
        $content = op_h($content);
        $this->requireLogin();
        $post = D('group_post_reply')->where(array('id' => intval($reply_id), 'status' => 1))->find();
        if ($post['uid'] != is_login() && !is_administrator()) {
            $this->error('您没有权限进行此操作');
        }
        //对帖子内容进行安全过滤
        $content = $this->filterPostContent($content);
        if (!$content) {
            $this->error("回复内容不能为空！");
        }
        $data['content'] = $content;
        $data['update_time'] = time();

        $post_id = $post['post_id'];
        $reply = D('group_post_reply')->where(array('id' => intval($reply_id)))->save($data);
        if ($reply) {
            S('group_post_replylist_' . $post_id, null);
            $this->success('编辑回复成功', U('Group/Index/detail', array('id' => $post_id)));
        } else {
            $this->error("编辑回复失败");
        }
    }

    public function edit($group_id = 0, $post_id = 0)
    {
        $group_id = intval($group_id);
        $post_id = intval($post_id);
        //判断是不是为编辑模式
        $isEdit = $post_id ? true : false;
        //如果是编辑模式的话，读取帖子，并判断是否有权限编辑
        if ($isEdit) {
            $post = D('GroupPost')->where(array('id' => intval($post_id), 'status' => 1))->find();

            $this->requireAllowEditPost($post_id);
        } else {
            $post = array('group_id' => $group_id);
        }
        //获取群组id
        $group_id = $group_id ? intval($group_id) : $post['group_id'];
        //确认群组能发帖
        if ($group_id) {
            $this->requireGroupAllowPublish($group_id);
        }
        $this->getPostCategory($group_id);

        $this->assign('group', $this->getGroup($group_id));
        $this->assign('group_id', $group_id);
        $this->setTitle('{$title}');
        $this->assign('title', $isEdit ? '编辑帖子' : '发表新帖');
        $this->assignAllowPublish();
        $this->assign('post', $post);
        $this->assign('isEdit', $isEdit);
        $this->display();
    }

    public function doEdit($post_id = null, $group_id = 0, $title = '', $content = '', $category = 0)
    {
        $title = op_t($title);
        $content = op_h($content);
        $post_id = intval($post_id);
        $group_id = intval($group_id);
        $category = intval($category);


        if (get_user_action('Group', 'post', 'ban')) {
            $this->error('您已被禁言，联系管理');
        }

        if ($group_id == '') {
            $this->error('请选择帖子所在的群组');
        }
        if ($title == '') {
            $this->error('请填写帖子标题');
        }
        if ($content == '') {
            $this->error('请填写帖子内容');
        }


        //判断是不是编辑模式
        $isEdit = $post_id ? true : false;
        //如果是编辑模式，确认当前用户能编辑帖子
        if ($isEdit) {
            $this->requireAllowEditPost($post_id);
        }
        //确认当前贴吧能发帖
        $this->requireGroupAllowPublish($group_id);
        //写入帖子的内容
        if (strlen($content) < 25) {
            $this->error('发表失败：内容长度不能小于25');
        }
        $content = filterBase64($content);
        //检测图片src是否为图片并进行过滤
        $content = filterImage($content);


        $model = D('GroupPost');
        if ($isEdit) {
            $data = array('id' => intval($post_id), 'title' => op_t($title), 'content' => op_h($content), 'parse' => 0, 'group_id' => intval($group_id), 'cate_id' => intval($category));
            $result = $model->editPost($data);
            //添加到最新动态
            $dynamic['group_id'] = $group_id;
            $dynamic['uid'] = is_login();
            $dynamic['type'] = 'update_post';
            $dynamic['create_time'] = time();
            $dynamic['row_id'] = $post_id;
            D('GroupDynamic')->add($dynamic);
            if (!$result) {
                $this->error('编辑失败：' . $model->getError());
            }
        } else {
            $data = array('uid' => is_login(), 'title' => op_h($title), 'content' => op_h($content), 'parse' => 0, 'group_id' => $group_id, 'cate_id' => intval($category));
            $before = getMyScore();
            $tox_money_before = getMyToxMoney();
            $result = $model->createPost($data);
            $after = getMyScore();
            $tox_money_after = getMyToxMoney();
            if (!$result) {
                $this->error('发表失败：' . $model->getError());
            }
            $post_id = $result;
            //添加到最新动态
            $dynamic['group_id'] = $group_id;
            $dynamic['uid'] = is_login();
            $dynamic['type'] = 'post';
            $dynamic['create_time'] = time();
            $dynamic['row_id'] = $post_id;
            D('GroupDynamic')->add($dynamic);
            //增加活跃度
            D('Group')->where(array('id' => $group_id))->setInc('activity');
            D('GroupMember')->where(array('group_id' => $group_id, 'uid' => is_login()))->setInc('activity');

        }


        $this->clearcache($group_id);
        //发布帖子成功，发送一条微博消息
        $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Group/Index/detail', array('id' => $post_id));
        $weiboApi = new WeiboApi();
        $weiboApi->resetLastSendTime();



        //实现发布帖子发布图片微博(公共内容)
        $type = 'feed';
        $feed_data = array();
        //解析并成立图片数据
        $arr = array();
        preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/", $data['content'], $arr); //匹配所有的图片
        if (!empty($arr[0])) {
            $feed_data['attach_ids'] = '';
            $dm = "http://$_SERVER[HTTP_HOST]" . __ROOT__; //前缀图片多余截取
            $max = count($arr['1']) > 9 ? 9 : count($arr['1']);
            for ($i = 0; $i < $max; $i++) {
                $tmparray = strpos($arr['1'][$i], $dm);
                if (!is_bool($tmparray)) {
                    $path = mb_substr($arr['1'][$i], strlen($dm), strlen($arr['1'][$i]) - strlen($dm));
                    $result_id = D('Home/Picture')->where(array('path'=>$path))->getField('id');
                } else {
                    $path = $arr['1'][$i];
                    $result_id = D('Home/Picture')->where(array('path'=>$path))->getField('id');
                    if(!$result_id){
                        $result_id = D('Home/Picture')->add(array('path'=>$path,'url' => $path, 'status' => 1, 'create_time' => time()));
                    }
                }
                $feed_data['attach_ids'] = $feed_data['attach_ids'] . ',' . $result_id;
            }
            $feed_data['attach_ids'] = substr($feed_data['attach_ids'], 1);
        }

        $feed_data['attach_ids'] != false &&  $type = "image";
        //开始发布微博
        if ($isEdit) {
            $weiboApi->sendWeibo("我更新了帖子【" . $title . "】：" . $postUrl, $type, $feed_data);
        } else {
            $weiboApi->sendWeibo("我发表了一个新的帖子【" . $title . "】：" . $postUrl, $type, $feed_data);
        }

        //显示成功消息
        $message = $isEdit ? '编辑成功。' : '发表成功。' . getScoreTip($before, $after) . getToxMoneyTip($tox_money_before, $tox_money_after);
        $this->success($message, U('Group/Index/detail', array('id' => $post_id)));
    }


    public function doAddGroup($group_id = null, $group_type, $title, $detail, $logo, $type, $background)
    {
        $logo = intval($logo);
        $background = intval($background);
           if ($title == '') {
            $this->error('请填写群组名称');
        }
        if (utf8_strlen($title) > 20) {
            $this->error('群组名称最多20个字');
        }
        if ($group_type == -1) {
            $this->error('请选择群组分类');
        }


        if (op_h($detail) == '') {
            $this->error('请填写群组介绍');
        }


        //判断是不是编辑模式
        $isEdit = $group_id ? true : false;
        //判断是否需要审核

        //如果是编辑模式，确认当前用户能编辑帖子
        if ($isEdit) {
            $this->requireAllowEditGroup($group_id);
        }
        $model = D('Group');
        if ($isEdit) {
            $data = array('id' => intval($group_id), 'type_id' => intval($group_type), 'title' => op_h($title), 'detail' => op_h($detail), 'logo' => $logo, 'type' => intval($type), 'background' => $background);
            $result = $model->editGroup($data);
        } else {
            $need_verify = modC('NEED_VERIFY', true);
            $data = array('id' => intval($group_id), 'type_id' => intval($group_type), 'title' => op_h($title), 'detail' => op_h($detail), 'logo' => $logo, 'type' => intval($type), 'uid' => is_login(), 'background' => $background);
            $data['status'] = $need_verify ? 0 : 1;
            $result = $model->createGroup($data);
            if (!$result) {
                $this->error('发表失败：' . $model->getError());
            }
            $group_id = $result;
            D('GroupMember')->add(array('uid' => is_login(), 'group_id' => $group_id, 'status' => 1, 'create_time' => time(), 'update_time' => time()));
            D('GroupPostCategory')->add(array('group_id'=>$group_id,'title'=>'默认分类','create_time'=>time(),'status'=>1,'sort'=>0));
        }

        //删除缓存
        $this->clearcache($group_id);


        if ($need_verify) {
            $message = '创建成功，请耐心等候管理员审核。';

            // 发送消息
            D('Message')->sendMessage(1, get_nickname(is_login()) . "创建了群组【{$title}】，快去审核吧。", '群组创建审核', U('admin/group/unverify'), is_login());

            $this->success($message, U('group/index/index'));
        }


        //发布帖子成功，发送一条微博消息
        $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Group/Index/group', array('id' => $group_id));
        $weiboApi = new WeiboApi();
        $weiboApi->resetLastSendTime();
        if ($isEdit) {
            $weiboApi->sendWeibo("我修改了群组【" . $title . "】：" . $postUrl);
        } else {
            $weiboApi->sendWeibo("我创建了一个新的群组【" . $title . "】：" . $postUrl);
        }
        //显示成功消息
        $message = $isEdit ? '编辑成功。' : '发表成功。';
        $url = $isEdit ? 'refresh' : U('group/index/group', array('id' => $group_id));
        $this->success($message, $url);
    }


    public function doReply($post_id, $content)
    {
        $post_id = intval($post_id);
        $content = op_h($content);
        if (get_user_action('Group', 'reply', 'ban')) {
            $this->error('您已被禁言，联系管理');
        }
        //确认有权限回复
        $this->requireAllowReply($post_id);
        $group_id = $this->getGroupIdByPost($post_id);

        if (!$this->isGroupAllowPublish($group_id)) {
            $this->error('只允许群组成员回复');
        }
        //检测回复时间限制
        $uid = is_login();
        $near = D('GroupPostReply')->where(array('uid' => $uid))->order('create_time desc')->find();
        $cha = time() - $near['create_time'];
        if ($cha > 10) {
            //添加到数据库
            $model = D('GroupPostReply');
            $before = getMyScore();
            $tox_money_before = getMyToxMoney();
            $result = $model->addReply($post_id, $this->filterPostContent($content));
            $after = getMyScore();
            $tox_money_after = getMyToxMoney();

            //添加到最新动态
            $dynamic['group_id'] = $group_id;
            $dynamic['uid'] = is_login();
            $dynamic['type'] = 'reply';
            $dynamic['create_time'] = time();
            $dynamic['row_id'] = $result;
            D('GroupDynamic')->add($dynamic);

            //增加活跃度
            D('Group')->where(array('id' => $group_id))->setInc('activity');
            D('GroupMember')->where(array('group_id' => $group_id, 'uid' => is_login()))->setInc('activity');

            $this->clearcache($group_id);
            if (!$result) {
                $this->error('回复失败：' . $model->getError());
            }
            //显示成功消息
            $this->success('回复成功。' . getScoreTip($before, $after) . getToxMoneyTip($tox_money_before, $tox_money_after), 'refresh');
        } else {
            $this->error('请10秒之后再回复');

        }
    }

    public function doBookmark($post_id, $add = true)
    {
        $post_id = intval($post_id);
        $add = intval($add);
        //确认用户已经登录
        $this->requireLogin();

        //写入数据库
        if ($add) {
            $result = D('GroupBookmark')->addBookmark(is_login(), $post_id);
            if (!$result) {
                $this->error('收藏失败');
            }
        } else {
            $result = D('GroupBookmark')->removeBookmark(is_login(), $post_id);
            if (!$result) {
                $this->error('取消失败');
            }
        }

        //返回成功消息
        if ($add) {
            $this->success('收藏成功');
        } else {
            $this->success('取消成功');
        }
    }

    public function search($page = 1, $uid = 0)
    {
        $_REQUEST['keywords'] = op_t($_REQUEST['keywords']);

        if ($uid != 0) {
            $where['uid'] = $uid;
            $this->assign('tip', $uid);
        } else {
            //读取帖子列表
            $map['title'] = array('like', "%{$_REQUEST['keywords']}%");
            $map['content'] = array('like', "%{$_REQUEST['keywords']}%");
            $map['_logic'] = 'OR';
            $where['_complex'] = $map;
            $where['status'] = 1;
        }
        $list = D('GroupPost')->where($where)->order('last_reply_time desc')->page($page, 10)->select();
        $totalCount = D('GroupPost')->where($where)->count();
        foreach ($list as &$post) {
            $post['colored_title'] = str_replace('"', '', str_replace($_REQUEST['keywords'], '<span style="color:red">' . $_REQUEST['keywords'] . '</span>', op_t(strip_tags($post['title']))));
            $post['colored_content'] = str_replace('"', '', str_replace($_REQUEST['keywords'], '<span style="color:red">' . $_REQUEST['keywords'] . '</span>', op_t(strip_tags($post['content']))));
            $post['group'] = $this->getGroup($post['group_id']);

        }
        unset($post);

        $_GET['keywords'] = $_REQUEST['keywords'];
        //显示页面
        $this->assign('list', $list);
        $this->assign('totalCount', $totalCount);
        $this->display();
    }


    private function limitPictureCount($content)
    {
        //默认最多显示10张图片
        $maxImageCount = 20;

        //正则表达式配置
        $beginMark = 'BEGIN0000hfuidafoidsjfiadosj';
        $endMark = 'END0000fjidoajfdsiofjdiofjasid';
        $imageRegex = '/<img(.*?)\\>/i';
        $reverseRegex = "/{$beginMark}(.*?){$endMark}/i";

        //如果图片数量不够多，那就不用额外处理了。
        $imageCount = preg_match_all($imageRegex, $content);
        if ($imageCount <= $maxImageCount) {
            return $content;
        }

        //清除伪造图片
        $content = preg_replace($reverseRegex, "<img$1>", $content);

        //临时替换图片来保留前$maxImageCount张图片
        $content = preg_replace($imageRegex, "{$beginMark}$1{$endMark}", $content, $maxImageCount);

        //替换多余的图片
        $content = preg_replace($imageRegex, "[图片]", $content);

        //将替换的东西替换回来
        $content = preg_replace($reverseRegex, "<img$1>", $content);

        //返回结果
        return $content;
    }

    private function requireCanDeletePostReply($post_id)
    {
        if (!$this->canDeletePostReply($post_id)) {
            $this->error('您没有删贴权限');
        }
    }

    private function canDeletePostReply($post_id)
    {
        //如果是管理员，则可以删除
        if (is_administrator()) {
            return true;
        }

        //如果是自己的回帖，则可以删除
        $reply = D('GroupPostReply')->find($post_id);
        if ($reply['uid'] == get_uid()) {
            return true;
        }

        //其他情况不能删除
        return false;
    }


    /**过滤输出，临时解决方案
     * @param $content
     * @return mixed|string
     * @auth 陈一枭
     */
    private function filterPostContent($content)
    {
        $content = op_h($content);
        $content = $this->limitPictureCount($content);
        $content = op_h($content);
        return $content;
    }

    /**创建群组
     * @auth 陈一枭
     */
    public function create()
    {
        $this->requireLogin();
        $this->getGroupTypes();
        $this->setTitle('创建群组');

        $this->display();
    }


    /**
     * attend  加入群组
     * @param $group_id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function attend($group_id)
    {
        //查询权限
        $this->requireGroupExists($group_id);
        $this->requireLogin();
        //判断是否已经加入
        if (is_joined($group_id)) {
            $this->error('已经加入了该群组');
        }
        // 已经加入但还未审核
        if (D('GroupMember')->where(array('uid' => is_login(), 'group_id' => $group_id))->select()) {
            $this->error('请耐心等待管理员审核');
        }
        // 获取群组的类型 0为公共的 1为私有的
        $type = $this->getGroupType($group_id);
        //要存入数据库的数据
        $data['group_id'] = $group_id;
        $data['uid'] = is_login();
        $data['create_time'] = time();

        if ($type == 1) {
            // 群组为私有的。
            $data['status'] = 0;
            $res = D('GroupMember')->add($data);
            $info = '，等待群组管理员审核！';
            $group = $this->getGroup($group_id);
            // 发送消息
            D('Message')->sendMessage($group['uid'], get_nickname(is_login()) . "请求加入群组【{$group['title']}】", '加入群组审核', U('group/Manage/member', array('group_id' => $group_id, 'status' => 0)), is_login());
            $this->clearcache($group_id);
        } else {
            // 群组为公共的
            $data['status'] = 1;
            $data['update_time'] = $data['create_time'];
            $res = D('GroupMember')->add($data);
            //添加到最新动态
            $dynamic['group_id'] = $group_id;
            $dynamic['uid'] = is_login();
            $dynamic['type'] = 'attend';
            $dynamic['create_time'] = $data['create_time'];
            D('GroupDynamic')->add($dynamic);
        }
        if ($res) {
            $this->success('加入成功' . $info, 'refresh');
        } else {
            $this->error('加入失败');
        }

    }

    /**
     * quit  退出群组
     * @param $group_id
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function quit($group_id)
    {
        //查询权限
        $this->requireGroupExists($group_id);
        $this->requireLogin();
        // 判断是否是创建者，创建者无法退出
        $group = $this->getGroup($group_id);
        if ($group['uid'] == is_login()) {
            $this->error('创建者无法退出群组');
        }
        // 判断是否在该群组内
        if (!D('GroupMember')->where(array('uid' => is_login(), 'group_id' => $group_id))->select()) {
            $this->error('你不在该群组中');
        }

        $res = D('GroupMember')->where(array('group_id' => $group_id, 'uid' => is_login()))->delete();
        //添加到最新动态
        $dynamic['group_id'] = $group_id;
        $dynamic['uid'] = is_login();
        $dynamic['type'] = 'quit';
        $dynamic['create_time'] = time();
        D('GroupDynamic')->add($dynamic);

        $this->clearcache($group_id);
        if ($res) {
            $this->success('退出成功', 'refresh');
        } else {
            $this->error('退出失败');
        }
    }

    public function recommend($post_id, $top = 1)
    {
        $top && $top = 1;
        $group_id = $this->getGroupIdByPost($post_id);
        $is_allow = $this->isAllowEditGroup($group_id);
        if (!$is_allow) {
            $this->error('没有权限进行该操作');
        }
        $res = D('GroupPost')->where(array('id' => $post_id, 'status' => 1))->setField('is_top', intval($top));
        if ($res) {
            $this->success('操作成功', 'refresh');
        } else {
            $this->error('操作失败');
        }
    }

    public function group_invite($group_id=0){
        if(IS_POST){
            $uids = I('post.uids');
            $group_id = I('post.group_id');
            $group = $this->getGroup($group_id);
            foreach($uids as $uid){
                D('Message')->sendMessage($uid, get_nickname(is_login()) . "邀请您加入群组【{$group['title']}】  <a class='ajax-post' href='".U('group/index/attend',array('group_id'=>$group_id))."'>接受邀请</a>", '邀请加入群组', U('group/index/group', array('id' => $group_id)), is_login());
            }

            $result=array('status'=>1,'info'=>'邀请成功');
            $this->ajaxReturn($result);
        }
        else{
            $group_id = I('get.group_id');
            $friendList = D('Follow')->getAllFriends(is_login());
            $friendIds = getSubByKey($friendList,'follow_who');
            $friends =array();
            foreach($friendIds as $v){
                $friends[$v] =query_user(array('avatar128','avatar64','nickname','uid','space_url','icons_html'),$v);
            }
            $this->assign('friends',$friends);
            $this->assign('group_id',$group_id);
            $this->display();
        }

    }

}