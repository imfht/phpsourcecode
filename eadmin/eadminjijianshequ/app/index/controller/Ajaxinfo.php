<?php

namespace app\index\controller;

use app\common\controller\HomeBase;
use esclass\database;


class Ajaxinfo extends HomeBase
{


    public function _initialize()
    {
        parent::_initialize();

    }

    public function gethomedata($uid, $actions, $page)
    {
        $userinfo = db('user')->where(['id' => $uid])->getRow();
        $orderstr = 'create_time desc';
        if ($actions == 1) {//关注的话题

            $where['type'] = 2;
            $where['uid']  = $uid;
            $list          = db('user_focus')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {

                $result = '';

                foreach ($list as $key => $vo) {

                    $groupinfo = db('group')->where(['id' => $vo['sid']])->getRow();
                    if ($groupinfo) {
                        $result .= '<li><div class="mod-head">';
                        $result .= '<a class="aw-topic-img pull-left aw-border-radius-5" data-id="62" href="' . es_url('Topic/index', ['name' => $groupinfo['name']]) . '">';
                        $result .= '<img src="' . get_picture_url($vo['cover_id']) . '" class="middleimg" alt="' . $groupinfo['name'] . '"></a>';
                        $result .= '<p><a class="aw-topic-name"  href="' . es_url('Topic/index', ['name' => $groupinfo['name']]) . '"><span>' . $groupinfo['name'] . '</span></a></p>';
                        $result .= '</div><div class="mod-footer"><p class="aw-user-center-follow-meta">' . $groupinfo['topiccount'] . ' 个帖子 •	' . $groupinfo['membercount'] . ' 个关注</p>';
                        $result .= '</div></li>';
                    }

                }
                if ($result == '') {
                    $this->jump(RESULT_ERROR, '获取失败');
                } else {
                    $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
                }

            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }


        }
        if ($actions == 4) {//关注者

            $where['type'] = 3;
            $where['uid']  = $uid;
            $list          = db('user_focus')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {
                $result = '';

                foreach ($list as $key => $vo) {

                    $fsuserinfo = db('user')->where(['id' => $vo['sid']])->getRow();
                    if ($fsuserinfo) {
                        $result .= '<li><div class="mod-head">';


                        $result .= '<a class="aw-user-img pull-left aw-border-radius-5" data-id="' . $fsuserinfo['id'] . '" href="' . es_url('user/home', ['id' => $fsuserinfo['id']]) . '">';
                        $result .= '<img src="' . getheadurl($fsuserinfo['userhead']) . '" class="middleimg" alt="' . $fsuserinfo['nickname'] . '"></a>';
                        $result .= '<p><a href="' . es_url('user/home', ['id' => $fsuserinfo['id']]) . '">' . $fsuserinfo['nickname'] . '</a></p>';
                        $result .= '</div>';
                        $result .= '<div class="mod-footer meta"><span><i class="iconfont icon-jifen"></i>金币 <em class="aw-text-color-green">' . $fsuserinfo['point'] . '</em></span><span><i class="iconfont icon-huoyuedu"></i> 经验 : <em class="aw-text-color-orange">' . $fsuserinfo['expoint1'] . '</em></span></div></li>';

                    }
                }


                if ($result == '') {
                    $this->jump(RESULT_ERROR, '获取失败');
                } else {
                    $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
                }


            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }


        }
        if ($actions == 5) {//粉丝

            $where['type'] = 3;
            $where['sid']  = $uid;
            $list          = db('user_focus')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {
                $result = '';

                foreach ($list as $key => $vo) {

                    $fsuserinfo = db('user')->where(['id' => $vo['uid']])->getRow();
                    if ($fsuserinfo) {
                        $result .= '<li><div class="mod-head">';


                        $result .= '<a class="aw-user-img pull-left aw-border-radius-5" data-id="' . $fsuserinfo['id'] . '" href="' . es_url('user/home', ['id' => $fsuserinfo['id']]) . '">';
                        $result .= '<img src="' . getheadurl($fsuserinfo['userhead']) . '" class="middleimg" alt="' . $fsuserinfo['nickname'] . '"></a>';
                        $result .= '<p><a href="' . es_url('user/home', ['id' => $fsuserinfo['id']]) . '">' . $fsuserinfo['nickname'] . '</a></p>';
                        $result .= '</div>';
                        $result .= '<div class="mod-footer meta"><span><i class="iconfont icon-jifen"></i>金币 <em class="aw-text-color-green">' . $fsuserinfo['point'] . '</em></span><span><i class="iconfont icon-huoyuedu"></i> 经验 : <em class="aw-text-color-orange">' . $fsuserinfo['expoint1'] . '</em></span></div></li>';
                    }
                }

                if ($result == '') {
                    $this->jump(RESULT_ERROR, '获取失败');
                } else {
                    $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
                }
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }


        }
        if ($actions == 6) {//帖子

            $where['status'] = 1;
            $where['uid']    = $uid;
            $list            = db('topic')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {
                $result = '';

                foreach ($list as $key => $vo) {


                    $result .= '<div class="aw-item"><div class="aw-mod"><div class="mod-head"><h4 class="aw-hide-txt"><a href="' . es_url('Topic/gview', ['id' => $vo['id']]) . '">' . $vo['title'] . '</a></h4>';


                    $result .= '</h4></div><div class="mod-body">';
                    $result .= '<span class="aw-border-radius-5 count pull-left"><i class="iconfont icon-zan2"></i>' . $vo['praise'] . '</span>';
                    $result .= '<p class="text-color-999">' . $vo['view'] . ' 次浏览 • ' . $vo['reply'] . ' 个回复 • ' . friendlyDate($vo['create_time']) . '</p>';

                    $result .= '</div></div></div>';
                }


                $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }


        }
        if ($actions == 7) {//关注的帖子

            $where['type'] = 1;
            $where['uid']  = $uid;
            $list          = db('user_focus')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {
                $result = '';

                foreach ($list as $key => $vo) {
                    $topicinfo = db('topic')->where(['id' => $vo['sid']])->getRow();
                    if ($topicinfo) {
                        $result .= '<div class="aw-item"><div class="aw-mod"><div class="mod-head"><h4 class="aw-hide-txt"><a href="' . es_url('Topic/gview', ['id' => $topicinfo['id']]) . '">' . $topicinfo['title'] . '</a></h4>';


                        $result .= '</h4></div><div class="mod-body">';
                        $result .= '<span class="aw-border-radius-5 count pull-left"><i class="iconfont icon-zan2"></i>' . $topicinfo['praise'] . '</span>';
                        $result .= '<p class="text-color-999">' . $topicinfo['view'] . ' 次浏览 • ' . $topicinfo['reply'] . ' 个回复 • ' . friendlyDate($topicinfo['create_time']) . '</p>';

                        $result .= '</div></div></div>';
                    }
                }

                if ($result == '') {
                    $this->jump(RESULT_ERROR, '获取失败');
                } else {
                    $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
                }
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }


        }


        if ($actions == 2) {//评论
            $where['pid'] = 0;
            $where['uid'] = $uid;
            $list         = db('comment')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {
                $result = '';
                //1表示赞帖子2表示浏览帖子3表示收藏帖子4取消收藏5关注话题6取消关注话题7关注用户8取消关注用户9表示赞评论10反对评论11发帖12评论13删除评论14删除帖子15访问主页16邀请注册17注册18登录19分享20打赏
                foreach ($list as $key => $vo) {

                    $topicinfo = db('topic')->where(['id' => $vo['fid']])->getRow();

                    if ($topicinfo) {
                        $result .= '<div class="aw-item"><div class="aw-mod"><div class="mod-head"><h4 class="aw-hide-txt"><a href="' . es_url('Topic/gview', ['id' => $topicinfo['id']]) . '">' . $topicinfo['title'] . '</a></h4>';

                        $result .= '</div><div class="mod-body"><span class="aw-border-radius-5 count pull-left"><i class="iconfont icon-zan2"></i>' . $vo['ding'] . '</span>';
                        $result .= '<p class="text-color-999">' . htmlspecialchars_decode($vo['content']) . '</p>';
                        $result .= '<p class="text-color-999">' . friendlyDate($vo['create_time']) . '</p>';
                        $result .= '</div></div></div>';
                    }
                }

                if ($result == '') {
                    $this->jump(RESULT_ERROR, '获取失败');
                } else {
                    $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
                }
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }


        }


        if ($actions == 3) {//动态
            $where['type'] = [11, 12];


            $where['uid'] = $uid;
            $list         = db('homeaction_log')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {
                $result = '';
                //1表示赞帖子2表示浏览帖子3表示收藏帖子4取消收藏5关注话题6取消关注话题7关注用户8取消关注用户9表示赞评论10反对评论11发帖12评论13删除评论14删除帖子15访问主页16邀请注册17注册18登录19分享20打赏
                foreach ($list as $key => $vo) {

                    if ($vo['type'] == 11) {
                        $result .= '<div class="aw-item"><p style="overflow: hidden;"><span class="pull-right text-color-999">' . friendlyDate($vo['create_time']) . '</span>';

                        $result .= '<em class="pull-left"><a href="' . es_url('user/home', ['id' => $userinfo['id']]) . '" class="aw-user-name" data-id="' . $userinfo['id'] . '">' . $userinfo['nickname'] . '</a>';

                        $result .= '发布了帖子</em><a class="aw-hide-txt" href="' . es_url('Topic/gview', ['id' => $vo['sid']]) . '">' . $vo['describe'] . '</a></p></div>';
                    }


                    if ($vo['type'] == 12) {
                        $commentinfo = db('comment')->where(['id' => $vo['sid']])->getRow();
                        if ($commentinfo) {
                            $topicinfo = db('topic')->where(['id' => $commentinfo['fid']])->getRow();
                            if ($topicinfo) {
                                $result .= '<div class="aw-item"><p style="overflow: hidden;"><span class="pull-right text-color-999">' . friendlyDate($vo['create_time']) . '</span>';

                                $result .= '<em class="pull-left"><a href="' . es_url('user/home', ['id' => $userinfo['id']]) . '" class="aw-user-name" data-id="' . $userinfo['id'] . '">' . $userinfo['nickname'] . '</a>';

                                $result .= '在<a class="aw-topic-name" href="' . es_url('Topic/gview', ['id' => $commentinfo['fid']]) . '">' . $topicinfo['title'] . '</a>帖子中发布了评论</em></p></div>';
                            }
                        }


                    }


                }


                if ($result == '') {
                    $this->jump(RESULT_ERROR, '获取失败');
                } else {
                    $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
                }
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }


        }


    }

//获取用户信息
    public function user_info($uid)
    {

        $nowuid = is_login();

        $info = db('user')->where(['id' => $uid])->getRow();

        if ($info) {
            $info['userhead'] = getheadurl($info['userhead']);
            $info['fscount']  = db('user_focus')->where(['sid' => $uid, 'type' => 3])->count();
            $info['focus']    = user_has_focus($nowuid, $uid);
            $verified         = db('rzuser')->where(['uid' => $uid])->getRow();
            $info['url']      = es_url('user/home', ['id' => $uid]);
            if ($verified && $verified['status'] == 1) {
                $info['verified'] = $verified['type'];
            } else {
                $info['verified'] = '';
            }


            $this->jump(RESULT_SUCCESS, '获取成功', '', $info);
        } else {
            $this->jump(RESULT_ERROR, '获取失败');
        }


    }

