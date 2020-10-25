<?php

namespace app\index\controller;

use app\common\controller\HomeBase;
use app\common\logic\User as LogicUser;
use app\common\logic\File as LogicFile;


class Wap extends HomeBase
{

    // 用户逻辑
    private static $logicUser = null;


    public function _initialize()
    {
        parent::_initialize();
        self::$logicUser = get_sington_object('logicUser', LogicUser::class);
    }

    public function index()
    {
        $uid = is_login();
        $this->assign('uid', $uid);


        $usergroup = [];
        if ($uid > 0) {


            $usergroup = self::$datalogic->setname('user_focus')->getDataList(['m.uid' => $uid, 'm.type' => 2], 'm.*,group.name,group.cover_id', 'm.create_time desc', false, [['group|group', 'group.id=m.sid and group.status=1']], '', 4);

            if (!empty($usergroup)) {
                foreach ($usergroup as $key => $vo) {

                    $usergroup[$key]['imgpath'] = get_picture_url($vo['cover_id']);
                    $usergroup[$key]['id']      = $vo['sid'];


                }
            }
            $this->assign('usergroup', $usergroup);
        }

        $hotgroup = db('group')->where(['status' => 1])->limit(4)->getList();


        if (empty($hotgroup)) {


        } else {


            foreach ($hotgroup as $key => $vo) {

                $hotgroup[$key]['imgpath'] = get_picture_url($vo['cover_id']);
            }


        }


        $this->assign('hotgroup', $hotgroup);

        return $this->fetch();


    }

    public function search()
    {

        return $this->fetch();


    }

    public function myshoucang()
    {

        return $this->fetch();


    }

    public function mytopic()
    {

        return $this->fetch();


    }

    public function login()
    {


        if (is_login() > 0) {
            $this->redirect(es_url('wap/ucenter'));
        }

        return $this->fetch();


    }

    /**
     * 登录处理
     */
    public function loginHandle()
    {

        $data     = $this->param;
        $username = $data['username'];
        $password = $data['password'];
        if (empty($username) || empty($password)) : $this->jump([RESULT_ERROR, '账号或密码不能为空']);endif;


        $member = db('user')->where(['username' => $username])->getRow();

        if (empty($member)) : $this->jump([RESULT_ERROR, '用户不存在']); endif;


        // 验证用户密码
        if (md5($password . $member['salt']) === $member['password']) {

            $ndata['last_login_ip'] = CLIENT_IP;

            $ndata['last_login_time'] = TIME_NOW;

            $where['id'] = $member['id'];

            db('user')->where($where)->update($ndata);


            $auth = ['member_id' => $member['id'], 'last_login_time' => TIME_NOW];

            point_controll($member['id'], 'login', 0);//登录增加经验值

            //$auth = ['member_id' => $member['id'], 'last_login_time' => $member['last_login_time']];
            session('member_info', $member);
            session('member_auth', $auth);
            session('member_auth_sign', data_auth_sign($auth));

            $this->jump([RESULT_SUCCESS, '登录成功']);


        } else {
            $this->jump([RESULT_ERROR, '密码输入错误']);

        }


    }

    public function ucenter()
    {
        if (is_login() == 0) {
            $this->redirect(es_url('wap/login'));
        }
        return $this->fetch();


    }

    public function commentadd()
    {

        $id = $this->param['id'];

        $this->assign('id', $id);

        return $this->fetch();


    }

    public function topicadd()
    {

        $id = $this->param['id'];

        $this->assign('id', $id);

        return $this->fetch();


    }

    /**
     * 注销处理
     */
    public function logout()
    {

        $this->jump(self::$logicUser->logout());

    }

    public function allcomment()
    {

        $id = $this->param['id'];

        $this->assign('id', $id);

        return $this->fetch();


    }

    public function setting()
    {

        return $this->fetch();


    }

