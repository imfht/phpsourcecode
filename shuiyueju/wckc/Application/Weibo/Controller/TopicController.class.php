<?php
/**
 *
 * @author quick
 *
 */
namespace Weibo\Controller;

use Think\Controller;
use Weibo\Api\WeiboApi;
use Common\Api\Api;

class TopicController extends Controller
{
    private $weiboApi;

    public function _initialize()
    {
        $this->weiboApi = new WeiboApi();
    }

    public function index($topk = '', $uid = 0, $lastId = 0)
    {

        $aTopic = urldecode(I('topk', '', 'op_t'));
        $aPage = I('page', 1, 'intval');
        $aUid = I('uid', 0, 'intval');
        $topicModel = D("Topic");
        $topic = $topicModel->where(array('name' => $aTopic))->find();
        if (!$topic) {
            $this->error('没有这个话题，赶紧去创建吧！', U('Weibo/Index/index'));
        }
        $topicModel->where('name = "' . $topk . '"')->setInc('read_count', 1);//浏览正确的话题就应该给该话题+1浏览量
        //获取微博列表
        if (isset($topk) && !empty($topk)) {
            $map['content'] = array('like', "%#{$aTopic}#%");
        }

        //载入第一页微博
        if ($aUid) {
            $map['uid'] = array('eq', $aUid);
            $result = $this->weiboApi->listAllWeibo($aPage, null, $map, 1, $lastId);
        } else {
            $result = $this->weiboApi->listAllWeibo($aPage, 0, $map, 1, $lastId);
        }
        if ($topic['uadmin'] != 0) {
            $host = $this->getUserStructure($topic['uadmin']);//话题主持人
            $host['status'] = 1;
        } else {
            $host = $this->getUserStructure(is_login());
            $host['status'] = 0;
        }
        //显示页面
        $this->assign('list', $result['list']);
        $this->assign('lastId', $result['lastId']);
        $this->assign('page', $aPage);
        $this->assign('tab', 'all');
        $this->assign('loadMoreUrl', U('loadWeibo', array('uid' => $uid, 'keywords' =>urlencode('#' . $topk . '#'))));
        $total_count = $this->weiboApi->listAllWeiboCount($map, '%#' . $topk . '#%');
        $this->assign('total_count', $total_count['total_count']);
        $this->assign('topic', $topic);
        $this->assign('host', $host);

        $this->assignSelf();
        $this->setTitle('{$topic.name|op_t} —— 话题');
        $this->display();
    }
    private function assignSelf()
    {
        $self = query_user(array('title', 'avatar128', 'nickname', 'uid', 'space_url', 'icons_html', 'score', 'title', 'fans', 'following', 'weibocount', 'rank_link'));
        $this->assign('self', $self);
    }
    public function loadWeibo($topk = '', $page = 1, $uid = 0, $loadCount = 1, $lastId = 0)
    {
        $count = 30;
        $aTopic = I('get.', '', 'op_t');
        //获取微博列表
        if ($aTopic!='') {
            $map['content'] = array('like', "%{$aTopic}%");
        }
        //载入全站微博

        if ($uid != 0) {
            $map['uid'] = array('eq', $uid);
            $result = $this->weiboApi->listAllWeibo($page, $count, $map, $loadCount, $lastId);
        } else {
            $result = $this->weiboApi->listAllWeibo($page, $count, $map, $loadCount, $lastId);
        }
        //如果没有微博，则返回错误
        if (!$result['list']) {
            $this->error('没有更多了');
        }

        //返回html代码用于ajax显示
        $this->assign('list', $result['list']);
        $this->assign('lastId', $result['lastId']);
        $this->display();
    }

    protected function getUserStructure($uid)
    {
        //请不要在这里增加用户敏感信息，可能会暴露用户隐私
        $fields = array('uid', 'nickname', 'avatar32', 'avatar64', 'avatar128', 'avatar256', 'avatar512', 'space_url', 'icons_html', 'rank_link', 'signature', 'score', 'tox_money', 'title', 'weibocount', 'fans', 'following');
        return query_user($fields, $uid);
    }

    public function beadmin()
    {
        if (!is_login()) {
            $this->error('必须先登录才能申请成为主持人。');
        }
        $tid = I('tid', 0, 'intval');
        $topicModel = D('Topic');
        $topic = $topicModel->find($tid);
        if ($topic) {
            if ($topic['uadmin']) {
                //已经存在管理员
                $this->error('已经有人捷足先登了呢。申请没有成功。');
            } else {
                if (is_administrator() || check_auth('beTopicAdmin')) {
                    $topic['uadmin'] = is_login();
                    $result = $topicModel->save($topic);
                    if ($result) {
                        $this->success('恭喜，您已抢先成为本话题的主持人。', 'refresh');
                    } else {
                        $this->error('抱歉，操作失败。可能是数据库原因导致。请联系管理员。');
                    }
                } else {
                    $this->error('抱歉，您无权申请成为话题主持人。');
                }
            }
        } else {
            $this->error('抱歉，此话题不存在。');
        }

    }

    public function editTopic()
    {
        $aId = I('id', -1, 'intval');
        $aLogo = I('logo', 0, 'intval');
        $aQrcode = I('qrcode', 0, 'intval');
        $aIntro = I('intro', '', 'op_t');
        $aIsTop = I('is_top', 0, 'intval');
        $aUadmin = I('uadmin', 0, 'intval');
        $topicModel = M('Topic');

        $topic = $topicModel->find($aId);
        if (!$topic) {
            $this->error('话题不存在。');
        } else {
            if (check_auth('manageTopic') || $topic['uadmin'] == get_uid()) {
                $topic['logo'] = $aLogo;
                $topic['qrcode'] = $aQrcode;
                if ($topic['intro'] != $aIntro && $topic['is_top'] == 1) {
                    S('topic_rank', null);
                }
                $topic['intro'] = $aIntro;

                if (check_auth('manageTopic')) {
                    if ($topic['is_top'] != $aIsTop) {
                        S('topic_rank', null);
                    }
                    $topic['uadmin'] = $aUadmin;
                    $topic['is_top'] = $aIsTop;

                }
                $result = $topicModel->save($topic);
                if ($result === false) {
                    $this->error('设置失败。');
                } else {
                    $this->success('设置成功。', 'refresh');
                }
                exit;
                //允许管理话题
            } else {
                $this->error('您不具备管理权限。');
            }
        }
    }
}