    public function gethtlist()
    {

        $data = $this->param;

        empty($data['query']) && $data['query'] = '';

        $data['query'] = urldecode($data['query']);


        $htlist = self::$datalogic->setname('group')->getDataList(['status' => 1, 'name|~' => $data['query']], 'name as label,name as value', 'create_time desc', false);


        echo json_encode($htlist);
        return;
    }

    //关注动作函数
    public function focus($type, $id)
    {

        $nowuid = is_login();

        if ($nowuid == 0) {
            $this->jump(RESULT_ERROR, '用户未登录');
        }

        $data['type'] = $type;

        $data['uid'] = $nowuid;

        $data['sid'] = $id;

        $focusinfo = db('user_focus')->where($data)->getRow();

        if ($focusinfo) {
            //取消关注
            db('user_focus')->where($data)->delete();

            $type = $type * 2 + 2;

            homeaction_log($nowuid, $type, $id);

            $this->jump(RESULT_SUCCESS, '取消成功', '', ['qx' => 1]);
        } else {
            //关注
            db('user_focus')->insert($data);

            $type = $type * 2 + 1;

            homeaction_log($nowuid, $type, $id);

            $this->jump(RESULT_SUCCESS, '关注成功', '', ['qx' => 2]);
        }


    }

    public function agreet($cid)
    {

        $nowuid = is_login();

        if ($nowuid == 0) {
            $this->jump(RESULT_ERROR, '用户未登录');
        }

        $info = db('homeaction_log')->where(['type' => 1, 'sid' => $cid, 'uid' => $nowuid])->getRow();
        if ($info) {
            $this->jump(RESULT_ERROR, '已表达过态度');
        } else {
            homeaction_log($nowuid, 1, $cid);
            self::$datalogic->setname('topic')->setIncOrDec(['id' => $cid], 'praise', 1);
            $this->jump(RESULT_SUCCESS, '表达态度成功');
        }


    }