    public function topicdetail()
    {
        $uid = is_login();

        $id = $this->param['id'];

        $topicinfo = db('topic')->where(['id' => $id])->getRow();

        $topicinfo['create_time'] = friendlyDate($topicinfo['create_time']);
        $userinfo                 = db('user')->where(['id' => $topicinfo['uid']])->getRow();
        $topicinfo['nickname']    = $userinfo['nickname'];
        if (preg_match("/^(http:\/\/|https:\/\/).*$/", $userinfo['userhead'])) {
            $topicinfo['userhead'] = $userinfo['userhead'];
        } else {
            $topicinfo['userhead'] = webconfig('web_url') . $userinfo['userhead'];
        }


        $topicinfo['commentcount'] = db('comment')->where(['fid' => $id, 'pid' => 0])->count();

        $topicinfo['content'] = replace_contentimage($topicinfo['content']);


        $sc['type'] = 1;
        $sc['sid']  = $id;
        $sc['uid']  = $uid;

        if (db('user_focus')->where($sc)->count('id') > 0) {
            $topicinfo['hassc'] = 1;
        } else {
            $topicinfo['hassc'] = 0;
        }
        $this->assign('uid', $uid);

        if (!empty($topicinfo['gidtext'])) {


            $nn = explode(',', $topicinfo['gidtext']);

            $groupinfo = db('group')->where(['name' => $nn[0]])->getRow();

            $topicinfo['gid'] = $groupinfo['id'];
        } else {

            $groupinfo = db('group')->where(['status' => 1])->limit(1)->getList();

            $topicinfo['gid'] = $groupinfo[0]['id'];
        }

        $this->assign('topicinfo', $topicinfo);
        return $this->fetch();


    }

    public function glist()
    {


        $groupcatelist = self::$datalogic->setname('groupcate')->getDataList(['pid' => 0, 'status' => 1], true, 'sort desc', false);

        $this->assign('groupcatelist', $groupcatelist);

        $waplist = [];

        foreach ($groupcatelist as $k => $v) {

            $b['pid']    = $v['id'];
            $b['status'] = 1;

            $waplist[$k]['name']  = $v['name'];
            $waplist[$k]['count'] = db('group')->where($b)->count();
            $waplist[$k]['id']    = $v['id'];
            $waplist[$k]['child'] = self::$datalogic->setname('group')->getDataList($b, true, 'choice desc,sort desc', false);

        }

        $this->assign('waplist', $waplist);


        return $this->fetch();

    }

    public function groupinfo()
    {

        $uid       = is_login();
        $id        = $this->param['id'];
        $groupinfo = db('group')->where(['id' => $id])->getRow();

        if (user_has_focus($uid, $id, 2)) {
            $groupinfo['hasfocus'] = 1;
        } else {
            $groupinfo['hasfocus'] = 0;
        }
        $weburl                = webconfig('web_url');
        $groupinfo['describe'] = clearHtml(htmlspecialchars_decode($groupinfo['describe']));
        $groupinfo['des']      = msubstr(clearHtml(htmlspecialchars_decode($groupinfo['describe'])), 0, 30);


        $this->assign('groupinfo', $groupinfo);

        return $this->fetch();

    }

    //以下为api代码
    public function gethottopiclist()
    {
        $data      = $this->param;
        $page      = $data['page'];
        $topiclist = db('topic')->where(['status' => 1])->order('settop desc,create_time desc')->page($page . ',10')->getList();


        if (!empty($topiclist)) {
            foreach ($topiclist as $k => $v) {
                $arr = getcontentimage(htmlspecialchars_decode($v['content']))[0];


                if (count($arr) > 3) {
                    $arr = array_slice($arr, 0, 3);
                }
                $topiclist[$k]['imagesarr'] = $arr;

                $topiclist[$k]['create_time'] = friendlyDate($topiclist[$k]['create_time']);

                $topiclist[$k]['descontent'] = msubstr(clearHtml(htmlspecialchars_decode($topiclist[$k]['content'])), 0, 60);


                $userinfo = db('user')->where(['id' => $v['uid']])->getRow();

                $topiclist[$k]['nickname'] = $userinfo['nickname'];
                if (preg_match("/^(http:\/\/|https:\/\/).*$/", $userinfo['userhead'])) {
                    $topiclist[$k]['userhead'] = $userinfo['userhead'];
                } else {
                    $topiclist[$k]['userhead'] = webconfig('web_url') . $userinfo['userhead'];
                }

                $comment = db('comment')->where(['fid' => $v['id']])->order('create_time desc')->limit(1)->getList();
                if ($comment) {
                    $topiclist[$k]['ccreate_time'] = $comment[0]['create_time'];
                    $topiclist[$k]['cuid']         = $comment[0]['uid'];
                }

            }
            $ret['status'] = 'success';


            $ret['info'] = $topiclist;

            $this->jump(RESULT_ERROR, '', '', $ret);

        } else {
            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';
            $this->jump(RESULT_ERROR, '', '', $ret);

        }


    }

