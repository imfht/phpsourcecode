<?php
/**
 * 所属项目 OnePlus.
 * 开发者: 想天
 * 创建日期: 3/12/14
 * 创建时间: 12:49 PM
 * 版权所有 想天工作室(www.ourstu.com)
 */

namespace Ucenter\Controller;


use Think\Controller;

class PublicController extends Controller
{
    /**获取个人资料，用以支持小名片
     * @auth 陈一枭
     */
    private function getProfile($uid)
    {
        $uid = intval($_REQUEST['uid']);
        $userProfile = query_user(array('uid', 'nickname', 'avatar64', 'space_url', 'following', 'fans', 'weibocount', 'signature', 'rank_link'), $uid);
        $follow['follow_who'] = $userProfile['uid'];
        $follow['who_follow'] = is_login();
        $userProfile['followed'] = D('Follow')->where($follow)->count();
        $userProfile['following_url'] = U('Ucenter/Index/following', array('uid' => $uid));
        $userProfile['fans_url'] = U('Ucenter/Index/fans', array('uid' => $uid));
        $userProfile['weibo_url'] = U('Ucenter/Index/appList', array('uid' => $uid, 'type' => "weibo"));
        $html = '';
        if (count($userProfile['rank_link'])) {
            foreach ($userProfile['rank_link'] as $val) {
                if ($val['is_show']) {
                    if (empty($val['label_content'])) {
                        $html = $html . '<img class="img-responsive" src="' . $val['logo_url'] . '" title="' . $val['title'] . '" alt="' . $val['title'] . '" style="width: 18px;height: 18px;vertical-align: middle;margin-left: 3px;display: inline;"/>';
                    } else {
                        $html = $html . '<span class="label label-badge rank-label" title="' . $val['title'] . '" style="background:' . $val['label_bg'] . ' !important;color:' . $val['label_color'] . ' !important;vertical-align: middle;margin-left: 3px;">' . $val['label_content'] . '</span>';
                    }
                }
            }
            unset($val);
        }
        $userProfile['rank_link'] = $html;
        //获取用户封面path
        $map = getUserConfigMap('user_cover', '', $uid);
        $map['role_id'] = 0;
        $model = D('Ucenter/UserConfig');
        $cover = $model->findData($map);
        if ($cover) {
            $userProfile['cover_path'] = getThumbImageById($cover['value'], 344, 100);
        } else {
            $userProfile['cover_path'] = __ROOT__ . '/Public/images/qtip_bg.png';
        }
        //个人标签
        $userProfile['tags'] = '';
        $userTagLinkModel = D('Ucenter/UserTagLink');
        $myTags = $userTagLinkModel->getUserTag($uid);
        if (count($myTags)) {
            $userProfile['tags'] = L('_PERSONAL_TAB_').L('_COLON_').'<span>';
            $first = 1;
            foreach ($myTags as $val) {
                if ($first) {
                    $userProfile['tags'] .= '<a style="color: #848484;"  href="' . U('people/index/index', array('tag' => $val['id'])) . '">' . $val['title'] . '</a>';
                    $first = 0;
                } else {
                    $userProfile['tags'] .= '、<a style="color: #848484;"  href="' . U('people/index/index', array('tag' => $val['id'])) . '">' . $val['title'] . '</a>';
                }
            }
            $userProfile['tags'] .= '</span>';
        }
        return $userProfile;
    }


    public function card()
    {
        $aUID = I('get.uid', 0, 'intval');
        $user = $this->getProfile($aUID);
        $follow=D('Common/Follow')->isFollow(is_login(),$aUID);
        $this->assign('follow',$follow);

        $this->assign('uid', $aUID);
        $this->assign('user', $user);
        $not_self = get_uid() != $aUID;
        $this->assign('not_self',$not_self);
        $this->display();
    }

    public function setAlias()
    {
        $aUid = I('post.uid', 0, 'intval');
        $aAlias =trim(I('post.alias', '', 'text'));
        if($aAlias==''){
            $this->error(L('_ERROR_REMARK_CANNOT_EMPTY_').L('_PERIOD_'));
        }
        if (is_login()) {
            $followModel = D('Common/Follow');
            $follow['who_follow'] = get_uid();
            $follow['follow_who'] = $aUid;
            $follow = $followModel->where($follow)->find();
            if (!$follow) {
                $this->error(L('_ERROR_REMARK_CANNOT_'));
            }
            $follow['alias'] = $aAlias;
            $result=$followModel->save($follow);
            if($result===false){
                $this->error(L('_ERROR_DB_WRITE_FAIL_').L('_PERIOD_'));
            }else{
                S('nickname_' . get_uid() . '_' . $aUid, null);
                $this->success(L('_SUCCESS_SETTINGS_').L('_PERIOD_'));
            }

        } else {
            $this->error(L('_FOLLOW_AFTER_LOGIN_').L('_PERIOD_'));
        }

    }

    /**检测消息
     * 返回系统的消息
     */
    public function getInformation()
    {

        $message = D('Common/Message');
        //取到所有没有提示过的信息
        $message_count = $message->getHaventReadMessageCount(is_login());
        $haventToastMessages = $message->getHaventToastMessage(is_login());

        $message->setAllToasted(is_login()); //消息中心推送
        //读取到推送之后，自动删除此推送来防止反复推送。

        exit(json_encode(array('message_count'=>$message_count,'messages' => $haventToastMessages)));
    }