    public function agreec($cid)
    {

        $nowuid = is_login();

        if ($nowuid == 0) {
            $this->jump(RESULT_ERROR, '用户未登录');
        }

        $info = db('homeaction_log')->where(['type' => 9, 'sid' => $cid, 'uid' => $nowuid])->getRow();
        if ($info) {
            $this->jump(RESULT_ERROR, '已表达过态度');
        } else {
            homeaction_log($nowuid, 9, $cid);
            self::$datalogic->setname('comment')->setIncOrDec(['id' => $cid], 'ding', 1);
            $this->jump(RESULT_SUCCESS, '表达态度成功');
        }


    }

    public function disagreec($cid)
    {

        $nowuid = is_login();

        if ($nowuid == 0) {
            $this->jump(RESULT_ERROR, '用户未登录');
        }

        $info = db('homeaction_log')->where(['type' => 10, 'sid' => $cid, 'uid' => $nowuid])->getRow();
        if ($info) {
            $this->jump(RESULT_ERROR, '已表达过态度');
        } else {
            homeaction_log($nowuid, 10, $cid);
            self::$datalogic->setname('comment')->setIncOrDec(['id' => $cid], 'cai', 1);
            $this->jump(RESULT_SUCCESS, '表达态度成功');
        }


    }

    public function getpinglun($id)
    {
        $nowuid = is_login();

        $id = $this->param['id'];

        $data = db('comment', 'm')->where(['m.pid' => $id])->field('m.*,user.nickname,user.userhead')->order('m.create_time asc')->join(['user|user', 'user.id=m.uid', 'INNER'])->getList();

        if ($data) {
            $result = '<ul>';
            foreach ($data as $k => $v) {
                $result .= '<li>';
                $result .= '<a class="aw-user-name" href="' . es_url('user/home', ['id' => $v['uid']]) . '" data-id="' . $v['uid'] . '">';
                $result .= '<img src="' . getheadurl($v['userhead']) . '" alt="' . $v['nickname'] . '">';
                $result .= '</a><div><p class="clearfix"><span class="pull-right">';

                if ($nowuid == $v['uid']) {
                    $result .= '<a href="javascript:;" onclick="AWS.User.remove_comment($(this).parent(), ' . $v['id'] . ');">删除</a>&nbsp;';
                }
                $result .= '<a href="javascript:;" onclick=';
                $result .= 'getplusername(this,"' . $v['nickname'] . '")';
                $result .= '>回复</a>';
                $result .= '</span><a href="' . es_url('user/home', ['id' => $v['uid']]) . '" class="aw-user-name author" data-id="' . $v['uid'] . '">' . $v['nickname'] . '</a> •';
                $result .= '<span>' . friendlyDate($v['create_time']) . '</span></p>	<p class="clearfix">' . htmlspecialchars_decode($v['content']) . '</p>	</div></li>';


            }
            $result .= '</ul>';
            $this->jump(RESULT_SUCCESS, '获取评论成功', '', ['data' => $result]);

        } else {
            $this->jump(RESULT_ERROR, '获取评论为空');
        }


    }