    public function getsearchitemlist()
    {

        $data = $this->param;
        $page = $data['page'];

        $order = "create_time desc";

        $topiclist = db('topic')->where(['status' => 1, 'title|~' => $data['cont']])->order($order)->page($page . ',10')->getList();

        $count = db('topic')->where(['status' => 1, 'title|~' => $data['cont']])->count();

        if (!empty($topiclist)) {
            foreach ($topiclist as $k => $v) {
                $arr = getcontentimage(htmlspecialchars_decode($v['content']))[0];


                if (count($arr) > 3) {
                    $arr = array_slice($arr, 0, 3);
                }
                $topiclist[$k]['imagesarr'] = $arr;

                $topiclist[$k]['create_time'] = friendlyDate($topiclist[$k]['create_time']);

                $topiclist[$k]['descontent'] = msubstr(clearHtml(htmlspecialchars_decode($topiclist[$k]['content'])), 0, 60);


                $userinfo = db('user')->where(['id' => $v['uid']])->getRow();

                $topiclist[$k]['nickname'] = $userinfo['nickname'];
                if (preg_match("/^(http:\/\/|https:\/\/).*$/", $userinfo['userhead'])) {
                    $topiclist[$k]['userhead'] = $userinfo['userhead'];
                } else {
                    $topiclist[$k]['userhead'] = webconfig('web_url') . $userinfo['userhead'];
                }

                $comment = db('comment')->where(['fid' => $v['id']])->order('create_time desc')->limit(1)->getList();
                if ($comment) {
                    $topiclist[$k]['ccreate_time'] = $comment[0]['create_time'];
                    $topiclist[$k]['cuid']         = $comment[0]['uid'];
                }

            }
            $ret['status'] = 'success';

            $ret['count'] = $count;

            $ret['info'] = $topiclist;

            $this->jump(RESULT_ERROR, '', '', $ret);

        } else {
            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


    }

    public function getgrouptopic()
    {

        $data = $this->param;

        $page = $data['page'];
        if ($data['order'] == 2) {
            $order = "update_time desc";
        } else {
            $order = "create_time desc";
        }
        $groupinfo = db('group')->where(['id' => $data['id']])->getRow();
        $topiclist = db('topic')->where(['status' => 1, 'gidtext|~' => $groupinfo['name']])->order($order)->page($page . ',10')->getList();


        if (!empty($topiclist)) {
            foreach ($topiclist as $k => $v) {
                $arr = getcontentimage(htmlspecialchars_decode($v['content']))[0];


                if (count($arr) > 3) {
                    $arr = array_slice($arr, 0, 3);
                }
                $topiclist[$k]['imagesarr'] = $arr;

                $topiclist[$k]['create_time'] = friendlyDate($topiclist[$k]['create_time']);

                $topiclist[$k]['descontent'] = msubstr(clearHtml(htmlspecialchars_decode($topiclist[$k]['content'])), 0, 60);


                $userinfo = db('user')->where(['id' => $v['uid']])->getRow();

                $topiclist[$k]['nickname'] = $userinfo['nickname'];
                if (preg_match("/^(http:\/\/|https:\/\/).*$/", $userinfo['userhead'])) {
                    $topiclist[$k]['userhead'] = $userinfo['userhead'];
                } else {
                    $topiclist[$k]['userhead'] = webconfig('web_url') . $userinfo['userhead'];
                }

                $comment = db('comment')->where(['fid' => $v['id']])->order('create_time desc')->limit(1)->getList();
                if ($comment) {
                    $topiclist[$k]['ccreate_time'] = $comment[0]['create_time'];
                    $topiclist[$k]['cuid']         = $comment[0]['uid'];
                }

            }
            $ret['status'] = 'success';


            $ret['info'] = $topiclist;

            $this->jump(RESULT_ERROR, '', '', $ret);

        } else {
            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


    }

    public function joingroup()
    {
        $data = $this->param;

        $data['uid'] = is_login();
        if ($data['uid'] == 0) {

            $ret['status'] = 'error';
            $ret['msg']    = '还未登录';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


        $info = db('user_focus')->where(['uid' => $data['uid'], 'sid' => $data['id'], 'type' => 2])->getRow();


        if ($info) {


            db('group')->setIncOrDec(['id' => $data['id']], 'membercount', 1, '-');

            db('user_focus')->where(['uid' => $data['uid'], 'sid' => $data['id'], 'type' => 2])->delete();
            $ret['status'] = 'success';
            $ret['action'] = 2;
            $ret['msg']    = '取消成功';

            $this->jump(RESULT_ERROR, '', '', $ret);


        } else {
            $ndata['type'] = 2;
            db('group')->setIncOrDec(['id' => $data['id']], 'membercount', 1);
            $ndata['uid'] = $data['uid'];
            $ndata['sid'] = $data['id'];
            db('user_focus')->insert($ndata);
            $ret['status'] = 'success';
            $ret['action'] = 1;
            $ret['msg']    = '关注成功';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


    }

    public function sctopic()
    {
        $id = $this->param['id'];


        $uid = is_login();
        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作']));
        } else {


            $where['type'] = 1;
            $where['sid']  = $id;
            $where['uid']  = $uid;

            if (self::$datalogic->setname('user_focus')->getStat($where) > 0) {
                $this->jump(self::$datalogic->setname('user_focus')->dataDel(['type' => 1, 'sid' => $id, 'uid' => $uid], '取消收藏', true));
            } else {
                $data['type'] = 1;
                $data['sid']  = $id;
                $data['uid']  = $uid;
                $this->jump(self::$datalogic->setname('user_focus')->dataAdd($data, false, '', '收藏成功'));

            }


        }

    }

    public function gettopiccomment()
    {

        $data  = $this->param;
        $limit = $data['limit'];
        $page  = $data['page'];
        $id    = $data['id'];
        if ($limit > 0) {
            $topiclist = db('comment')->where(['fid' => $id, 'pid' => 0])->order('create_time desc')->limit($limit)->getList();

        } else {
            $topiclist = db('comment')->where(['fid' => $id, 'pid' => 0])->order('create_time desc')->page($page . ',10')->getList();

        }


        if (!empty($topiclist)) {

            $ret['status'] = 'success';
            foreach ($topiclist as $key => $vo) {

                $userinfo                    = db('user')->where(['id' => $vo['uid']])->getRow();
                $topiclist[$key]['nickname'] = $userinfo['nickname'];
                if (preg_match("/^(http:\/\/|https:\/\/).*$/", $userinfo['userhead'])) {
                    $topiclist[$key]['userhead'] = $userinfo['userhead'];
                } else {
                    $topiclist[$key]['userhead'] = webconfig('web_url') . $userinfo['userhead'];
                }


                $topiclist[$key]['child'] = db('comment')->where(['pid' => $vo['id']])->order('create_time desc')->getList();

                $topiclist[$key]['create_time'] = friendlyDate($vo['create_time']);

                $topiclist[$key]['content'] = htmlspecialchars_decode($vo['content']);

            }

            $ret['info'] = $topiclist;

            $this->jump(RESULT_ERROR, '', '', $ret);

        } else {
            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


    }

    public function getmyshoucang()
    {

        $data = $this->param;
        $uid  = is_login();
        $page = $data['page'];


        $sc['type'] = 1;

        $sc['uid'] = $uid;


        $idarr = db('user_focus')->where($sc)->column('sid');

        if ($idarr) {

            $topiclist = db('topic')->where(['id' => $idarr])->order('create_time desc')->page($page . ',10')->getList();

            if ($topiclist) {
                $ret['status'] = 'error';
                $ret['msg']    = '暂无数据';
            } else {
                $ret['status'] = 'success';
                foreach ($topiclist as $key => $vo) {

                    $topiclist[$key]['descontent'] = msubstr(clearHtml(htmlspecialchars_decode($vo['content'])), 0, 40);
                    $topiclist[$key]['content']    = htmlspecialchars_decode($vo['content']);

                }

                $ret['info'] = $topiclist;

            }

            $this->jump(RESULT_ERROR, '', '', $ret);
        } else {

            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


    }

    public function getmytopic()
    {

        $data      = $this->param;
        $uid       = is_login();
        $page      = $data['page'];
        $topiclist = db('topic')->where(['uid' => $uid])->order('create_time desc')->page($page . ',10')->getList();


        if ($topiclist) {

            $ret['status'] = 'success';
            foreach ($topiclist as $key => $vo) {

                $topiclist[$key]['descontent'] = msubstr(clearHtml(htmlspecialchars_decode($vo['content'])), 0, 40);
                $topiclist[$key]['content']    = htmlspecialchars_decode($vo['content']);

            }

            $ret['info'] = $topiclist;

            $this->jump(RESULT_ERROR, '', '', $ret);
        } else {

            $ret['status'] = 'error';
            $ret['msg']    = '暂无数据';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


    }

    public function picupload()
    {
        $fileLogic = get_sington_object('fileLogic', LogicFile::class);

        $result = $fileLogic->pictureUpload();
        echo json_encode($result);

    }

    public function topicpost()
    {
        $data      = $this->param;
        $uid       = is_login();
        $groupinfo = db('group')->where(['id' => $data['id']])->getRow();

        $where['uid']     = $uid;
        $where['gidtext'] = $groupinfo['name'];

        $create_time = db('topic')->where($where)->max('create_time');

        if (time() - $create_time < 60) {
            $ret['status'] = 'error';
            $ret['msg']    = '发帖时间小于60秒';

            $this->jump(RESULT_ERROR, '', '', $ret);
        } else {
            $ndata['title']   = $data['title'];
            $ndata['content'] = htmlspecialchars_decode($data['content']);
            $ndata['uid']     = $uid;

            $ndata['gidtext']     = $groupinfo['name'];
            $ndata['status']      = 1;
            $ndata['create_time'] = time();


            db('group')->setIncOrDec(['id' => $data['id']], 'topiccount');

            if ($result = db('topic')->insert($ndata)) {
                $ret['status'] = 'success';
                $ret['msg']    = '帖子发布成功';

                $this->jump(RESULT_ERROR, '', '', $ret);
            } else {
                $ret['status'] = 'error';
                $ret['msg']    = '帖子发布失败';

                $this->jump(RESULT_ERROR, '', '', $ret);
            }
        }


    }

    public function commentpost()
    {

        $data = $this->param;
        $uid  = is_login();

        $topicinfo = db('topic')->where(['id' => $data['id']])->getRow();


        if ($uid == 0) {
            $ret['status'] = 'error';
            $ret['msg']    = '您还未登录';

            $this->jump(RESULT_ERROR, '', '', $ret);

        }

        $where['uid'] = $uid;
        $where['fid'] = $data['id'];


        $cinfo = db('comment')->where($where)->getRow();
        if ($cinfo) {
            if (time() - $cinfo['create_time'] < 60) {
                $ret['status'] = 'error';
                $ret['msg']    = '两次评论时间过短';

                $this->jump(RESULT_ERROR, '', '', $ret);

            }
        }

        $ndata['uid']     = $uid;
        $ndata['pid']     = 0;
        $ndata['content'] = htmlspecialchars_decode($data['content']);
        $ndata['fid']     = $data['id'];
        $ndata['floor']   = 0;


        $floor = db('comment')->where(['pid' => 0, 'fid' => $data['id']])->order('floor desc')->limit(1)->getList();
        if ($floor) {
            $ndata['floor'] = $floor[0]['floor'] + 1;
        } else {
            $ndata['floor'] = 2;
        }

        $ndata['status']      = 1;
        $ndata['create_time'] = time();


        db('topic')->where(['id' => $topicinfo['id']])->update(['reply' => $topicinfo['reply'] + 1, 'update_time' => time()]);

        if ($result = db('comment')->insert($ndata)) {
            $ret['status'] = 'success';
            $ret['msg']    = '评论发布成功';

            $this->jump(RESULT_ERROR, '', '', $ret);
        } else {
            $ret['status'] = 'error';
            $ret['msg']    = '评论发布失败';

            $this->jump(RESULT_ERROR, '', '', $ret);
        }


    }
}