    /**设置全部的系统消息为已读
     * @auth 陈一枭
     */
    public function setAllMessageReaded()
    {
        D('Message')->setAllReaded(is_login());
    }

    /**设置某条系统消息为已读
     * @param $message_id
     * @auth 陈一枭
     */
    public function readMessage($message_id)
    {
        exit(json_encode(array('status' => D('Common/Message')->readMessage($message_id))));

    }

    /**
     * 用户修改封面
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function changeCover()
    {
        if (!is_login()) {
            $this->error(L('_ERROR_NEED_LOGIN_').L('_EXCLAMATION_'));
        }
        if (IS_POST) {
            $aCoverId = I('post.cover_id', 0, 'intval');
            $result['status'] = 0;
            if ($aCoverId <= 0) {
                $result['info'] = L('_ERROR_ILLEGAL_OPERATE_').L('_EXCLAMATION_');
                $this->ajaxReturn($result);
            }

            $data = getUserConfigMap('user_cover');
            $data['role_id'] = 0;
            $model = D('Ucenter/UserConfig');
            $already_data = $model->findData($data);
            if (!$already_data) {
                $data['value'] = $aCoverId;
                $res = $model->addData($data);
            } else {
                if ($already_data['value'] == $aCoverId) {
                    $result['info'] = L('_ALTER_NOT_').L('_EXCLAMATION_');
                    $this->ajaxReturn($result);
                } else {
                    $res = $model->saveValue($data, $aCoverId);
                }
            }
            if ($res) {
                $result['status'] = 1;
                $result['path_1140'] = getThumbImageById($aCoverId, 1140, 230);
                $result['path_273'] = getThumbImageById($aCoverId, 273, 70);
            } else {
                $result['info'] = L('_FAIL_OPERATE_').L('_EXCLAMATION_');
            }
            $this->ajaxReturn($result);
        } else {
            //获取用户封面id
            $map = getUserConfigMap('user_cover');
            $map['role_id'] = 0;
            $model = D('Ucenter/UserConfig');
            $cover = $model->findData($map);
            $my_cover['cover_id'] = $cover['value'];
            $my_cover['cover_path'] = getThumbImageById($cover['value'], 348, 70);
            $this->assign('my_cover', $my_cover);
            $this->display('_change_cover');
        }
    }

    /**关注某人
     * @param int $uid
     * @auth 陈一枭
     */
    public function follow()
    {
        $aUid=I('post.uid',0,'intval');
        if (!is_login()) {
            $this->ajaxReturn(array('status' => 0, 'info' => L("_PLEASE_")." ".L("_LOG_IN_")));
        }

        if (D('Follow')->follow($aUid)) {
            $this->ajaxReturn(array('status' => 1, 'info' => L("_FOLLOWERS_")." ".L('_SUCCESS_')));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => L("_FOLLOWERS_")." ".L("_FAIL_")));
        }
    }

    /**取消对某人的关注
     * @param int $uid
     * @auth 陈一枭
     */
    public function unfollow()
    {
        $aUid=I('post.uid',0,'intval');
        if (!is_login()) {
            $this->ajaxReturn(array('status' => 0, 'info' => L("_PLEASE_")." ".L("_LOG_IN_")));
        }

        if (D('Follow')->unfollow($aUid)) {
            $this->ajaxReturn(array('status' => 1, 'info' =>  L("_CANCEL_")." ".L("_FOLLOWERS_")." ".L("_SUCCESS_")));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' =>  L("_CANCEL_")." ".L("_FOLLOWERS_")." ".L("_FAIL_")));
        }
    }


    /**
     * atWhoJson
     * @author:陈一枭
     */
    public function atWhoJson()
    {
        exit(json_encode($this->getAtWhoUsersCached()));
    }

    private function getAtWhoUsersCached()
    {
        $cacheKey = 'weibo_at_who_users';
        $atusers = S($cacheKey);
        if (empty($atusers[get_uid()])) {
            $atusers[get_uid()] = $this->getAtWhoUsers();
            S($cacheKey, $atusers, 600);
        }
        return $atusers[get_uid()];
    }

    /**
     * getAtWhoUsers  获取@列表
     * @return array
     * @author:陈一枭
     */
    private function getAtWhoUsers()
    {
        //获取能AT的人，UID列表
        $uid = get_uid();
        $follows = D('Follow')->where(array('who_follow' => $uid, 'follow_who' => $uid, '_logic' => 'or'))->select();
        $uids = array();
        foreach ($follows as &$e) {
            $uids[] = $e['who_follow'];
            $uids[] = $e['follow_who'];
        }
        unset($e);
        $uids = array_unique($uids);

        //加入拼音检索
        $users = array();
        foreach ($uids as $uid) {
            $user = query_user(array('nickname', 'id', 'avatar32'), $uid);
            $user['search_key'] = $user['nickname'] . D('PinYin')->Pinyin($user['nickname']);
            $users[] = $user;
        }

        //返回at用户列表
        return $users;
    }


    public function getVideo(){
        $aLink = I('post.link');
        $this->ajaxReturn(array('data'=>D('ContentHandler')->getVideoInfo($aLink)));
    }
}