    public function addpinglun()
    {//添加二级评论

        $nowuid = is_login();
        $id     = $this->param['id'];
        if ($nowuid == 0) {
            $this->jump(RESULT_ERROR, '用户未登录');
        }
        $userinfo = self::$datalogic->setname('user')->getDataInfo(['id' => $nowuid]);
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
        $info = db('comment')->where(['id' => $id])->getRow();

        $data['uid'] = $nowuid;
        $data['fid'] = $info['fid'];
        $cinfo       = self::$datalogic->setname('comment')->getStat($data, 'max', 'create_time');
        if (time() - $cinfo < 60) {
            $this->jump([RESULT_ERROR, '两次评论时间过短']);
        }
        $data['pid'] = $id;


        $data['floor']   = 0;
        $data['content'] = htmlspecialchars_decode(string_remove_xss($this->param['content']));
        self::$datalogic->setname('comment')->setIncOrDec(['id' => $id], 'reply', 1);

        $describe  = '"' . msubstr(clearHtml(htmlspecialchars_decode($info['content'])), 0, 60) . '"';
        $describe1 = '"' . msubstr(clearHtml(htmlspecialchars_decode($data['content'])), 0, 60) . '"';
        sendsysmess('你的评论' . $describe . '有了新回复' . $describe1 . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $info['fid']]) . '">链接</a>', 0, $info['uid'], 1);


        preg_match_all('/\@(.*?):/i', $data['content'], $matches);
        if ($matches) {
            $match = $matches[1];

            foreach ($match as $k => $v) {
                $uinfo = db('user')->where(['nickname' => $v])->getRow();
                if ($uinfo) {
                    sendsysmess('有人在评论中@了你' . $describe1 . '&nbsp;<a href="' . es_url('topic/gview', ['id' => $info['fid']]) . '">链接</a>', 0, $uinfo['id'], 1);
                }

            }


        }


        $this->jump(self::$datalogic->setname('comment')->dataAdd($data, true, '', ['type_name' => 'answer', 'item_id' => $id], '', function ($result, $data) {
            homeaction_log($data['uid'], 12, $result);


        }));


    }

    public function delpinglun($id)
    {
        $nowuid = is_login();

        if ($nowuid == 0) {
            $this->jump(RESULT_ERROR, '用户未登录');
        }

        $info = db('comment')->where(['id' => $id])->getRow();
        if ($nowuid != $info['uid']) {
            $this->jump(RESULT_ERROR, '非法操作');
        }
        if ($info['pid'] > 0) {

            self::$datalogic->setname('comment')->setIncOrDec(['id' => $info['pid']], 'reply', 1, '-');
        } else {
            self::$datalogic->setname('topic')->setIncOrDec(['id' => $info['fid']], 'reply', 1, '-');
        }
        homeaction_log($nowuid, 13, $id);
        db('comment')->where(['id' => $id])->delete();


        $this->jump(RESULT_SUCCESS, '删除成功');


    }

    public function search()
    {

        $uid = is_login();

        $orderstr = 'create_time desc';

        $data = $this->param;
        $page = $data['page'];

        if ($data['type'] == 'topics') {
            $where['title|~'] = $data['q'];
            $list             = db('topic', 'm')->where($where)->field('user.nickname,user.point,user.expoint1,user.userhead,rzuser.type as rztype,rzuser.status as rzstatus,m.*')->order($orderstr)->join(['user|user', 'user.id=m.uid'])->join(['rzuser|rzuser', 'rzuser.uid=m.uid'])->page($page . ',10')->getList();
            if ($list) {

                foreach ($list as $key => $vo) {


                    $focuscount = db('user_focus')->where(['sid' => $vo['id'], 'type' => 1])->count();

                    $list[$key]['url']        = es_url('Topic/gview', ['id' => $vo['id']]);
                    $list[$key]['focuscount'] = $focuscount;
                }


                $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $list]);
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }
        }


        if ($data['type'] == 'ht') {
            $where['name|~'] = $data['q'];
            $list            = db('group')->where($where)->order($orderstr)->page($page . ',10')->getList();
            if ($list) {

                foreach ($list as $key => $vo) {
                    $list[$key]['url'] = es_url('Topic/index', ['name' => $vo['name']]);
                    $list[$key]['img'] = get_picture_url($vo['cover_id']);
                }

                $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $list]);
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }
        }
        if ($data['type'] == 'users') {
            $where['nickname|~'] = $data['q'];
            $list                = db('user')->where($where)->order('last_login_time desc')->page($page . ',10')->getList();
            if ($list) {
                $result = '';
                foreach ($list as $key => $vo) {

                    $list[$key]['userhead'] = getheadurl($vo['userhead']);
                    $list[$key]['userurl']  = es_url('user/home', ['id' => $vo['id']]);


                }


                $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $list]);
            } else {
                $this->jump(RESULT_ERROR, '获取失败');
            }

        }

    }

    public function searchajax()
    {

        $uid = is_login();

        $orderstr = 'create_time desc';

        $data              = $this->param;
        $limit             = $data['limit'];
        $listarr           = [];
        $count             = 0;
        $htwhere['name|~'] = $data['q'];
        $htlist            = db('group')->where($htwhere)->order($orderstr)->limit($limit)->getList();//话题
        if ($htlist) {

            foreach ($htlist as $key => $vo) {
                $listarr[$count]['type']       = 'topics';
                $listarr[$count]['topiccount'] = $vo['topiccount'];
                $listarr[$count]['name']       = $vo['name'];
                $listarr[$count]['url']        = es_url('Topic/index', ['name' => $vo['name']]);
                $count++;
            }
        }
        $topicwhere['title|~'] = $data['q'];
        $topiclist             = db('topic')->where($topicwhere)->limit($limit)->getList();
        if ($topiclist) {

            foreach ($topiclist as $key => $vo) {
                $listarr[$count]['type']  = 'articles';
                $listarr[$count]['reply'] = $vo['reply'];
                $listarr[$count]['name']  = $vo['title'];
                $listarr[$count]['url']   = es_url('Topic/gview', ['id' => $vo['id']]);
                $count++;
            }

        }
        $userwhere['nickname|~'] = $data['q'];
        $userlist                = db('user')->where($userwhere)->order('last_login_time desc')->limit($limit)->getList();
        if ($userlist) {

            foreach ($userlist as $key => $vo) {
                $listarr[$count]['type']        = 'users';
                $listarr[$count]['signature']   = $vo['description'];
                $listarr[$count]['name']        = $vo['nickname'];
                $listarr[$count]['avatar_file'] = getheadurl($vo['userhead']);
                $listarr[$count]['url']         = es_url('user/home', ['id' => $vo['id']]);
                $count++;
            }
        }

        if ($count > 0) {

            $this->jump(RESULT_SUCCESS, '获取成功', '', $listarr);
        } else {
            $this->jump(RESULT_ERROR, '获取失败');
        }


    }


    public function gettopic($order, $id, $page)
    {
        $uid = is_login();
        if ($order == 'new') {
            $orderstr = 'create_time desc';
        }
        if ($order == 'tj') {
            $where['choice'] = 1;
        }
        if ($order == 'mysc') {

            $sidarr = db('user_focus')->where(['uid' => $uid])->column('sid');

            $where['id'] = $sidarr;

            $orderstr = 'create_time desc';
        }


        $where['gidtext|~'] = $id;

        $list = db('topic', 'm')->where($where)->field('user.nickname,user.point,user.expoint1,user.userhead,rzuser.type as rztype,rzuser.status as rzstatus,m.*')->order($orderstr)->join(['user|user', 'user.id=m.uid'])->join(['rzuser|rzuser', 'rzuser.uid=m.uid'])->page($page . ',10')->getList();
        if ($list) {
            $result = '';
            foreach ($list as $key => $vo) {

                if ($vo['rzstatus'] && $vo['rzstatus'] == 1) {

                    if ($vo['rztype'] == 1) {
                        $rzicon = 'icon-myvip';
                    } else {
                        $rzicon = 'icon-myvip i-ve';
                    }

                } else {
                    $rzicon = 'hide';
                }

                $list[$key]['userhead'] = getheadurl($vo['userhead']);
                $list[$key]['userurl']  = es_url('User/home', ['id' => $vo['uid']]);
                $list[$key]['url']      = es_url('Topic/gview', ['id' => $vo['id']]);
                $list[$key]['rzicon']   = $rzicon;
                $focuscount             = db('user_focus')->where(['sid' => $vo['id'], 'type' => 1])->count();
                $cinfo                  = db('comment')->where(['fid' => $vo['id'], 'pid' => 0])->order('create_time desc')->limit(1)->getList();


                if ($cinfo) {
                    $replystr  = '回复 ' . friendlyDate($cinfo[0]['create_time']) . ' (' . $focuscount . '人关注)';
                    $replyuser = getusernamebyid($cinfo[0]['uid']);
                    $replyuid  = $cinfo[0]['uid'];
                } else {
                    $replystr = '发布 ' . friendlyDate($vo['create_time']) . ' (' . $focuscount . '人关注)';
                }
                $list[$key]['replystr']   = $replystr;
                $list[$key]['focuscount'] = $focuscount;
            }


            $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $list]);
        } else {
            $this->jump(RESULT_ERROR, '获取失败');
        }


    }

    public function getdt($page)
    {

        //帖子有了新评论，话题增加了帖子
        $where['type'] = [11, 12];
        $orderstr      = 'create_time desc';

        $uid = is_login();


        $list = db('homeaction_log')->where($where)->order($orderstr)->page($page . ',10')->getList();


        if ($list) {
            $result = '';
            foreach ($list as $key => $vo) {

                if ($vo['type'] == 11) {
                    //发布帖子
                    $tpinfo   = db('topic')->where(['id' => $vo['sid']])->getRow();
                    $userinfo = db('user')->where(['id' => $tpinfo['uid']])->getRow();

                    if ($tpinfo && $userinfo) {

                        $result .= '<div class="aw-item" >';
                        $result .= '<div class="mod-head">';

                        $result .= '<a data-id="' . $userinfo['id'] . '" class="aw-user-img aw-border-radius-5" href="' . es_url('user/home', ['id' => $userinfo['id']]) . '" rel="nofollow"><img src="' . getheadurl($userinfo['userhead']) . '" alt="' . $userinfo['nickname'] . '" class="middleimg"></a>';
                        $result .= '<p class="text-color-999">';
                        $result .= '<a href="' . es_url('user/home', ['id' => $userinfo['id']]) . '" class="aw-user-name" data-id="' . $userinfo['id'] . '">' . $userinfo['nickname'] . '</a> 增加了帖子';
                        if ($tpinfo['gidtext']) {
                            $result .= ',发布在话题"' . $tpinfo['gidtext'] . '"';
                        }
                        $result .= '</p>';
                        $result .= '<h4><a href="' . es_url('Topic/gview', ['id' => $tpinfo['id']]) . '">' . $tpinfo['title'] . '</a></h4></div>';
                        if (!$tpinfo['description']) {

                            $tpinfo['description'] = msubstr(clearHtml(htmlspecialchars_decode($tpinfo['content'])), 0, 60);
                        }
                        $result .= '<div class="mod-body clearfix"><div id="detail_' . $tpinfo['id'] . '" class="markitup-box">' . msubstr(clearHtml(htmlspecialchars_decode($tpinfo['description'])), 0, 60) . '</div></div></div>';
                    }

                }
                if ($vo['type'] == 12) {
                    //发布帖子评论
                    $tpinfo = db('comment')->where(['id' => $vo['sid']])->getRow();
                    if ($tpinfo) {

                        $tinfo = db('topic')->where(['id' => $tpinfo['fid']])->getRow();


                        $userinfo = db('user')->where(['id' => $tpinfo['uid']])->getRow();

                        if ($userinfo && $tinfo) {
                            $result .= '<div class="aw-item" >';
                            $result .= '<div class="mod-head">';

                            $result .= '<a data-id="' . $userinfo['id'] . '" class="aw-user-img aw-border-radius-5" href="' . es_url('user/home', ['id' => $userinfo['id']]) . '" rel="nofollow"><img src="' . getheadurl($userinfo['userhead']) . '" alt="' . $userinfo['nickname'] . '" class="middleimg"></a>';
                            $result .= '<p class="text-color-999">';
                            $result .= '<a href="' . es_url('user/home', ['id' => $userinfo['id']]) . '" class="aw-user-name" data-id="' . $userinfo['id'] . '">' . $userinfo['nickname'] . '</a> 在帖子中增加了评论';

                            $result .= '</p>';
                            $result .= '<h4><a href="' . es_url('Topic/gview', ['id' => $tinfo['id']]) . '">' . $tinfo['title'] . '</a></h4></div>';


                            $tpinfo['description'] = msubstr(clearHtml(htmlspecialchars_decode($tpinfo['content'])), 0, 60);

                            $result .= '<div class="mod-body clearfix"><div id="detail_' . $tpinfo['id'] . '" class="markitup-box">' . msubstr(clearHtml(htmlspecialchars_decode($tpinfo['description'])), 0, 60) . '</div></div></div>';
                        }


                    }


                }


            }


            if ($result == '') {
                $this->jump(RESULT_ERROR, '获取失败');
            } else {
                $this->jump(RESULT_SUCCESS, '获取成功', '', ['data' => $result]);
            }


        } else {
            $this->jump(RESULT_ERROR, '获取失败');
        }


    }
}
