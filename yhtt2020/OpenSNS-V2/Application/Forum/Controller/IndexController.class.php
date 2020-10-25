<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:30
 */

namespace Forum\Controller;

use Think\Controller;
use Think\View;
use Weibo\Api\WeiboApi;

define('TOP_ALL', 2);
define('TOP_FORUM', 1);

class IndexController extends Controller
{

    protected $forumModel = null;

    protected $forumPostModel = null;

    public function _initialize()
    {
        /*if(D('Common/Module')->isInstalled('Mob')) {
            $sign = modC('JUMP_MOB', 0, 'mob');
            if(is_mobile() && ($sign == 0)) {
                redirect('Mob/Forum/index');
            }
        }*/
        
        $forum_list = D('Forum')->getForumList();
        //判断板块能否发帖
        foreach ($forum_list as &$e) {
            $e['allow_publish'] = $this->isForumAllowPublish($e['id']);
        }
        unset($e);
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), is_login());
        $this->assign('myInfo', $myInfo);
        //赋予论坛列表
        $this->assign('forum_list', $forum_list);
        $types = D('Forum')->getAllForumsSortByTypes();
        $this->forumModel = D("Forum");
        $this->forumPostModel = D('ForumPost');
        $this->assign('types', $types);

    }

    public function index()
    {   //参数获取
        $aId = I('id', 0, 'intval');
        $aPage = I('page', 0, 'intval');
        $aOrder = I('order', 'reply', 'text');

        $count = S('forum_count' . $aId);
        //模型初始化
        $forumPostModel = D('ForumPost');

        //当前登录用户信息
        $user_log=query_user(array('uid', 'avatar32', 'avatar64', 'avatar128', 'space_url', 'nickname'),is_login());
        $forum_count=D('forum_post')->where(array('uid'=>is_login()))->count();
        $forum_comment_count=D('forum_post_reply')->where(array('uid'=>is_login()))->count();
        $this->assign('user_log', $user_log);
        $this->assign('forum_count', $forum_count);
        $this->assign('forum_comment_count', $forum_comment_count);
        //会员数和新会员
        $all_member=D('member')->where(array('status'=>1))->count();
        $new_uid=D('member')->where(array('status'=>1))->order('reg_time desc')->limit(1)->getField('uid');
        $new_member=query_user(array('uid', 'avatar32', 'avatar64', 'avatar128', 'space_url', 'nickname'),$new_uid);
        $this->assign('all_member', $all_member);
        $this->assign('new_member', $new_member);
        //昨日和今日的帖子数
        $today = strtotime(date('Y-m-d 00:00:00'));
        $yesterday=strtotime(date('Y-m-d 00:00:00'))-24 * 60 * 60;
        $tomorrow=strtotime(date('Y-m-d 00:00:00'))+24 * 60 * 60;
        $map_a['create_time']=array('between',array($today,$tomorrow));
        $map_b['create_time']=array('between',array($yesterday,$today));
        $today_forum=D('forum_post')->where($map_a)->count();
        $yesterday_forum=D('forum_post')->where($map_b)->count();
        $this->assign('today_forum', $today_forum);
        $this->assign('yesterday_forum', $yesterday_forum);
        //统计论坛帖子数
        if (empty($count)) {
            $map['status'] = 1;
            $count['forum'] = D('Forum')->where($map)->count();
            $count['post'] = $forumPostModel->where($map)->count();
            $count['all'] = $count['post'] + D('ForumPostReply')->where($map)->count() + D('ForumLzlReply')->where($map)->count();
            S('forum_count', $count, 60);
        }
        $this->assign('count', $count);
        //取到帖子排序
        if ($aOrder == 'ctime') {
            $aOrder = 'create_time desc';
        } else if ($aOrder == 'reply') {
            $aOrder = 'last_reply_time desc';
        } else {
            $aOrder = 'last_reply_time desc';//默认的
        }

        $this->requireForumAllowView($aId);


        if ($aOrder == 'ctime') {
            $this->assign('order', 1);
        } else {
            $this->assign('order', 0);
        }

        //读取置顶列表
        if ($aId == 0) {
            $map = array('status' => 1);
            $list_top = $forumPostModel->where(' status=1 AND is_top=' . TOP_ALL)->order($aOrder)->select();
        } else {
            $map = array('forum_id' => $aId, 'status' => 1);
            $list_top = $forumPostModel->where('status=1 AND (is_top=' . TOP_ALL . ') OR (is_top=' . TOP_FORUM . ' AND forum_id=' . intval($aId) . ' and status=1)')->order($aOrder)->select();
        }

        $list_top = $this->forumPostModel->assignForumInfo($list_top);
        //读取帖子列表

        $this->assign('list_top', $list_top);


        $list = D('ForumPost')->where($map)->order($aOrder)->page($aPage, modC('FORM_POST_SHOW_NUM_INDEX',5,'Forum'))->select();
        $totalCount = D('ForumPost')->where($map)->count();
        $list = $this->forumPostModel->assignForumInfo($list);
        $this->assignSuggestionTopics();


        //关联版块数据
        $this->assignForumInfo($aId);


        $this->assignHotForum();

        $this->assignRecommandForums();


        $active_user = S('forum_active_user');
        if ($active_user === false) {
            $active_user = M('ForumPost')->field('uid,count(id) as post_count')->group('uid')->order('post_count desc')->limit(9)->select();
            foreach ($active_user as &$u) {
                $u['user'] = query_user(array('nickname', 'space_url', 'avatar64'), $u['uid']);
            }
            S('forum_active_user', $active_user, 600);
        }
        $this->assign('active_user', $active_user);

        $this->assign('list', $list);
        $this->assign('forum_id', $aId);
        $this->assignAllowPublish();

        // dump($list_top);exit;
        $this->assign('totalCount', $totalCount);

        $this->assign('tab', 'index');
        $this->display();
    }

    public function assignSuggestionTopics()
    {
        $posts = S('forum_suggestion_posts');
        if ($posts === false) {
            $suggestion_topics = modC('SUGGESTION_POSTS', '23,24,25,26,27');
            $suggestion_topics = explode('|', $suggestion_topics);
            foreach ($suggestion_topics as $s) {
                $post = M('ForumPost')->find($s);
                $post['cover'] = $this->get_pic($post['content']);
                $post['summary'] = mb_substr(text($post['content']), 0, 80, 'utf8') . '...';
                $posts[] = $post;
                S('forum_suggestion_posts', $posts, 60);
            }
        }

        $this->assign('suggestionPosts', $posts);
        return $posts;
    }

    /**正则表达式获取html中首张图片
     * @param $str_img
     * @return mixed
     */
    function get_pic($str_img)
    {
        preg_match_all("/<img.*\>/isU", $str_img, $ereg); //正则表达式把图片的整个都获取出来了
        $img = $ereg[0][0]; //图片
        $p = "#src=('|\")(.*)('|\")#isU"; //正则表达式
        preg_match_all($p, $img, $img1);
        $img_path = $img1[2][0]; //获取第一张图片路径
        return $img_path;
    }

    public function lists($page = 1)
    {
        $block_size = modC('FORUM_BLOCK_SIZE', 4, 'forum');

        $followed = D('Forum')->getFollowForums(is_login());
        $followed_id = getSubByKey($followed, 'id');
        $this->assign('block_size', $block_size);
        $types = $this->get('types');
        foreach ($types as $k => $t) {
            foreach ($t['forums'] as $key => $forum) {

                if (in_array($forum['id'], $followed_id)) {
                    $types[$k]['forums'][$key]['hasFollowed'] = true;
                }
            }

        }
        // dump($types);exit;
        $this->assign('types', $types);
        $this->assign('tab', 'lists');
        $this->display();
        // redirect(U('forum', array('page' => intval($page))));
    }

    public function assignForumInfo($forum_id)
    {
        $forums = D('Forum')->getForumList();
        $forum_key_value = array();
        foreach ($forums as $f) {
            $forum_key_value[$f['id']] = $f;
        }
        if ($forum_id != 0) {
            $forum = $forum_key_value[$forum_id];
            $hasFollowed = D('Forum')->checkFollowed($forum['id'], is_login());
            $this->assign('hasFollowed', $hasFollowed);
        } else {
            $forum = array('title' => L('_TITLE_FORUM_'));
        }
        $this->assign('forum', $forum);
        return $forum;
    }

    /**某个版块的帖子列表
     * @param int $aId 版块ID
     * @param int $aPage 分页
     * @param string $aOrder 回复排序方式
     * @auth 陈一枭
     */
    public function forum()
    {
        //参数获取
        $aId = I('id', 0, 'intval');
        $aPage = I('page', 0, 'intval');
        $aOrder = I('order', 'reply', 'text');

        $count = S('forum_count' . $aId);
        //模型初始化
        $forumPostModel = D('ForumPost');

        //版主信息
        $forum_uid=D('forum')->where(array('id'=>$aId))->getField('admin');
        $user_section=query_user(array('uid', 'avatar32', 'avatar64', 'avatar128', 'space_url', 'nickname'),$forum_uid);
        $forum_count_section=D('forum_post')->where(array('uid'=>$forum_uid))->count();
        $forum_comment_count_section=D('forum_post_reply')->where(array('uid'=>$forum_uid))->count();
        $this->assign('user_section', $user_section);
        $this->assign('forum_count_section', $forum_count_section);
        $this->assign('forum_comment_count_section', $forum_comment_count_section);
        //统计论坛帖子数
        if (empty($count)) {
            $map['status'] = 1;
            $count['forum'] = D('Forum')->where($map)->count();
            $count['post'] = $forumPostModel->where($map)->count();
            $count['all'] = $count['post'] + D('ForumPostReply')->where($map)->count() + D('ForumLzlReply')->where($map)->count();
            S('forum_count', $count, 60);
        }
        $this->assign('count', $count);

        //取到帖子排序
        if ($aOrder == 'ctime') {
            $this->assign('order', 1);
        } else {
            $this->assign('order', 0);
        }

        if ($aOrder == 'ctime') {
            $aOrder = 'create_time desc';
        } else if ($aOrder == 'reply') {
            $aOrder = 'last_reply_time desc';
        } else {
            $aOrder = 'last_reply_time desc';//默认的
        }

        $this->requireForumAllowView($aId);
        $forums = D('Forum')->getForumList();
        $forum_key_value = array();
        foreach ($forums as $f) {
            $forum_key_value[$f['id']] = $f;
        }


        //读取置顶列表
        if ($aId == 0) {
            $map = array('status' => 1);
            $list_top = $forumPostModel->where(' status=1 AND is_top=' . TOP_ALL)->order($aOrder)->select();
        } else {
            $map = array('forum_id' => $aId, 'status' => 1);
            $list_top = $forumPostModel->where('status=1 AND (is_top=' . TOP_ALL . ') OR (is_top=' . TOP_FORUM . ' AND forum_id=' . intval($aId) . ' and status=1)')->order($aOrder)->select();
        }

        //读取帖子列表
        foreach ($list_top as &$v) {
            $v['forum'] = $forum_key_value[$v['forum_id']];
        }
        unset($v);
        $this->assign('list_top', $list_top);

        $list = D('ForumPost')->where($map)->order($aOrder)->page($aPage, modC('FORM_POST_SHOW_NUM_PAGE','10','Forum'))->select();
        $totalCount = D('ForumPost')->where($map)->count();
        foreach ($list as &$v) {
            $v['forum'] = $forum_key_value[$v['forum_id']];
        }
        unset($v);

        //关联版块数据
        $this->assignForumInfo($aId);
        $this->assign('list', $list);
        $this->assign('forum_id', $aId);
        $this->assignAllowPublish();

        $this->assign('tab','lists');
        $this->assign('totalCount', $totalCount);

        $this->display();
    }

    public function doFollowing()
    {
        $aId = I('id', 0, 'intval');
        check_action_limit('forum_follow','forum',$aId,get_uid());
        $forumModel = D('Forum');
        list ($result, $follow) = D('Forum')->following($aId);
        if ($result) {
            action_log('forum_follow','forum',$aId,get_uid());
            $this->ajaxReturn(array('status' => 1, 'info' => $follow == 1 ? L('_SUCCESS_FOLLOW_').L('_PERIOD_') : L('_SUCCESS_FOLLOW_CANCEL_').L('_PERIOD_') , 'follow' => $follow));
        } else {
            $this->error($forumModel->getError());
        }
    }

    public function forums()
    {
        $this->display();
    }

    /**帖子详情页
     *
     * sr与sp仅作用于楼中楼消息来访，sp指代消息中某楼层的ID，sp指代该消息所在的分页
     *
     * @param      $id
     * @param int $page
     * @param null $sr 楼中楼回复消息中某楼层的ID
     * @param int $sp 楼中楼回复消息中的分页ID
     * @auth 陈一枭
     */
    public function detail($id, $page = 1, $sr = null, $sp = 1)
    {

        $id = intval($id);
        $page = intval($page);
        $sr = intval($sr);
        $sp = intval($sp);

        $limit = 10;
        //读取帖子内容
        $post = D('ForumPost')->where(array('id' => $id, 'status' => 1))->find();

        if (!$post) {
            $this->error(L('_ERROR_POST_NOT_FOUND_'));
        }
        $post['forum'] = D('Forum')->find($post['forum_id']);

        $post['content'] =D('Common/ContentHandler')->displayHtmlContent($post['content']);
        //增加浏览次数
        D('ForumPost')->where(array('id' => $id))->setInc('view_count');
        //读取回复列表
        $map = array('post_id' => $id, 'status' => 1);
        $replyList = D('ForumPostReply')->getReplyList($map, 'create_time', $page, $limit);

        $replyTotalCount = D('ForumPostReply')->where($map)->count();
        //判断是否需要显示1楼
        if ($page == 1) {
            $showMainPost = true;
        } else {
            $showMainPost = false;
        }

        foreach ($replyList as &$reply) {
            $reply['content'] =D('Common/ContentHandler')->displayHtmlContent($reply['content']);
        }

        unset($reply);
        //判断是否已经收藏
        $isBookmark = D('ForumBookmark')->exists(is_login(), $id);
        //显示页面
        $post['forum']['background'] = $post['forum']['background'] ? getThumbImageById($post['forum']['background'], 800, 'auto') : C('TMPL_PARSE_STRING.__IMG__') . '/default_bg.jpg';
        $this->assign('forum', $post['forum']);
        $this->assign('forum_id', $post['forum_id']);
        $this->assignAllowPublish();
        $this->assign('isBookmark', $isBookmark);
        $this->assign('post', $post);
        $this->setTitle('{$post.title|op_t} '.L('_DASH_').' '.L('_MODULE_'));

        $this->assign('limit', $limit);
        $this->assign('sr', $sr);
        $this->assign('sp', $sp);
        $this->assign('page', $page);
        $this->assign('replyList', $replyList);
        $this->assign('replyTotalCount', $replyTotalCount);
        $this->assign('showMainPost', $showMainPost);
        $this->assignForumInfo($post['forum_id']);
        $this->display();
    }

    /**
     * 删除贴子
     * @param $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function delPost($id)
    {
        $id = intval($id);
        $post=D('ForumPost')->where(array('id' => $id, 'status' => 1))->find();
        $forum_id=$post['forum_id'];

        check_auth('Forum/Index/delPost',get_expect_ids(0,0,0,$forum_id,0),L('info_authority_post_Delete_none').L('_EXCLAMATION_'));
        check_action_limit('forum_del_post','Forum',null,get_uid());
        $res = M('ForumPost')->where(array('id'=>$id))->setField('status',-1);
        D('ForumPostReply')->where(array('post_id'=>$id))->setField('status',-1);
        if($res){
            action_log('forum_del_post','Forum',$id,get_uid());
            $this->success(L('_SUCCESS_OPERATE_').L('_EXCLAMATION_'),U('Forum/Index/forum',array('id'=>$forum_id)));
        }else{
            $this->error(L('_FAIL_OPERATE_').L('_EXCLAMATION_'));
        }
    }

    public function delPostReply($id)
    {

        $id = intval($id);

        $this->requireLogin();
        check_auth('Forum/Index/delPostReply',get_expect_ids(0,$id,0,0,1),L('info_authority_post_Delete_none').L('_EXCLAMATION_'));
        $res = D('ForumPostReply')->delPostReply($id);
        $res && $this->success($res);
        !$res && $this->error('');
    }


    public function editReply($reply_id = null)
    {
        $reply_id = intval($reply_id);

        check_auth('Forum/Index/doReplyEdit',get_expect_ids(0,$reply_id,0,0,1),L('_INFO_AUTHORITY_REPLY_EDIT_').L('_EXCLAMATION_'));

        if ($reply_id) {
            $reply = D('forum_post_reply')->where(array('id' => $reply_id, 'status' => 1))->find();
        } else {
            $this->error(L('_ERROR_PARAM_').L('_EXCLAMATION_'));
        }

        $this->setTitle(L('_COMMENT_EDIT_').' '.L('_DASH_').L('_MODULE_'));
        //显示页面
        $this->assign('reply', $reply);
        $this->display();
    }

    public function doReplyEdit($reply_id = null, $content)
    {
        $reply_id = intval($reply_id);
        //对帖子内容进行安全过滤
        $content = $this->filterPostContent($content);

        $content = filter_content($content);


        check_auth('Forum/Index/doReplyEdit',get_expect_ids(0,$reply_id,0,0,1),L('_INFO_AUTHORITY_REPLY_EDIT_').L('_EXCLAMATION_'));

        if (!$content) {
            $this->error(L('_ERROR_COMMENT_CANNOT_EMPTY_').L('_EXCLAMATION_'));
        }
        $data['content'] = $content;
        $data['update_time'] = time();
        $post_id = D('forum_post_reply')->where(array('id' => intval($reply_id), 'status' => 1))->getField('post_id');
        $reply = D('forum_post_reply')->where(array('id' => intval($reply_id)))->save($data);
        if ($reply) {
            S('post_replylist_' . $post_id, null);
            $this->success(L('success_comment_Edit'), U('Forum/Index/detail', array('id' => $post_id)));
        } else {
            $this->error(L('fail_comment_Edit'));
        }
    }

    public function edit($forum_id = 0, $post_id = null)
    {
        $forum_id = intval($forum_id);
        $post_id = intval($post_id);

        //判断是不是为编辑模式
        $isEdit = $post_id ? true : false;
        //如果是编辑模式的话，读取帖子，并判断是否有权限编辑
        if ($isEdit) {
            $post = D('ForumPost')->where(array('id' => intval($post_id), 'status' => 1))->find();
            $this->requireAllowEditPost($post_id);
        } else {
            $post = array('forum_id' => $forum_id);
            check_auth('Forum/Index/addPost',get_expect_ids(0,0,0,$forum_id,0),L('_INFO_AUTHORITY_POST_').L('_EXCLAMATION_'));
        }
        //获取论坛编号
        $forum_id = $forum_id ? intval($forum_id) : $post['forum_id'];

        //确认当前论坛能发帖
        $this->requireForumAllowPublish($forum_id);

        //显示页面
        $this->assign('forum_id', $forum_id);
        $this->assignAllowPublish();
        $this->assign('post', $post);
        $this->assign('isEdit', $isEdit);
        $this->assign('tab','lists');
        $this->display();
    }

    public function doEdit($post_id = null, $forum_id = 0, $title, $content)
    {
        $post_id = intval($post_id);
        $forum_id = intval($forum_id);
        $title = text($title);
        $aSendWeibo = I('sendWeibo', 0, 'intval');

        $content = filter_content($content);//op_h($content);


        //判断是不是编辑模式
        $isEdit = $post_id ? true : false;
        $forum_id = intval($forum_id);

        //如果是编辑模式，确认当前用户能编辑帖子
        if ($isEdit) {
            $this->requireAllowEditPost($post_id);
        }else{
            check_auth('Forum/Index/addPost',-1,L('_INFO_AUTHORITY_POST_').L('_EXCLAMATION_'));
            check_action_limit('forum_add_post','Forum',null,get_uid());
        }

        //确认当前论坛能发帖
        $this->requireForumAllowPublish($forum_id);
        
        if ($title == '') {
            $this->error(L('_ERROR_TITLE_'));
        }
        if ($forum_id == 0) {
            $this->error(L('_ERROR_BLOCK_'));
        }
        if (strlen($content) < 20) {
            $this->error(L('_ERROR_CONTENT_LENGTH_'));
        }


        //   $content = filterBase64($content);
        //检测图片src是否为图片并进行过滤
        //  $content = filterImage($content);

        //写入帖子的内容
        $model = D('ForumPost');
        if ($isEdit) {
            $data = array('id' => intval($post_id), 'title' => $title, 'content' => $content, 'parse' => 0, 'forum_id' => intval($forum_id));
            $result = $model->editPost($data);
            if (!$result) {
                $this->error(L('_FAIL_EDIT_').L('_COLON_') . $model->getError());
            }
            action_log('forum_edit_post','Forum',$post_id,get_uid());
        } else {
            $data = array('uid' => is_login(), 'title' => $title, 'content' => $content, 'parse' => 0, 'forum_id' => $forum_id);

            $before = getMyScore();
            $result = $model->createPost($data);
            $after = getMyScore();
            if (!$result) {
                $this->error(L('_FAIL_POST_').L('_COLON_') . $model->getError());
            }
            action_log('forum_add_post','Forum',$result,get_uid());
            $post_id = $result;
        }

        /*   //发布帖子成功，发送一条微博消息
           $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Forum/Index/detail', array('id' => $post_id));
           $weiboApi = new WeiboApi();
           $weiboApi->resetLastSendTime();*/


        //实现发布帖子发布图片微博(公共内容)
        $type = 'feed';
        $feed_data = array();
        //解析并成立图片数据
        $arr = array();
        preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"] .*?[\/]?>/", $data['content'], $arr); //匹配所有的图片

        // 移除附件图片
        foreach ($arr[1] as $key => $value)
            if (strstr($value, "fileTypeImages")) {
                unset($arr[1][$key]);
                unset($arr[0][$key]);
            }

        if (!empty($arr[0])) {

            $feed_data['attach_ids'] = '';
            $dm = "http://$_SERVER[HTTP_HOST]" . __ROOT__; //前缀图片多余截取
            $max = count($arr['1']) > 9 ? 9 : count($arr['1']);
            for ($i = 0; $i < $max; $i++) {
                if(isset($result_id)){
                    unset($result_id);
                }
                $tmparray = strpos($arr['1'][$i], $dm);
                if (!is_bool($tmparray)) {
                    $path = mb_substr($arr['1'][$i], strlen($dm), strlen($arr['1'][$i]) - strlen($dm));
                    $result_id = D('Home/Picture')->where(array('path' => $path))->getField('id');
                } else {
                    if(strlen(__ROOT__)>0){//zzl兼容二级域名
                        $tmparray = strpos($arr['1'][$i], __ROOT__);
                        if(!is_bool($tmparray)){
                            $path = mb_substr($arr['1'][$i], strlen(__ROOT__), strlen($arr['1'][$i]) - strlen(__ROOT__));
                            $result_id = D('Home/Picture')->where(array('path' => $path))->getField('id');
                            if (!$result_id) {
                                $result_id = D('Home/Picture')->add(array('path' => $path, 'url' => $path, 'status' => 1, 'create_time' => time()));
                            }
                        }
                    }
                    if(!isset($result_id)||!$result_id){
                        $path = $arr['1'][$i];
                        $result_id = D('Home/Picture')->where(array('path' => $path))->getField('id');
                        if (!$result_id) {
                            $result_id = D('Home/Picture')->add(array('path' => $path, 'url' => $path, 'status' => 1, 'create_time' => time()));
                        }
                    }
                }
                $feed_data['attach_ids'] = $feed_data['attach_ids'] . ',' . $result_id;
            }
            $feed_data['attach_ids'] = substr($feed_data['attach_ids'], 1);
        }

        $feed_data['attach_ids'] != false && $type = "image";
        if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
            if ($aSendWeibo) {
                //开始发布微博
                if ($isEdit) {
                    D('Weibo/Weibo')->addWeibo(is_login(), L('_POST_UPDATED_')."【" . $title . "】：" . U('detail', array('id' => $post_id), null, true), $type, $feed_data);
                } else {
                    D('Weibo/Weibo')->addWeibo(is_login(), L('_POST_POSTED_')."【" . $title . "】：" . U('detail', array('id' => $post_id), null, true), $type, $feed_data);
                }
            }
        }
        //显示成功消息
        $message = $isEdit ? L('_SUCCESS_EDIT_') : L('_SUCCESS_POST_') . getScoreTip($before, $after);
        $this->success($message, U('Forum/Index/detail', array('id' => $post_id)));
    }

    public function doReply($post_id, $content)
    {
        $post_id = intval($post_id);
        $content = $this->filterPostContent($content);

        $content = filter_content($content);

        //确认有权限评论
        $post_id = intval($post_id);
        $post = D('ForumPost')->where(array('id' => $post_id))->find();
        if (!$post) {
            $this->error(L('_POST_INEXISTENT_'));
        }
        $this->requireLogin();
        check_auth('Forum/Index/doReply',$post['uid'],L('_INFO_AUTHORITY_COMMENT_').L('_EXCLAMATION_'));
        //确认有权限评论 end

        check_action_limit('forum_post_reply','Forum',null,get_uid());

        //添加到数据库
        $model = D('ForumPostReply');
        $before = getMyScore();
        $result = $model->addReply($post_id, $content);
        $after = getMyScore();
        if (!$result) {
            $this->error(L('_FAIL_COMMENT_').L('_COLON_') . $model->getError());
        }
        //显示成功消息
        action_log('forum_post_reply','Forum',$result,get_uid());
        $this->success(L('_SUCCESS_REPLY_').L('_PERIOD_') . getScoreTip($before, $after), 'refresh');
    }

    public function doBookmark($post_id, $add = true)
    {
        $post_id = intval($post_id);
        $add = intval($add);
        //确认用户已经登录
        $this->requireLogin();

        //写入数据库
        if ($add) {
            $result = D('ForumBookmark')->addBookmark(is_login(), $post_id);
            if (!$result) {
                $this->error(L('_FAIL_FAVORITE_'));
            }
        } else {
            $result = D('ForumBookmark')->removeBookmark(is_login(), $post_id);
            if (!$result) {
                $this->error(L('_FAIL_CANCEL_'));
            }
        }

        //返回成功消息
        if ($add) {
            $this->success(L('_SUCCESS_FAVORITE_'));
        } else {
            $this->success(L('_SUCCESS_CANCEL_'));
        }
    }

    /**
     * 随便看看
     */
    public function look()
    {
        $prefix = C('DB_PREFIX');
        $sql="select * from {$prefix}forum_post where status=1 order by rand() LIMIT 0 , ".modC('FORM_POST_SHOW_NUM_PAGE','10','Forum');
        $post = M('')->query($sql);
        $post = $this->forumPostModel->assignForumInfo($post);
        if (IS_POST) {
            $view = new View();
            $view->assign('posts', $post);
            $view->display(T('Forum@Index/_look'));
            exit;
        }
        $this->assign('tab','look');
        $this->assign('posts', $post);
        $this->display();
        
    }

    private function assignAllowPublish()
    {
        $forum_id = $this->get('forum_id');
        $allow_publish = $this->isForumAllowPublish($forum_id);
        $this->assign('allow_publish', $allow_publish);
    }

    private function requireLogin()
    {
        if (!$this->isLogin()) {
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
    }

    private function isLogin()
    {
        return is_login() ? true : false;
    }

    private function requireForumAllowPublish($forum_id)
    {
        $this->requireForumExists($forum_id);
        $this->requireLogin();
        $this->requireForumAllowCurrentUserGroup($forum_id);
    }

    private function isForumAllowPublish($forum_id)
    {
        if (!$this->isLogin()) {
            return false;
        }
        if (!$this->isForumExists($forum_id)) {
            return false;
        }
        if (!$this->isForumAllowCurrentUserGroup($forum_id)) {
            return false;
        }
        return true;
    }

    private function requireAllowEditPost($post_id)
    {
        $this->requirePostExists($post_id);
        $this->requireLogin();
        check_auth('Forum/Index/editPost',get_expect_ids(0,0,$post_id,0,1),L('_INFO_AUTHORITY_EDIT_').L('_EXCLAMATION_'));
        check_action_limit('forum_edit_post','Forum',$post_id,get_uid());
    }

    private function requireForumAllowView($forum_id)
    {
        $this->requireForumExists($forum_id);
    }

    private function requireForumExists($forum_id)
    {
        if (!$this->isForumExists($forum_id)) {
            $this->error(L('_ERROR_FORUM_INEXISTENT_'));
        }
    }

    private function isForumExists($forum_id)
    {
        $forum_id = intval($forum_id);
        $forum = D('Forum')->where(array('id' => $forum_id, 'status' => 1));
        return $forum ? true : false;
    }

    private function requirePostExists($post_id)
    {
        $post_id = intval($post_id);
        $post = D('ForumPost')->where(array('id' => $post_id))->find();
        if (!$post) {
            $this->error(L('_POST_INEXISTENT_'));
        }
    }

    private function requireForumAllowCurrentUserGroup($forum_id)
    {
        $forum_id = intval($forum_id);
        if (!$this->isForumAllowCurrentUserGroup($forum_id)) {
            $this->error(L('_ERROR_BLOCK_CANNOT_POST_'));
        }
    }

    private function isForumAllowCurrentUserGroup($forum_id)
    {
        $forum_id = intval($forum_id);
        //如果是超级管理员，直接允许
        if (is_login() == 1) {
            return true;
        }

        //如果帖子不属于任何板块，则允许发帖
        if (intval($forum_id) == 0) {
            return true;
        }

        //读取论坛的基本信息
        $forum = D('Forum')->where(array('id' => $forum_id))->find();
        $userGroups = explode(',', $forum['allow_user_group']);

        //读取用户所在的权限组
        $list = M('AuthGroupAccess')->where(array('uid' => is_login()))->select();
        foreach ($list as &$e) {
            $e = $e['group_id'];
        }


        //判断权限组是否有权限
        $list = array_intersect($list, $userGroups);
        return $list ? true : false;
    }


    public function search($page = 1)
    {
        $page = intval($page);
        $keywords=I('post.keywords','','text');
        $_REQUEST['keywords'] = op_t($_REQUEST['keywords']);


        //读取帖子列表
        $map['title'] = array('like', "%{$keywords}%");
        $map['content'] = array('like', "%{$keywords}%");
        $map['_logic'] = 'OR';
        $where['_complex'] = $map;
        $where['status'] = 1;

        $list = D('ForumPost')->where($where)->order('last_reply_time desc')->page($page, 10)->select();
        $totalCount = D('ForumPost')->where($where)->count();
        $forums = D('Forum')->getForumList();
        $forum_key_value = array();
        foreach ($forums as $f) {
            $forum_key_value[$f['id']] = $f;
        }
        foreach ($list as &$post) {
            $post['colored_title'] = str_replace('"', '', str_replace($keywords, '<span style="color:red">' .$keywords. '</span>', text(strip_tags($post['title']))));
            $post['colored_content'] = str_replace('"', '', str_replace($keywords, '<span style="color:red">' .$keywords . '</span>', text(strip_tags($post['content']))));
            $post['forum'] = $forum_key_value[$post['forum_id']];
        }
        unset($post);

        $_GET['keywords'] = $_REQUEST['keywords'];
        //显示页面
        $this->assign('keywords',$keywords);
        $this->assign('list', $list);
        $this->assign('totalCount', $totalCount);
        $this->display();
    }


    private function limitPictureCount($content)
    {
        //默认最多显示10张图片
        $maxImageCount = modC('LIMIT_IMAGE', 10);
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
        $content = preg_replace($imageRegex, "[".L('_PICTURE_')."]", $content);

        //将替换的东西替换回来
        $content = preg_replace($reverseRegex, "<img$1>", $content);

        //返回结果
        return $content;
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

    /**
     * @param $forumModel
     * @return mixed
     */
    public function assignRecommandForums()
    {
        $forums_recommand = S('forum_recommand_forum');
        if ($forums_recommand === false) {
            $forums_recommand_id = modC('RECOMMAND_FORUM', '1,2,3');
            $forums_recommand = $this->forumModel->where(array('id' => array('in', explode(',', $forums_recommand_id)),'status'=>1))->order('post_count desc')->select();
            S('forum_recommand_forum', $forums_recommand);
        }
        foreach ($forums_recommand as &$v) {
            $v['hasFollowed'] = $this->forumModel->checkFollowed($v['id'], is_login());
        }
        $this->assign('forums_recommand', $forums_recommand);
        return $forums_recommand;
    }

    /**
     * @return \Model
     */
    public function assignHotForum()
    {
        $forums_hot = S('forum_hot_forum');
        if ($forums_hot === false) {
            $forumModel = M('Forum');
            $forums_hot_id = modC('HOT_FORUM', '1,2,3');
            $forums_hot = $forumModel->where(array('id' => array('in', explode(',', $forums_hot_id)),'status'=>1))->order('post_count desc')->select();

            S('forum_hot_forum', $forums_hot);
        }
        foreach ($forums_hot as &$v) {
            $v['hasFollowed'] = $this->forumModel->checkFollowed($v['id'], is_login());
        }

        $this->assign('forums_hot', $forums_hot);
        return $forums_hot;
    }

    /**
     * 跳转到我的帖子或我回复的帖子列表页
     * @auth qhy
     */
    public function myForum(){
        $uid = I('get.uid','', 'intval');
        $type = I('get.type','', 'text');
        if ($type=='forum'){
            $forum=D('ForumPost')->getMyForum($uid);
            $forum_count=count($forum);
            $comment='帖子';
        }
        if ($type=='reply'){
            $forum=D('ForumPostReply')->getMyForum($uid);
            $forum_count=count($forum);
            $comment='回复';
        }
        $user=query_user(array('uid', 'avatar32', 'avatar64', 'avatar128', 'space_url', 'nickname'),$uid);
        $this->assign('comment', $comment);
        $this->assign('user', $user);
        $this->assign('forum', $forum);
        $this->assign('forum_count', $forum_count);
        $this->display();
    }
}