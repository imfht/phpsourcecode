<?php
namespace app\ucenter\Controller;

use think\Controller;
use think\Db;

class Common extends Controller
{
    /**
     * 获取个人资料，用以支持小名片
     */
    private function getProfile($uid)
    {
        $uid = intval($_REQUEST['uid']);
        $userProfile = query_user(array('uid', 'nickname', 'avatar64', 'space_url', 'following', 'fans', 'signature'), $uid);
        $follow['follow_who'] = $userProfile['uid'];
        $follow['who_follow'] = is_login();
        $userProfile['followed'] = Db::name('Follow')->where($follow)->count();
        $userProfile['following_url'] = url('ucenter/Index/following', array('uid' => $uid));
        $userProfile['fans_url'] = url('ucenter/Index/fans', array('uid' => $uid));

        //获取用户封面path
        $map = getUserConfigMap('user_cover', '', $uid);
        $map['role_id'] = 0;
        $model = model('ucenter/UserConfig');
        $cover = $model->findData($map);
        if ($cover) {
            $userProfile['cover_path'] = getThumbImageById($cover['value'], 344, 100);
        } else {
            $userProfile['cover_path'] = STATIC_URL . '/common/images/qtip_bg.png';
        }
        //个人标签
        $userProfile['tags'] = '';
        $userTagLinkModel = model('ucenter/UserTagLink');
        $myTags = $userTagLinkModel->getUserTag($uid);
        if (count($myTags)) {
            $userProfile['tags'] = lang('_PERSONAL_TAB_').L('_COLON_').'<span>';
            $first = 1;
            
            $userProfile['tags'] .= '</span>';
        }
        return $userProfile;
    }


    public function card()
    {
        $aUID = input('get.uid', 0, 'intval');
        $user = $this->getProfile($aUID);
        $follow=model('common/Follow')->isFollow(is_login(),$aUID);
        $this->assign('follow',$follow);

        $this->assign('uid', $aUID);
        $this->assign('user', $user);
        $not_self = get_uid() != $aUID;
        $this->assign('not_self',$not_self);
        return $this->fetch();
    }

    public function setAlias()
    {
        $aUid = input('post.uid', 0, 'intval');
        $aAlias =trim(input('post.alias', '', 'text'));
        if($aAlias==''){
            $this->error(lang('_ERROR_REMARK_CANNOT_EMPTY_').lang('_PERIOD_'));
        }
        if (is_login()) {
            $follow['who_follow'] = get_uid();
            $follow['follow_who'] = $aUid;
            $follow = Db::name('follow')->where($follow)->find();
            if (!$follow) {
                $this->error(lang('_ERROR_REMARK_CANNOT_'));
            }
            $follow['alias'] = $aAlias;
            $result=Db::name('follow')->save($follow);
            if($result===false){
                $this->error(lang('_ERROR_DB_WRITE_FAIL_').L('_PERIOD_'));
            }else{
                cache('nickname_' . get_uid() . '_' . $aUid, null);
                $this->success(lang('_SUCCESS_SETTINGS_').L('_PERIOD_'));
            }

        } else {
            $this->error(lang('_FOLLOW_AFTER_LOGIN_').L('_PERIOD_'));
        }

    }



    /**
     * 用户修改封面
     */
    public function changeCover()
    {
        if (!is_login()) {
            $this->error(L('_ERROR_NEED_LOGIN_').L('_EXCLAMATION_'));
        }
        if (Request()->isPost()) {
            $aCoverId = input('post.cover_id', 0, 'intval');
            $result['status'] = 0;
            if ($aCoverId <= 0) {
                $result['info'] = lang('_ERROR_ILLEGAL_OPERATE_').L('_EXCLAMATION_');
                $this->ajaxReturn($result);
            }

            $data = getUserConfigMap('user_cover');
            $data['role_id'] = 0;
            $model = model('ucenter/UserConfig');
            $already_data = $model->findData($data);
            if (!$already_data) {
                $data['value'] = $aCoverId;
                $res = $model->addData($data);
            } else {
                if ($already_data['value'] == $aCoverId) {
                    $result['info'] = lang('_ALTER_NOT_').lang('_EXCLAMATION_');
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
                $result['info'] = lang('_FAIL_OPERATE_').lang('_EXCLAMATION_');
            }
            $this->ajaxReturn($result);
        } else {
            //获取用户封面id
            $map = getUserConfigMap('user_cover');
            $map['role_id'] = 0;
            $model = model('ucenter/UserConfig');
            $cover = $model->findData($map);
            
            $my_cover['cover_id'] = $cover['value'];
            if($cover['value']){
                $my_cover['cover_path'] = getThumbImageById($cover['value'], 348, 70);
            }else{
                $my_cover['cover_path'] = STATIC_URL . '/ucenter/images/user_top_default_bg.jpg';
            }
            
            $this->assign('my_cover', $my_cover);
            return $this->fetch('_change_cover');
        }
    }

    /**关注某人
     * @param int $uid
     * @auth 陈一枭
     */
    public function follow()
    {
        $aUid=input('post.uid',0,'intval');
        if (!is_login()) {
            $this->ajaxReturn(array('status' => 0, 'info' => lang("_PLEASE_")." ".lang("_LOG_IN_")));
        }

        if (model('Follow')->follow($aUid)) {
            $this->ajaxReturn(array('status' => 1, 'info' => lang("_FOLLOWERS_")." ".L('_SUCCESS_')));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => lang("_FOLLOWERS_")." ".lang("_FAIL_")));
        }
    }

    /**取消对某人的关注
     * @param int $uid
     * @auth 陈一枭
     */
    public function unfollow()
    {
        $aUid=input('post.uid',0,'intval');
        if (!is_login()) {
            $this->ajaxReturn(array('status' => 0, 'info' => lang("_PLEASE_")." ".lang("_LOG_IN_")));
        }

        if (model('Follow')->unfollow($aUid)) {
            $this->ajaxReturn(array('status' => 1, 'info' =>  lang("_CANCEL_")." ".lang("_FOLLOWERS_")." ".lang("_SUCCESS_")));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' =>  lang("_CANCEL_")." ".lang("_FOLLOWERS_")." ".lang("_FAIL_")));
        }
    }

    public function getVideo(){
        $aLink = input('post.link');
        $this->ajaxReturn(array('data'=>model('ContentHandler')->getVideoInfo($aLink)));
    }
}