<?php

namespace app\index\controller;

use app\common\controller\HomeBase;

use app\admin\controller\Callback;


class Group extends HomeBase
{


    public function _initialize()
    {
        parent::_initialize();


    }

    public function checkusergroupinfo($id)
    {

        $uid = is_login();
        if ($uid > 0) {
            $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $id, 'uid' => $uid]);
        } else {
            $usergroupinfo = [];
        }

        $this->assign('usergroupinfo', $usergroupinfo);


    }

    public function nowquanxian()
    {

        $gid           = $this->param['gid'];
        $mid           = $this->param['uid'];
        $uid           = is_login();
        $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $gid, 'uid' => $uid]);
        if ($usergroupinfo['grade'] == 2) {

            $minfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $gid, 'uid' => $mid]);

            return json($minfo);
        }

    }

    public function setgroupqx()
    {

        $data        = $this->param;
        $data['uid'] = $data['memid'];

        unset($data['memid']);
        $uid           = is_login();
        $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $data['group_id'], 'uid' => $uid]);
        if ($usergroupinfo['grade'] == 2) {
            $where['group_id'] = $data['group_id'];

            $where['uid'] = $data['uid'];

            $this->jump(self::$datalogic->setname('user_group')->dataEdit($data, $where, false, '', '权限设置成功'));
        } else {
            $this->jump([RESULT_ERROR, '无权限操作']);
        }


    }

    public function groupinfo($id)
    {

        $groupinfo = self::$datalogic->setname('group')->getDataInfo(['id' => $id]);
        $this->assign('groupinfo', $groupinfo);
        $uid = is_login();

        $where['uid']      = $uid;
        $where['group_id'] = $id;

        $this->assign('usergroupinfo', self::$datalogic->setname('user_group')->getDataInfo($where));

        $topiccount = self::$datalogic->setname('topic')->getStat(['uid' => $uid, 'tid' => $id]);
        $this->assign('topiccount', $topiccount);
        $commentcount = self::$datalogic->setname('comment')->getStat(['uid' => $uid, 'gid' => $id]);
        $this->assign('commentcount', $commentcount);
        $this->assign('uid', $uid);


    }

    public function index()
    {


        if (empty($this->param['id'])) {
            $this->error('非法参数', es_url('index/index'));
        }
        $id = $this->param['id'];
        $this->groupinfo($id);
        $this->checkusergroupinfo($id);

        $uid = is_login();
        usercz($uid, $id, 2, 2);

        empty($this->param['sorttype']) ? $sorttype = 1 : $sorttype = $this->param['sorttype'];//1表示最新发帖2最新回复3按回复数排序
        $this->assign('sorttype', $sorttype);

        empty($this->param['type']) ? $type = 1 : $type = $this->param['type'];//1表示全部2表示精华
        $this->assign('type', $type);

        $topicwhere['m.tid']    = $id;
        $topicwhere['m.status'] = 1;

        if ($type == 2) {
            $topicwhere['m.choice'] = 1;
        }

        if ($sorttype == 1) {
            $order = 'm.settop desc,m.create_time desc';
        } else if ($sorttype == 2) {
            $order = 'm.settop desc,m.update_time desc';
        } else {
            $order = 'm.settop desc,m.reply desc';
        }

        $list = self::$datalogic->setname('topic')->getDataList($topicwhere, 'm.*,user.nickname,user.userhead', $order, 0, [['user|user', 'user.id=m.uid', 'LEFT']], '', '', false, 'm');

        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['imagesarr'] = getcontentimage(html_entity_decode($v['content']))[0];

            $comment = self::$datalogic->setname('comment')->getDataList(['fid' => $v['id']], true, 'create_time desc', false, '', '', 1);

            if ($comment) {
                $list['data'][$k]['ccreate_time'] = $comment[0]['create_time'];
                $list['data'][$k]['cuid']         = $comment[0]['uid'];
            }

        }

        $this->assign('list', $list['data']);

        $this->assign('listpage', $list['page']);


        //最新加入
        $memberlist = self::$datalogic->setname('user_group')->getDataList(['m.group_id' => $id, 'm.grade' => 0], 'm.*,user.nickname,user.userhead,user.grades', 'm.create_time desc', false, [['user|user', 'user.id=m.uid', 'LEFT']], '', 9, false, 'm');
        $this->assign('memberlist', $memberlist);


        $this->assign('groupid', $id);
        return $this->fetch();

    }

    public function joingroup()
    {
        if (empty(session('member_info'))) {
            $this->jump([RESULT_ERROR, '还未登录']);
        }
        $uid = session('member_info')['id'];

        $data['uid'] = $uid;

        $data['group_id'] = $this->param['id'];
        $info             = self::$datalogic->setname('user_group')->getDataInfo($data);


        if ($info) {
            if ($info['grade'] == 2) {
                $this->jump([RESULT_ERROR, '您是组长，不能退出该组']);
            } else {

                self::$datalogic->setname('group')->setIncOrDec(['id' => $data['group_id']], 'membercount', 1, '-');

                $this->jump(self::$datalogic->setname('user_group')->dataDel($data, '退出成功', true));
            }


        } else {
            $data['grade'] = 0;

            self::$datalogic->setname('group')->setIncOrDec(['id' => $data['group_id']], 'membercount', 1);
            $this->jump(self::$datalogic->setname('user_group')->dataAdd($data, false, '', '加入成功'));
        }


    }

    public function groupcz()
    {

        $uid = is_login();
        if ($uid == 0) {
            $this->jump([RESULT_ERROR, '还未登录']);
        }


        if (empty($this->param)) {
            $this->jump([RESULT_ERROR, '非法操作']);
        }

        $gid  = $this->param['id'];
        $guid = $this->param['uid'];
        $type = $this->param['type'];


        if ($uid != self::$datalogic->setname('user_group')->getDataValue(['group_id' => $gid, 'grade' => 2], 'uid')) {
            $this->jump([RESULT_ERROR, '非法操作']);
        }

        switch ($type) {

            case 'jinyan':

                $this->jump(self::$datalogic->setname('user_group')->setDataValue(['group_id' => $gid, 'uid' => $guid], 'status', 0));


                break;
            case 'jcjinyan':
                $this->jump(self::$datalogic->setname('user_group')->setDataValue(['group_id' => $gid, 'uid' => $guid], 'status', 1));


                break;
            case 'shengzhi':
                $this->jump(self::$datalogic->setname('user_group')->setDataValue(['group_id' => $gid, 'uid' => $guid], 'grade', 1));

                break;
            case 'tichu':
                $this->jump(self::$datalogic->setname('user_group')->dataDel(['group_id' => $gid, 'uid' => $guid], '已踢出', true));
                break;
            case 'jiangzhi':
                $this->jump(self::$datalogic->setname('user_group')->setDataValue(['group_id' => $gid, 'uid' => $guid], 'grade', 0));
                break;
            default:
                $this->jump([RESULT_ERROR, '非法操作']);
                break;


        }


    }

    public function topicadd()
    {


        if (IS_POST) {
            $uid       = is_login();
            $data      = $this->param;
            $groupinfo = self::$datalogic->setname('group')->getDataInfo(['id' => $data['tid'], 'pid' => $data['gid']]);
            if (empty($groupinfo) || $uid == 0) {
                $this->jump([RESULT_ERROR, '传参错误']);
            }

            if (self::$datalogic->setname('user_group')->getDataValue(['group_id' => $data['tid'], 'uid' => $uid], 'status') == 0) {

                $this->jump([RESULT_ERROR, '您已被该组管理禁言']);

            }


            $data['content'] = htmlspecialchars_decode($data['content']);
            $data['uid']     = $uid;

            self::$datalogic->setname('group')->setIncOrDec(['id' => $data['tid']], 'topiccount', 1);
            $this->jump(self::$datalogic->setname('topic')->dataAdd($data, true, '', '帖子发布成功'));

        } else {
            if (empty($this->param['id'])) {
                $this->error('非法参数', es_url('index/index'));
            }
            $id        = $this->param['id'];
            $groupinfo = self::$datalogic->setname('group')->getDataInfo(['id' => $id]);
            if (empty($groupinfo)) {
                $this->error('该小组不存在', es_url('index/index'));
            }

            //	$cateinfo=self::$datalogic->setname('user')->getDataInfo('groupcate',['id'=>$groupinfo['pid']]);

            $emotionlist = parse_config_attr(webconfig('emot_list'));

            $this->assign('emotionlist', $emotionlist);
            $this->assign('groupinfo', $groupinfo);
            $this->assign('tid', $id);
        }

        return $this->fetch();

    }

    public function topicedit()
    {

        $uid = is_login();

        if (IS_POST) {

            $data      = $this->param;
            $groupinfo = self::$datalogic->setname('group')->getDataInfo(['id' => $data['tid'], 'pid' => $data['gid']]);
            if (empty($groupinfo) || $uid == 0) {
                $this->jump([RESULT_ERROR, '传参错误']);
            }

            if (self::$datalogic->setname('user_group')->getDataValue(['group_id' => $data['tid'], 'uid' => $uid], 'status') == 0) {

                $this->jump([RESULT_ERROR, '您已被该组管理禁言']);

            }
            $info             = self::$datalogic->setname('topic')->getDataInfo(['id' => $data['id']]);
            $topic_limit_time = webconfig('topic_limit_time');
            switch ($topic_limit_time) {
                case 1:
                    $time = 'nolimit';
                    break;
                case 2:
                    $time = 10 * 60;
                    break;
                case 3:
                    $time = 24 * 60 * 60;
                    break;
                case 4:
                    $time = 7 * 24 * 60 * 60;
                    break;
                case 5:
                    $time = 30 * 24 * 60 * 60;
                    break;
                default:
                    $time = 'nolimit';
                    break;

            }
            if ($info['create_time'] > time() - $time || $time == 'nolimit') {


            } else {
                $this->jump([RESULT_ERROR, '已超过编辑时间']);
            }

            $data['content'] = htmlspecialchars_decode($data['content']);

            $this->jump(self::$datalogic->setname('topic')->dataEdit($data, ['id' => $data['id']]));

        } else {
            if (empty($this->param['id'])) {
                $this->error('非法参数', es_url('index/index'));
            }
            $id = $this->param['id'];

            $info = self::$datalogic->setname('topic')->getDataInfo(['id' => $id]);
            //需要判断是否是小组长和有权限的副组长

            $topic_limit_time = webconfig('topic_limit_time');

            switch ($topic_limit_time) {
                case 1:
                    $time = 'nolimit';
                    break;
                case 2:
                    $time = 10 * 60;
                    break;
                case 3:
                    $time = 24 * 60 * 60;
                    break;
                case 4:
                    $time = 7 * 24 * 60 * 60;
                    break;
                case 5:
                    $time = 30 * 24 * 60 * 60;
                    break;
                default:
                    $time = 'nolimit';
                    break;
            }

            if ($info['create_time'] > time() - $time || $time == 'nolimit') {


            } else {
                $this->jump([RESULT_ERROR, '已超过编辑时间']);
            }


            if ($uid != $info['uid']) {
                $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $info['tid'], 'uid' => $uid]);
                if ($usergroupinfo['grade'] > 0) {
                    //小组长可以编辑
                    if ($usergroupinfo['grade'] == 1 && $usergroupinfo['edit'] != 1) {
                        //副组长根据权限进行编辑
                        $this->error('无编辑权限', es_url('index/index'));

                    }


                } else {
                    $this->error('无编辑权限', es_url('index/index'));


                }


            }

            $groupinfo = self::$datalogic->setname('group')->getDataInfo(['id' => $info['tid']]);

            $this->assign('info', $info);
            $this->assign('groupinfo', $groupinfo);

        }

        return $this->fetch();

    }

    public function topicsettop()
    {
        $id      = $this->param['id'];
        $val     = $this->param['val'];
        $groupid = $this->param['groupid'];

        $uid = is_login();

        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作']));
        } else {
            $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $groupid, 'uid' => $uid]);
            if ($usergroupinfo['grade'] > 0) {
                if ($usergroupinfo['grade'] == 1 && $usergroupinfo['settop'] != 1) {
                    //副组长根据权限进行编辑
                    $this->jump(([RESULT_ERROR, '无操作权限']));

                }
            } else {
                $this->jump(([RESULT_ERROR, '无操作权限']));
            }
            $this->jump(self::$datalogic->setname('topic')->setDataValue(['id' => $id], 'settop', $val));
        }

    }

    public function topicdele()
    {

        $id      = $this->param['id'];
        $val     = $this->param['val'];
        $groupid = $this->param['groupid'];

        $uid = is_login();

        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作']));
        } else {
            $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $groupid, 'uid' => $uid]);
            if ($usergroupinfo['grade'] > 0) {
                if ($usergroupinfo['grade'] == 1 && $usergroupinfo['dele'] != 1) {
                    //副组长根据权限进行编辑
                    $this->jump(([RESULT_ERROR, '无操作权限']));

                }
            } else {
                $this->jump(([RESULT_ERROR, '无操作权限']));
            }

            //删除之前一定要更新小组的帖子数量
            self::$datalogic->setname('group')->setIncOrDec(['id' => $groupid], 'topiccount', 1, '-');
            $this->jump(self::$datalogic->setname('topic')->dataDel(['id' => $id], '删除成功', true));
        }

    }

    public function mytopicdele()
    {
        $id   = $this->param['id'];
        $info = self::$datalogic->setname('topic')->getDataInfo(['id' => $id]);
        $uid  = is_login();

        if ($uid != $info['uid']) {
            $this->error('无编辑权限', es_url('user/index'));
        }
        self::$datalogic->setname('group')->setIncOrDec(['id' => $info['tid']], 'topiccount', 1, '-');
        $this->jump(self::$datalogic->setname('topic')->dataDel(['id' => $id], '删除成功', true));
    }

    public function topicsetchoice()
    {
        $id      = $this->param['id'];
        $val     = $this->param['val'];
        $groupid = $this->param['groupid'];

        $uid = is_login();

        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作']));
        } else {
            $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $groupid, 'uid' => $uid]);
            if ($usergroupinfo['grade'] > 0) {
                if ($usergroupinfo['grade'] == 1 && $usergroupinfo['choice'] != 1) {
                    //副组长根据权限进行编辑
                    $this->jump(([RESULT_ERROR, '无操作权限']));

                }
            } else {
                $this->jump(([RESULT_ERROR, '无操作权限']));
            }
            $this->jump(self::$datalogic->setname('topic')->setDataValue(['id' => $id], 'choice', $val));
        }

    }

    public function groupadd()
    {


        if (IS_POST) {
            $uid              = is_login();
            $data             = $this->param;
            $data['describe'] = htmlspecialchars_decode($data['describe']);
            $data['status']   = 0;
            $data['uid']      = $uid;
            $obj              = new Callback();
            $this->jump(self::$datalogic->setname('group')->dataAdd($data, false, '', '申请提交成功,等待审核', $obj, 'groupadd_call_back'));


        } else {

            $this->assign('groupcate_list', self::$datalogic->setname('groupcate')->getDataList(['status' => 1], true, 'id desc', false));

        }

        return $this->fetch();

    }

    public function groupedit()
    {


        if (IS_POST) {
            $uid  = is_login();
            $data = $this->param;


            $where['uid']      = $uid;
            $where['group_id'] = $data['id'];
            $where['grade']    = 2;

            if (self::$datalogic->setname('user_group')->getStat($where) > 0) {


                $data['describe'] = htmlspecialchars_decode($data['describe']);

                $this->jump(self::$datalogic->setname('group')->dataEdit($data, ['id' => $data['id']], false));
            } else {
                $this->jump([RESULT_ERROR, '非法操作']);
            }


        } else {
            if (empty($this->param['id'])) {
                $this->error('非法参数', es_url('index/index'));
            }
            $id        = $this->param['id'];
            $groupinfo = self::$datalogic->setname('group')->getDataInfo(['id' => $id]);
            $uid       = is_login();


            if (empty($groupinfo)) {
                $this->error('该小组不存在', es_url('index/index'));
            }
            if ($groupinfo['uid'] != $uid) {

                $this->error('非法操作', es_url('index/index'));
            }
            //	$cateinfo=self::$datalogic->setname('user')->getDataInfo('groupcate',['id'=>$groupinfo['pid']]);

            $this->assign('groupinfo', $groupinfo);
            $this->assign('tid', $id);
        }

        return $this->fetch();

    }

    public function member()
    {
        if (empty($this->param['id'])) {
            $this->error('非法参数', es_url('index/index'));
        }
        $id = $this->param['id'];


        $uid = is_login();
        $this->checkusergroupinfo($id);


        //组长
        $zuzhanginfo = self::$datalogic->setname('user_group')->getDataInfo(['m.group_id' => $id, 'm.grade' => 2], 'm.*,user.nickname,user.statusdes,user.userhead,user.grades', [['user|user', 'user.id=m.uid', 'LEFT']], false, 'm');
        $this->assign('zuzhanginfo', $zuzhanginfo);
        //副组长
        $fzuzlist = self::$datalogic->setname('user_group')->getDataList(['m.group_id' => $id, 'm.grade' => 1], 'm.*,user.nickname,user.statusdes,user.userhead,user.grades', 'm.create_time asc', false, [['user|user', 'user.id=m.uid', 'LEFT']], '', '', false, 'm');
        $this->assign('fzuzlist', $fzuzlist);
        //组员

        $zuylist = self::$datalogic->setname('user_group')->getDataList(['m.group_id' => $id, 'm.grade' => 0], 'm.*,user.nickname,user.statusdes,user.userhead,user.grades', 'm.create_time asc', 30, [['user|user', 'user.id=m.uid', 'LEFT']], '', '', false, 'm');
        $this->assign('zuylist', $zuylist['data']);
        $this->assign('zuylistpage', $zuylist['page']);

        $this->assign('groupid', $id);

        return $this->fetch();

    }

    public function glist()
    {

        empty($this->param['keyword']) ? $keyword = '' : $keyword = $this->param['keyword'];

        $this->assign('keyword', $keyword);


        $groupcatelist = self::$datalogic->setname('groupcate')->getDataList(['pid' => 0, 'status' => 1], true, 'sort desc', false);

        $this->assign('groupcatelist', $groupcatelist);

        $waplist = [];

        foreach ($groupcatelist as $k => $v) {

            $b['pid']    = $v['id'];
            $b['status'] = 1;

            $waplist[$k]['name']  = $v['name'];
            $waplist[$k]['count'] = self::$datalogic->setname('group')->getStat($b);
            $waplist[$k]['id']    = $v['id'];
            $waplist[$k]['child'] = self::$datalogic->setname('group')->getDataList($b, true, 'choice desc,sort desc', false);

        }

        $this->assign('waplist', $waplist);
        empty($this->param['pid']) ? $pid = 0 : $pid = $this->param['pid'];
        $this->assign('pid', $pid);

        $where['status'] = 1;
        $where['name|~'] = '%' . $keyword . '%';

        if ($pid == 0) {

        } else {
            $where['pid'] = $pid;
        }
        $grouplist = self::$datalogic->setname('group')->getDataList($where, true, 'choice desc,sort desc');
        $this->assign('grouplist', $grouplist['data']);
        $this->assign('grouplistpage', $grouplist['page']);

        return $this->fetch();

    }

    public function gview()
    {
        if (empty($this->param['id'])) {
            $this->error('非法参数', es_url('index/index'));
        }
        $id = $this->param['id'];


        $uid = is_login();
        $this->assign('uid', $uid);
        //目前回复数为pid=0的回复总数
        $topicinfo = self::$datalogic->setname('topic')->getDataInfo(['m.id' => $id], 'm.*,user.nickname,user.statusdes,user.userhead,user.grades', [['user|user', 'user.id=m.uid', 'LEFT']], '', '', false, 'm');
        $this->checkusergroupinfo($topicinfo['tid']);

        $usergroupinfo = self::$datalogic->setname('user_group')->getDataInfo(['group_id' => $topicinfo['tid'], 'uid' => $uid]);


        $topicaccess = ['edit' => 0, 'dele' => 0, 'choice' => 0, 'settop' => 0];

        if (!empty($usergroupinfo)) {

            if ($usergroupinfo['grade'] == 2) {
                $topicaccess = ['edit' => 1, 'dele' => 1, 'choice' => 1, 'settop' => 1];
            }

            if ($usergroupinfo['grade'] == 1) {
                $topicaccess = ['edit' => $usergroupinfo['edit'], 'dele' => $usergroupinfo['dele'], 'choice' => $usergroupinfo['choice'], 'settop' => $usergroupinfo['settop']];
            }

        }


        $this->assign('topicaccess', $topicaccess);


        usercz($uid, $id, 2, 1);
        self::$datalogic->setname('topic')->setIncOrDec(['id' => $id], 'view', 1);
        if ($uid > 0) {


            $sc['type'] = 3;
            $sc['sid']  = $id;
            $sc['uid']  = $uid;

            if (self::$datalogic->setname('zan')->getStat($sc) > 0) {
                $topicinfo['hassc'] = 1;
            } else {
                $topicinfo['hassc'] = 0;
            }


        } else {

            $topicinfo['hassc'] = 0;
        }


        $this->assign('topicid', $id);
        $this->assign('groupid', $topicinfo['tid']);

        $this->groupinfo($topicinfo['tid']);

        empty($this->param['ctype']) ? $ctype = 1 : $ctype = $this->param['ctype'];
        empty($this->param['viewl']) ? $viewl = 1 : $viewl = $this->param['viewl'];

        $where['m.pid'] = 0;
        $where['m.fid'] = $id;

        if ($viewl == 2) {
            $where['m.uid'] = $topicinfo['uid'];
        } else {

        }

        if ($ctype == 2) {
            $order = 'm.create_time desc';
        } else {
            $order = 'm.create_time asc';
        }
        $this->assign('ctype', $ctype);
        $this->assign('viewl', $viewl);

        $commentlist = self::$datalogic->setname('comment')->getDataList($where, 'm.*,user.nickname,user.userhead,user.grades', $order, 10, [['user|user', 'user.id=m.uid', 'LEFT']]);

        foreach ($commentlist['data'] as $k => $v) {

            $result = self::$datalogic->setname('comment')->getDataList(['m.pid' => $v['id']], 'm.*,user.nickname,user.userhead,user.grades', $order, false, [['user|user', 'user.id=m.uid', 'LEFT']]);

            if ($result) {
                $commentlist['data'][$k]['child'] = $result;
            } else {
                $commentlist['data'][$k]['child'] = [];
            }


        }

        $this->assign('commentlist', $commentlist['data']);
        $this->assign('commentlistpage', $commentlist['page']);
        //最近活跃
        $memberlist = self::$datalogic->setname('topic')->getDataList(['m.tid' => $topicinfo['tid']], 'm.uid,user.nickname,user.userhead,user.grades,count(m.id) as tcount,count(comment.id) as ccount', 'tcount desc,ccount desc', false, [['user|user', 'user.id=m.uid', 'LEFT'], ['comment|comment', 'user.id=comment.uid and comment.gid=m.tid', 'LEFT']], 'm.uid,comment.uid', 12);

        $this->assign('memberlist', $memberlist);
        empty($this->param['page']) ? $page = 1 : $page = $this->param['page'];
        $this->assign('page', $page);

        $this->assign('topicinfo', $topicinfo);
        return $this->fetch();

    }

    public function commentadd()
    {
        if (IS_POST) {
            $uid       = is_login();
            $data      = $this->param;
            $topicinfo = self::$datalogic->setname('topic')->getDataInfo(['id' => $data['fid']]);


            if (empty($topicinfo)) {
                $this->jump([RESULT_ERROR, '传参错误']);
            }
            if ($uid == 0) {
                $this->jump([RESULT_ERROR, '您还未登录']);
            }

            $where['uid'] = $uid;
            $where['fid'] = $data['fid'];


            $cinfo = self::$datalogic->setname('comment')->getStat($where, 'max', 'create_time');

            if (time() - $cinfo < 60) {
                $this->jump([RESULT_ERROR, '两次评论时间过短']);
            }
            $data['uid']     = $uid;
            $data['gid']     = $topicinfo['tid'];
            $data['content'] = htmlspecialchars_decode($data['content']);

            $data['floor'] = 0;

            if ($data['pid'] > 0) {
                self::$datalogic->setname('comment')->setIncOrDec(['id' => $data['pid']], 'reply', 1);

                if ($this->param['pidcontent'] != 0) {
                    $data['content'] = $this->param['pidcontent'] . $data['content'];
                }


            } else {

                $floor = self::$datalogic->setname('comment')->getDataList(['pid' => 0, 'fid' => $data['fid']], true, 'floor desc', false, '', '', 1);
                if ($floor) {
                    $data['floor'] = $floor[0]['floor'] + 1;
                } else {
                    $data['floor'] = 2;
                }
            }
            $obj = new Group();

            $this->jump(self::$datalogic->setname('comment')->dataAdd($data, true, '', '评论成功', $obj, 'commentadd_callback'));
        } else {

            $emotionlist = parse_config_attr(config('emot_list'));
            $data        = $this->param;
            $topicinfo   = self::$datalogic->setname('topic')->getDataInfo(['id' => $data['fid']]);
            $uid         = is_login();
            if ($uid == 0) {
                $this->jump([RESULT_ERROR, '您还未登录', es_url('group/gview', ['id' => $data['fid']])]);
            }
            $this->assign('topicinfo', $topicinfo);
            $this->assign('emotionlist', $emotionlist);
            return $this->fetch();

        }


    }

    public function commentadd_callback($result, $data)
    {

        if ($data['pid'] == 0) {
            self::$datalogic->setname('topic')->setIncOrDec(['id' => $data['fid']], 'reply', 1);
        }
        self::$datalogic->setname('topic')->setDataValue(['id' => $data['fid']], 'update_time', time());

    }

    public function sctopic()
    {
        $id = $this->param['id'];


        $uid = is_login();
        if ($uid == 0) {
            $this->jump(([RESULT_ERROR, '请登录后操作']));
        } else {


            $where['type'] = 3;
            $where['sid']  = $id;
            $where['uid']  = $uid;

            if (self::$datalogic->setname('zan')->getStat($where) > 0) {
                $this->jump(self::$datalogic->setname('zan')->dataDel(['type' => 3, 'sid' => $id, 'uid' => $uid], '取消收藏', true));
            } else {
                $data['type'] = 3;
                $data['sid']  = $id;
                $data['uid']  = $uid;
                $this->jump(self::$datalogic->setname('zan')->dataAdd($data, false, '', '收藏成功'));

            }


        }

    }
}
