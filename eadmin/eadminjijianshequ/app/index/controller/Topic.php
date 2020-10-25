<?php

namespace app\index\controller;

use app\common\controller\HomeBase;

use app\admin\controller\Callback;


class Topic extends HomeBase
{


    public function _initialize()
    {
        parent::_initialize();


    }


    public function index()
    {
        //获取话题页面


        if (empty($this->param['name'])) {
            $this->error('非法参数', es_url('index/index'));
        }
        $name      = urldecode($this->param['name']);
        $groupinfo = self::$datalogic->setname('group')->getDataInfo(['name' => $name]);
        $uid       = is_login();
        if (!$groupinfo) {
            $this->error('该话题已被删除', es_url('index/index'));
        }

        $groupinfo['hassc'] = user_has_focus($uid, $groupinfo['id'], 2) ? 1 : 0;

        $focusinfo = db('user_focus', 'm')->where(['type' => 2, 'sid' => $groupinfo['id']])->field('m.*,user.nickname,user.userhead')->join(['user|user', 'user.id=m.uid', 'INNER'])->getList();

        $this->assign('focusinfo', $focusinfo);

        if ($focusinfo) {
            $groupinfo['focuscount'] = count($focusinfo);
        } else {
            $groupinfo['focuscount'] = 0;
        }


        $this->assign('groupinfo', $groupinfo);

        //关注该话题的用户
        $memberlist = self::$datalogic->setname('user_focus')->getDataList(['m.sid' => $groupinfo['id'], 'type' => 2], 'm.*,user.nickname,user.userhead,user.grades', 'm.create_time desc', false, [['user|user', 'user.id=m.uid', 'LEFT']], '', 20);
        $this->assign('memberlist', $memberlist);
        $focuscount = self::$datalogic->setname('user_focus')->getStat(['sid' => $groupinfo['id'], 'type' => 2]);
        $this->assign('focuscount', $focuscount);

        //相似话题
        $grouplist = self::$datalogic->setname('group')->getDataList(['name|~' => $name, 'id|!' => $groupinfo['id']], true, 'create_time desc', false);
        $this->assign('grouplist', $grouplist);


        $this->assign('uid', $uid);

        $order = empty($this->param['order']) ? 1 : $this->param['order'];
        $paixu = empty($this->param['paixu']) ? 2 : $this->param['paixu'];

        if ($paixu == 2) {
            $orderstr = 'settop desc,create_time desc';
        } else {
            $orderstr = 'settop desc,update_time desc,create_time desc';
        }


        if ($order == 2) {
            $where['choice'] = 1;
        }
        if ($order == 3) {

            $sidarr = db('user_focus')->where(['uid' => $uid])->column('sid');
            if ($sidarr) {
                $where['id'] = $sidarr;
            } else {
                $where['id'] = 0;
            }


        }


        $where['gidtext|~'] = $groupinfo['name'];
        $topiclist          = self::$datalogic->setname('topic')->getDataList($where, 'user.nickname,user.point,user.expoint1,user.userhead,rzuser.type as rztype,rzuser.status as rzstatus,m.*', $orderstr, 10, [['user|user', 'user.id=m.uid'], ['rzuser|rzuser', 'rzuser.uid=m.uid']]);

        foreach ($topiclist['data'] as $key => $vo) {

            if ($vo['rzstatus'] && $vo['rzstatus'] == 1) {

                if ($vo['rztype'] == 1) {
                    $topiclist['data'][$key]['rzicon'] = 'icon-vimeo';
                } else {
                    $topiclist['data'][$key]['rzicon'] = 'icon-vimeo i-ve';
                }

            }

            $focuscount = db('user_focus')->where(['sid' => $vo['id'], 'type' => 1])->count();

            $cinfo = db('comment')->where(['fid' => $vo['id'], 'pid' => 0])->order('create_time desc')->limit(1)->getList();


            if ($cinfo) {

                $lsnickname = getusernamebyid($cinfo[0]['uid']);
                //'<span class="aw-user-name" data-id="'.$cinfo[0]['uid'].'">'.$lsnickname.'</span>&nbsp;'.
                $topiclist['data'][$key]['replystr']   = '回复&nbsp;' . friendlyDate($cinfo[0]['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
                $topiclist['data'][$key]['replytime']  = friendlyDate($cinfo[0]['create_time']);
                $topiclist['data'][$key]['replyuser']  = $lsnickname;
                $topiclist['data'][$key]['replyuid']   = $cinfo[0]['uid'];
                $topiclist['data'][$key]['focuscount'] = $focuscount;
                $topiclist['data'][$key]['actionname'] = '回复';
            } else {
                $lsnickname                            = getusernamebyid($vo['uid']);
                $topiclist['data'][$key]['replystr']   = '发布&nbsp;' . friendlyDate($vo['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
                $topiclist['data'][$key]['replytime']  = friendlyDate($vo['create_time']);
                $topiclist['data'][$key]['replyuser']  = $lsnickname;
                $topiclist['data'][$key]['replyuid']   = $vo['uid'];
                $topiclist['data'][$key]['focuscount'] = $focuscount;
                $topiclist['data'][$key]['actionname'] = '发布';
            }

            if ($vo['gidtext']) {

                $topiclist['data'][$key]['htlist'] = explode(',', $vo['gidtext']);

            }


            if (!$vo['description']) {

                $topiclist['data'][$key]['description'] = msubstr(clearHtml(htmlspecialchars_decode($vo['content'])), 0, 60);
            }
            $arr                                    = getcontentimage(htmlspecialchars_decode($vo['content']), false)[1];
            $topiclist['data'][$key]['imagescount'] = count($arr);

            if (count($arr) > 3) {
                $arr = array_slice($arr, 0, 3);
            }
            $topiclist['data'][$key]['imagesarr'] = $arr;

        }

        $this->assign('topiclist', $topiclist['data']);
        $this->assign('page', $topiclist['page']);


        $this->assign('paixu', $paixu);
        $this->assign('order', $order);
        return $this->fetch();

    }

    public function dongtai()
    {


        //帖子有了新评论，话题增加了帖子
        $where['type'] = [11, 12];
        $orderstr      = 'create_time desc';

        $uid = is_login();


        $topiclist = self::$datalogic->setname('homeaction_log')->getDataList($where, true, $orderstr, 10);
        $sidarr    = [];

        if ($topiclist) {

            foreach ($topiclist['data'] as $key => $vo) {

                if ($vo['type'] == 11) {
                    //发布帖子
                    $tpinfo   = db('topic')->where(['id' => $vo['sid']])->getRow();
                    $userinfo = db('user')->where(['id' => $tpinfo['uid']])->getRow();

                    if ($tpinfo && $userinfo) {
                        $topiclist['data'][$key]['userinfo'] = $userinfo;
                        $topiclist['data'][$key]['topinfo']  = $tpinfo;
                    } else {

                        array_push($sidarr, $vo['sid']);

                        unset($topiclist['data'][$key]);


                    }


                }
                if ($vo['type'] == 12) {
                    //发布帖子评论
                    $tpinfo   = db('comment')->where(['id' => $vo['sid']])->getRow();
                    $tinfo    = db('topic')->where(['id' => $tpinfo['fid']])->getRow();
                    $userinfo = db('user')->where(['id' => $tpinfo['uid']])->getRow();


                    if ($tpinfo && $tinfo && $userinfo) {
                        $topiclist['data'][$key]['topinfo']  = $tpinfo;
                        $topiclist['data'][$key]['tinfo']    = $tinfo;
                        $topiclist['data'][$key]['userinfo'] = $userinfo;


                    } else {
                        array_push($sidarr, $vo['sid']);

                        unset($topiclist['data'][$key]);
                    }


                }


            }


        }

        if (count($sidarr) > 0) {
            $where['sid|!'] = $sidarr;

            $page = self::$datalogic->setname('homeaction_log')->getDataList($where, true, $orderstr, 10);

        } else {
            $page = $topiclist;
        }


        $this->assign('topiclist', $topiclist['data']);
        $this->assign('page', $page['page']);


        return $this->fetch();

    }

    public function gview()
    {
        if (empty($this->param['id'])) {
            $this->error('非法参数', es_url('index/index'));
        }
        $id        = $this->param['id'];
        $topicinfo = self::$datalogic->setname('topic')->getDataInfo(['m.id' => $id], 'm.*,user.nickname,user.statusdes,user.userhead,user.grades', [['user|user', 'user.id=m.uid', 'LEFT']], '', '', false, 'm');
        if (!$topicinfo) {
            $this->error('该帖子已被删除', es_url('index/index'));
        }

        $uid = is_login();
        $this->assign('uid', $uid);
        self::$datalogic->setname('topic')->setIncOrDec(['id' => $id], 'view', 1);//浏览数+1
        homeaction_log($uid, 2, $id);

        if ($topicinfo['gidtext']) {

            $htlist = explode(',', $topicinfo['gidtext']);//话题数组
            $this->assign('htlist', $htlist);
        }
        $topicinfo['hassc'] = user_has_focus($uid, $topicinfo['id'], 1) ? 1 : 0;

        $focusinfo = db('user_focus', 'm')->where(['type' => 1, 'sid' => $topicinfo['id']])->field('m.*,user.nickname,user.userhead')->join(['user|user', 'user.id=m.uid', 'INNER'])->getList();

        $this->assign('focusinfo', $focusinfo);

        if ($focusinfo) {
            $topicinfo['focuscount'] = count($focusinfo);
        } else {
            $topicinfo['focuscount'] = 0;
        }

        //智能推荐，目前非人工智能
        $znlist = db('topic')->where(['id|!' => $topicinfo['id'], 'choice' => 1])->order('update_time desc,create_time desc')->getList();
        $this->assign('znlist', $znlist);


        $topicinfo['commentcount'] = db('comment')->where(['pid' => 0, 'fid' => $topicinfo['id']])->count();

        $this->assign('topicinfo', $topicinfo);


        $topicuserinfo            = db('user')->where(['id' => $topicinfo['uid']])->getRow();
        $topicuserinfo['fscount'] = db('user_focus')->where(['type' => 3, 'sid' => $topicinfo['uid']])->count();

        $rzuserinfo = db('rzuser')->where(['status' => 1, 'uid' => $topicinfo['uid']])->getRow();
        if ($rzuserinfo) {
            $topicuserinfo['rzuser'] = $rzuserinfo;
            if ($rzuserinfo['type'] == 1) {
                $topicuserinfo['rzicon'] = 'icon-myvip';

            } else {
                $topicuserinfo['rzicon'] = 'icon-myvip i-ve';

            }

        } else {
            $topicuserinfo['rzicon'] = 1;
        }

        $this->assign('topicuserinfo', $topicuserinfo);


        //按照赞成数、关注人的评论和时间升序降序排列评论


        empty($this->param['ctype']) ? $ctype = 1 : $ctype = $this->param['ctype'];
        empty($this->param['asc']) ? $asc = 1 : $asc = $this->param['asc'];


        $where['m.pid'] = 0;
        $where['m.fid'] = $id;


        if ($ctype == 1) {
            if ($asc == 1) {
                $asc   = 2;
                $order = 'm.create_time asc';
            } else {
                $asc   = 1;
                $order = 'm.create_time desc';
            }

        } elseif ($ctype == 2) {

            if ($asc == 1) {
                $asc   = 2;
                $order = 'm.ding asc';
            } else {
                $asc   = 1;
                $order = 'm.ding desc';
            }
        } else {

            $cidarr = db('user_focus')->where(['type' => 3, 'uid' => $uid])->column('sid');
            if ($cidarr) {
                $where['m.uid'] = $cidarr;
            } else {
                $where['m.uid'] = 0;
            }

            $order = 'm.create_time desc';

        }
        $this->assign('ctype', $ctype);
        $this->assign('asc', $asc);

        $commentlist = self::$datalogic->setname('comment')->getDataList($where, 'm.*,user.nickname,user.userhead,user.grades', $order, 10, [['user|user', 'user.id=m.uid', 'LEFT']]);

        foreach ($commentlist['data'] as $k => $v) {

            $commentlist['data'][$k]['dinguser'] = db('homeaction_log', 'm')->where(['sid' => $v['id'], 'type' => 9])->field('m.uid,user.nickname')->join(['user|user', 'user.id=m.uid'])->getList();

            $commentlist['data'][$k]['subcount'] = db('comment')->where(['pid' => $v['id']])->count();

        }

        //被屏蔽的帖子列表
        $tipoffs_list = db('tipoffs')->where(['status' => 1])->column('contentId');
        //已被屏蔽的帖子渲染页面时屏蔽帖子内容
        foreach ($commentlist['data'] as &$item) {
            if (array_search($item['id'], $tipoffs_list)) {
                $item['content'] = '帖子内容已被屏蔽';
            }
        }

        $this->assign('commentlist', $commentlist['data']);
        $this->assign('commentlistpage', $commentlist['page']);
        $this->assign('commentlistcount', $commentlist['total']);
//        $this->assign('tipoffs_list',$tipoffs_list);

        return $this->fetch();

    }


    public function commentadd()
    {

        $uid       = is_login();
        $data      = $this->param;
        $topicinfo = self::$datalogic->setname('topic')->getDataInfo(['id' => $data['fid']]);


        if (empty($topicinfo)) {
            $this->jump([RESULT_ERROR, '传参错误']);
        }
        if ($uid == 0) {
            $this->jump([RESULT_ERROR, '您还未登录']);
        }

        $userinfo = self::$datalogic->setname('user')->getDataInfo(['id' => $uid]);
        if ($userinfo['status'] == 6) {

            $this->jump([
                RESULT_ERROR,
                '您已被禁言'
            ]);
        }


        $gradeinfo = self::$datalogic->setname('usergrade')->getDataInfo(['id' => $userinfo['grades']]);

        $gradeinfoarr = explode(',', $gradeinfo['quanx']);

        if (!in_array(2, $gradeinfoarr)) {
            $this->jump([
                RESULT_ERROR,
                '您所在权限组无法回帖'
            ]);

        }


        $where['uid'] = $uid;
        $where['fid'] = $data['fid'];


        $cinfo = self::$datalogic->setname('comment')->getStat($where, 'max', 'create_time');

        if (time() - $cinfo < 60) {
            $this->jump([RESULT_ERROR, '两次评论时间过短']);
        }
        $data['uid'] = $uid;

        $data['content'] = htmlspecialchars_decode(string_remove_xss($data['content']));

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


        $this->jump(self::$datalogic->setname('comment')->dataAdd($data, true, '', '评论成功', '', function ($result, $data) {
            homeaction_log($data['uid'], 12, $result);
            if ($data['pid'] == 0) {
                self::$datalogic->setname('topic')->setIncOrDec(['id' => $data['fid']], 'reply', 1);
            }
        }));


    }

    public function cindex()
    {


        if (empty($this->param['id'])) {
            $this->error('非法参数', es_url('index/index'));
        }

        $order = empty($this->param['order']) ? 1 : $this->param['order'];

        $uid = is_login();

        $cateinfo = self::$datalogic->setname('topiccate')->getDataInfo(['id' => $this->param['id']]);
        if (!$cateinfo) {
            $this->error('非法参数', es_url('index/index'));
        }
        $this->assign('id', $this->param['id']);
        $this->assign('cateinfo', $cateinfo);
        if ($order == 1) {
            $orderstr = 'settop desc,create_time desc';
        } elseif ($order == 2) {
            $orderstr = 'settop desc,update_time desc,reply desc,view desc';
        } else {
            $orderstr = 'settop desc,choice desc,update_time desc';
        }

        $this->assign('order', $order);


        $topiclist = self::$datalogic->setname('topic')->getDataList(['m.status' => 1, 'm.tid' => $cateinfo['id']], 'user.nickname,user.point,user.expoint1,user.userhead,rzuser.type as rztype,rzuser.status as rzstatus,m.*', $orderstr, 10, [['user|user', 'user.id=m.uid'], ['rzuser|rzuser', 'rzuser.uid=m.uid']]);

        foreach ($topiclist['data'] as $key => $vo) {

            if ($vo['rzstatus'] && $vo['rzstatus'] == 1) {

                if ($vo['rztype'] == 1) {
                    $topiclist['data'][$key]['rzicon'] = 'icon-vimeo';
                } else {
                    $topiclist['data'][$key]['rzicon'] = 'icon-vimeo i-ve';
                }

            }

            $focuscount = db('user_focus')->where(['sid' => $vo['id'], 'type' => 1])->count();

            $cinfo = db('comment')->where(['fid' => $vo['id'], 'pid' => 0])->order('create_time desc')->limit(1)->getList();


            if ($cinfo) {

                $lsnickname = getusernamebyid($cinfo[0]['uid']);
                //'<span class="aw-user-name" data-id="'.$cinfo[0]['uid'].'">'.$lsnickname.'</span>&nbsp;'.
                $topiclist['data'][$key]['replystr']   = '回复&nbsp;' . friendlyDate($cinfo[0]['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
                $topiclist['data'][$key]['replytime']  = friendlyDate($cinfo[0]['create_time']);
                $topiclist['data'][$key]['replyuser']  = $lsnickname;
                $topiclist['data'][$key]['replyuid']   = $cinfo[0]['uid'];
                $topiclist['data'][$key]['replyuser']  = $lsnickname;
                $topiclist['data'][$key]['replyuid']   = $cinfo[0]['uid'];
                $topiclist['data'][$key]['focuscount'] = $focuscount;
                $topiclist['data'][$key]['actionname'] = '回复';


            } else {
                $lsnickname                            = getusernamebyid($vo['uid']);
                $topiclist['data'][$key]['replystr']   = '发布&nbsp;' . friendlyDate($vo['create_time']) . '&nbsp;(' . $focuscount . '人关注)';
                $topiclist['data'][$key]['replytime']  = friendlyDate($vo['create_time']);
                $topiclist['data'][$key]['replyuser']  = $lsnickname;
                $topiclist['data'][$key]['replyuid']   = $vo['uid'];
                $topiclist['data'][$key]['focuscount'] = $focuscount;
                $topiclist['data'][$key]['actionname'] = '发布';
            }

            if ($vo['gidtext']) {

                $topiclist['data'][$key]['htlist'] = explode(',', $vo['gidtext']);

            }


            if (!$vo['description']) {

                $topiclist['data'][$key]['description'] = msubstr(clearHtml(htmlspecialchars_decode($vo['content'])), 0, 60);
            }
            $arr                                    = getcontentimage(htmlspecialchars_decode($vo['content']), false)[1];
            $topiclist['data'][$key]['imagescount'] = count($arr);

            if (count($arr) > 3) {
                $arr = array_slice($arr, 0, 3);
            }
            $topiclist['data'][$key]['imagesarr'] = $arr;

        }

        $this->assign('topiclist', $topiclist['data']);
        $this->assign('page', $topiclist['page']);


        //热门话题列表
        $hotgrouplist = self::$datalogic->setname('group')->getDataList(['status' => 1], true, 'sort desc,membercount desc,topiccount desc', false, '', '', 2);

        $this->assign('hotgrouplist', $hotgrouplist);


        $rzuserlist = self::$datalogic->setname('rzuser')->getDataList(['m.status' => 1], 'user.nickname,user.point,user.expoint1,user.expoint2,user.userhead,m.*', 'm.create_time desc', false, [['user|user', 'user.id=m.uid']], '', 2);

        foreach ($rzuserlist as $key => $vo) {

            $rzuserlist[$key]['zancount'] = db('homeaction_log')->where(['type' => [1, 9], 'uid' => $vo['uid']])->count();

            $rzuserlist[$key]['topiccount'] = db('topic')->where(['status' => 1, 'uid' => $vo['uid']])->count();

            $rzuserlist[$key]['fscount'] = db('user_focus')->where(['sid' => $vo['uid'], 'type' => 3])->count();

        }
        $this->assign('rzuserlist', $rzuserlist);

        $hotuserlist = self::$datalogic->setname('user')->getDataList(['m.status' => 1], 'count(topic.id) as topiccount,m.*,rzuser.type,rzuser.statusdes as rzstatusdes', 'topiccount desc', false, [['topic|topic', 'topic.uid=m.id'], ['rzuser|rzuser', 'rzuser.uid=m.id and rzuser.status=1']], 'topic.uid', 5);

        foreach ($hotuserlist as $key => $vo) {

            $hotuserlist[$key]['zancount'] = db('homeaction_log')->where(['type' => [1, 9], 'uid' => $vo['id']])->count();
        }

        $this->assign('hotuserlist', $hotuserlist);

        $topapp = self::$datalogic->setname('app')->getDataList(['status' => 1, 'istop' => 1], true, 'aid desc', false, '', '', 5);

        $newapp = self::$datalogic->setname('app')->getDataList(['status' => 1, 'istop' => 0], true, 'aid desc', false, '', '', 5);

        $this->assign('newapp', $newapp);

        $this->assign('topapp', $topapp);

        return $this->fetch();

    }

    public function topicadd()
    {
        if (IS_POST) {
            $uid = is_login();

            $data = $this->param;
            if ($uid == 0) {

                $this->jump([
                    RESULT_ERROR,
                    '请先登录'
                ]);
            }
            if (empty($data['tid'])) {
                $this->jump([
                    RESULT_ERROR,
                    '请选择分类'
                ]);
            }
            $userinfo = self::$datalogic->setname('user')->getDataInfo(['id' => $uid]);
            if ($userinfo['status'] == 6) {

                $this->jump([
                    RESULT_ERROR,
                    '您已被禁言'
                ]);
            }


            $gradeinfo = self::$datalogic->setname('usergrade')->getDataInfo(['id' => $userinfo['grades']]);

            $gradeinfoarr = explode(',', $gradeinfo['quanx']);

            if (!in_array(1, $gradeinfoarr)) {
                $this->jump([
                    RESULT_ERROR,
                    '您所在权限组无法发帖'
                ]);

            }


            $where['uid'] = $uid;
            $cinfo        = self::$datalogic->setname('topic')->getStat($where, 'max', 'create_time');

            if (time() - $cinfo < 60) {
                $this->jump([RESULT_ERROR, '两次发帖时间过短']);
            }
            $data['title']   = clearHtml($data['title']);
            $data['content'] = htmlspecialchars_decode(string_remove_xss($data['content']));

            if (webconfig('bd_image') == 1) {
                $data ['content'] = getImageToLocal($data['content']);
            }


            $data ['uid'] = $uid;

            $htlist = $data ['gidtext'];

            if (!empty ($htlist)) {

                $htarr = explode(',', $htlist);
                foreach ($htarr as $key => $vo) {

                    $ginfo = db('group')->where([
                        'name' => $vo
                    ])->getRow();
                    if ($ginfo) {
                        self::$datalogic->setname('group')->setIncOrDec([
                            'id' => $ginfo ['id']
                        ], 'topiccount', 1);
                    } else {

                        $htdata ['status']      = 1;
                        $htdata ['create_time'] = time();
                        $htdata ['name']        = $vo;
                        $htdata ['topiccount']  = 1;
                        $htdata ['uid']         = $uid;

                        $result = db('group')->insert($htdata);


                    }
                }
            }

            if (webconfig('OPEN_DRAFTBOX') == 1) {
                $data['status'] = 0;
            } else {
                $data['status'] = 1;
            }

            $this->jump(self::$datalogic->setname('topic')->dataAdd($data, true, '', '帖子发布成功', '', function ($result, $data) {

                homeaction_log($data['uid'], 11, $result);

            }));
        } else {

            $cateinfo = self::$datalogic->setname('group')->getDataList(['status' => 1], true, 'choice desc,sort desc,create_time asc', false, '', '', 20);
            $this->assign('cateinfo', $cateinfo);

            $name = '';
            !empty($this->param['name']) && $name = urldecode($this->param['name']);
            $this->assign('name', $name);
            $topiccatelist = self::$datalogic->setname('topiccate')->getDataList(['status' => 1], true, 'sort desc', false);
            $this->assign('topiccatelist', $topiccatelist);
        }

        return $this->fetch();
    }

    public function topicedit()
    {
        $uid = is_login();

        if (IS_POST) {

            $data = $this->param;

            if ($uid == 0) {

                $this->jump([
                    RESULT_ERROR,
                    '请先登录'
                ]);
            }
            if (self::$datalogic->setname('user')->getDataValue([
                    'id' => $uid
                ], 'status') == 6) {

                $this->jump([
                    RESULT_ERROR,
                    '您已被禁言'
                ]);
            }
            $info             = self::$datalogic->setname('topic')->getDataInfo([
                'id' => $data ['id']
            ]);
            $topic_limit_time = webconfig('topic_limit_time');
            switch ($topic_limit_time) {
                case 1 :
                    $time = 'nolimit';
                    break;
                case 2 :
                    $time = 10 * 60;
                    break;
                case 3 :
                    $time = 24 * 60 * 60;
                    break;
                case 4 :
                    $time = 7 * 24 * 60 * 60;
                    break;
                case 5 :
                    $time = 30 * 24 * 60 * 60;
                    break;
                default :
                    $time = 'nolimit';
                    break;
            }
            if ($info ['create_time'] > time() - $time || $time == 'nolimit') {
            } else {
                $this->jump([
                    RESULT_ERROR,
                    '已超过编辑时间'
                ]);
            }
            $data['title']    = clearHtml($data['title']);
            $data ['content'] = htmlspecialchars_decode(string_remove_xss($data['content']));
            if (webconfig('bd_image') == 1) {
                $data ['content'] = getImageToLocal($data['content']);
            }
            $nowhtlist = $info ['gidtext'];

            if (!empty ($nowhtlist)) {
                $nowhtarr = explode(',', $nowhtlist);
                foreach ($nowhtarr as $k => $v) {


                    self::$datalogic->setname('group')->setIncOrDec([
                        'name' => $v
                    ], 'topiccount', 1, '-');

                }


            }

            $htlist = $data ['gidtext'];

            if (!empty ($htlist)) {

                $htarr = explode(',', $htlist);
                foreach ($htarr as $key => $vo) {

                    $ginfo = db('group')->where([
                        'name' => $vo
                    ])->getRow();
                    if ($ginfo) {
                        self::$datalogic->setname('group')->setIncOrDec([
                            'id' => $ginfo ['id']
                        ], 'topiccount', 1);
                    } else {

                        $htdata ['status']      = 1;
                        $htdata ['create_time'] = time();
                        $htdata ['name']        = $vo;
                        $htdata ['topiccount']  = 1;
                        $htdata ['uid']         = $uid;

                        db('group')->insert($htdata);
                    }

                }

            }

            $this->jump(self::$datalogic->setname('topic')->dataEdit($data, [
                'id' => $data ['id']
            ]));
        } else {
            if (empty ($this->param ['id'])) {
                $this->error('非法参数', es_url('index/index'));
            }
            $id = $this->param ['id'];

            $info = self::$datalogic->setname('topic')->getDataInfo([
                'id' => $id
            ]);
            if ($uid != $info ['uid']) {

                $this->error('无编辑权限', es_url('index/index'));
            }
            $topic_limit_time = webconfig('topic_limit_time');

            switch ($topic_limit_time) {
                case 1 :
                    $time = 'nolimit';
                    break;
                case 2 :
                    $time = 10 * 60;
                    break;
                case 3 :
                    $time = 24 * 60 * 60;
                    break;
                case 4 :
                    $time = 7 * 24 * 60 * 60;
                    break;
                case 5 :
                    $time = 30 * 24 * 60 * 60;
                    break;
                default :
                    $time = 'nolimit';
                    break;
            }

            if ($info ['create_time'] > time() - $time || $time == 'nolimit') {
            } else {
                $this->jump([
                    RESULT_ERROR,
                    '已超过编辑时间'
                ]);
            }

            $cateinfo = self::$datalogic->setname('group')->getDataList(['status' => 1], true, 'choice desc,sort desc,create_time asc', false, '', '', 20);
            $this->assign('cateinfo', $cateinfo);
            $topiccatelist = self::$datalogic->setname('topiccate')->getDataList(['status' => 1], true, 'sort desc', false);
            $this->assign('topiccatelist', $topiccatelist);
            $info['title'] = clearHtml($info['title']);
            $this->assign('info', $info);
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

        if ($info['gidtext']) {

            $nn = explode(',', $info['gidtext']);
            foreach ($nn as $k => $v) {

                self::$datalogic->setname('group')->setIncOrDec(['name' => $v], 'topiccount', 1, '-');


            }


        }
        homeaction_log($uid, 14, $id);

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


    public function htlist()
    {

        empty($this->param['keyword']) ? $keyword = '' : $keyword = $this->param['keyword'];

        $this->assign('keyword', $keyword);


        $groupcatelist = self::$datalogic->setname('groupcate')->getDataList(['pid' => 0, 'status' => 1], true, 'sort desc', false);

        $this->assign('groupcatelist', $groupcatelist);


        empty($this->param['pid']) ? $pid = 0 : $pid = $this->param['pid'];
        $this->assign('pid', $pid);

        $where['status'] = 1;
        $where['name|~'] = '%' . $keyword . '%';

        if ($pid == 0) {

        } else {
            $where['pid'] = $pid;
        }
        $newhtlist = self::$datalogic->setname('group')->getDataList(['status' => 1, 'create_time|>' => time() - 3600 * 24], true, 'create_time desc', false, '', '', 10);

        $grouplist = self::$datalogic->setname('group')->getDataList($where, true, 'choice desc,sort desc');
        $this->assign('newhtlist', $newhtlist);

        $this->assign('grouplist', $grouplist['data']);
        $this->assign('grouplistpage', $grouplist['page']);

        return $this->fetch();

    }

}
