<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-6-27
 * Time: 下午1:54
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Usercenter\Controller;


use Think\Controller;

class IndexController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $uid = isset($_GET['uid']) ? op_t($_GET['uid']) : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_fans_and_following($uid);

        $this->_tab_menu();

    }

    public function index($uid = null, $page = 1, $count = 10)
    {
        $appArr = $this->_tab_menu();
        if (!$appArr) {
            $this->redirect('Usercenter/Index/information', array('uid' => $uid));
        }
        foreach ($appArr as $key => $val) {
            $type = $key;
            break;
        }
        if (!isset ($appArr [$type])) {
            $this->error('参数出错！！');
        }
        $this->assign('type', $type);
        $className = ucfirst($type) . 'Protocol';
        $content = D(ucfirst($type) . '/' . $className)->profileContent($uid, $page, $count);
        if (empty($content)) {
            $content = '暂无内容';
        } else {
            $totalCount = D(ucfirst($type) . '/' . $className)->getTotalCount($uid);
            $this->assign('totalCount', $totalCount);
        }
        $this->assign('content', $content);
        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $str_app = '{$appArr[$type]|op_t}';
        $this->setTitle($str . "的个人主页");
        $this->setKeywords($str . "，个人主页，Think OX，个人" . $str_app);
        $this->setDescription($str . "的个人" . $str_app . "页");
        //四处一词 seo end
        $this->display();
    }

    private function userInfo($uid = null)
    {
        $user_info = query_user(array('avatar128', 'nickname', 'uid', 'space_url', 'icons_html', 'score', 'title', 'fans', 'following', 'weibocount', 'rank_link', 'signature'), $uid);
        $this->assign('user_info', $user_info);
        return $user_info;
    }

    public function information($uid = null, $info_type = "base_info")
    {
        $info_type = op_t($info_type);
        if ($info_type == "base_info") {
            //调用API获取基本信息
            //TODO tox 获取省市区数据
            $user = query_user(array('nickname', 'signature', 'email', 'mobile', 'rank_link', 'sex', 'pos_province', 'pos_city', 'pos_district', 'pos_community'), $uid);
            if ($user['pos_province'] != 0) {
                $user['pos_province'] = D('district')->where(array('id' => $user['pos_province']))->getField('name');
                $user['pos_city'] = D('district')->where(array('id' => $user['pos_city']))->getField('name');
                $user['pos_district'] = D('district')->where(array('id' => $user['pos_district']))->getField('name');
                $user['pos_community'] = D('district')->where(array('id' => $user['pos_community']))->getField('name');
            }
            //显示页面
            $this->assign('user', $user);
        } else {
            $info_type = "expand_info";

            $this->getExpandInfo($uid);
        }
        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $this->setTitle($str . "的个人资料页");
        $this->setKeywords($str . "，个人资料，Think OX");
        $this->setDescription($str . "的个人资料页");
        //四处一词 seo end

        $this->assign('info_type', $info_type);
        $this->display();
    }

    /**获取用户扩展信息
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getExpandInfo($uid = null, $profile_group_id = null)
    {
        $profile_group_list = $this->_profile_group_list($uid);
        foreach ($profile_group_list as &$val) {
            $val['info_list'] = $this->_info_list($val['id'], $uid);
        }
        $this->assign('profile_group_list', $profile_group_list);
    }

    /**扩展信息分组列表获取
     * @param null $uid
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _profile_group_list($uid = null)
    {
        if (isset($uid) && $uid != is_login()) {
            $map['visiable'] = 1;
        }
        $map['status'] = 1;
        $profile_group_list = D('field_group')->where($map)->order('sort asc')->select();

        return $profile_group_list;
    }

    /**分组下的字段信息及相应内容
     * @param null $id
     * @param null $uid
     * @return null
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _info_list($id = null, $uid = null)
    {
        $info_list = null;

        if (isset($uid) && $uid != is_login()) {
            //查看别人的扩展信息
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'visiable' => '1'))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = $uid;
        } else if (is_login()) {
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1'))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = is_login();

        } else {
            $this->error('请先登录！');
        }
        foreach ($field_setting_list as &$val) {
            $map['field_id'] = $val['id'];
            $field = D('field')->where($map)->find();
            $val['field_content'] = $field;
            unset($map['field_id']);
            $info_list[$val['id']] = $this->_get_field_data($val);
        }
        return $info_list;
    }

    public function _get_field_data($data = null)
    {
        $result = null;
        $result['field_name'] = $data['field_name'];
        $result['field_data'] = "还未设置";
        switch ($data['form_type']) {
            case 'input':
            case 'radio':
            case 'textarea':
            case 'select':
                $result['field_data'] = isset($data['field_content']['field_data']) ? $data['field_content']['field_data'] : "还未设置";
                break;
            case 'checkbox':
                $result['field_data'] = isset($data['field_content']['field_data']) ? implode(' ', explode('|', $data['field_content']['field_data'])) : "还未设置";
                break;
            case 'time':
                $result['field_data'] = isset($data['field_content']['field_data']) ? date("Y-m-d", $data['field_content']['field_data']) : "还未设置";
                break;
        }
        $result['field_data'] = op_t($result['field_data']);
        return $result;
    }

    public function appList($uid = null, $page = 1, $count = 10, $tab = null)
    {

        $appArr = $this->_tab_menu();

        $type = op_t($_GET['type']);
        if (!isset ($appArr [$type])) {
            $this->error('参数出错！！');
        }
        $this->assign('type', $type);
        $className = ucfirst($type) . 'Protocol';
        $content = D(ucfirst($type) . '/' . $className)->profileContent($uid, $page, $count, $tab);
        if (empty($content)) {
            $content = '暂无内容';
        } else {
            $totalCount = D(ucfirst($type) . '/' . $className)->getTotalCount($uid, $tab);
            $this->assign('totalCount', $totalCount);
        }
        $this->assign('content', $content);

        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $str_app = '{$appArr[$type]|op_t}';
        $this->setTitle($str . "的个人" . $str_app . "页");
        $this->setKeywords($str . "，个人主页，Think OX，个人" . $str_app);
        $this->setDescription($str . "的个人" . $str_app . "页");
        //四处一词 seo end

        $this->display('index');
    }

    /**
     * 个人主页标签导航
     * @return void
     */
    public function _tab_menu()
    {
        // 根据应用目录取全部APP信息
        $map['status'] = 1;
        $dir = APP_PATH;
        $appList = null;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $appList[]['app_name'] = $file;
                }
                closedir($dh);
            }
        }
        $apps=array();
        // 获取APP的HASH数组
        foreach ($appList as $app) {
            $appName = strtolower($app['app_name']);
            $className = ucfirst($appName);
            $dao = D($className . '/' . $className . 'Protocol');
            if (method_exists($dao, 'profileContent')) {
                $apps [$appName] = D($className . '/' . $className . 'Protocol')->getModelInfo();
            }
            unset ($dao);
        }
        $apps = $this->sortApps($apps);
        $this->assign('appArr', $apps);

        return $apps;
    }


    public function _fans_and_following($uid = null)
    {
        $uid = isset($uid) ? $uid : is_login();
        //我的粉丝展示
        $map['follow_who'] = $uid;
        $fans_default = D('Follow')->where($map)->field('who_follow')->order('create_time desc')->limit(8)->select();
        $fans_totalCount = D('Follow')->where($map)->count();
        foreach ($fans_default as &$user) {
            $user['user'] = query_user(array('avatar64', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['who_follow']);
        }
        unset($user);
        $this->assign('fans_totalCount', $fans_totalCount);
        $this->assign('fans_default', $fans_default);

        //我关注的展示
        $map_follow['who_follow'] = $uid;
        $follow_default = D('Follow')->where($map_follow)->field('follow_who')->order('create_time desc')->limit(8)->select();
        $follow_totalCount = D('Follow')->where($map_follow)->count();
        foreach ($follow_default as &$user) {
            $user['user'] = query_user(array('avatar64', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['follow_who']);
        }
        unset($user);
        $this->assign('follow_totalCount', $follow_totalCount);
        $this->assign('follow_default', $follow_default);
    }

    public function fans($uid = null, $page = 1)
    {
        $uid = isset($uid) ? $uid : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_tab_menu();


        $this->assign('tab', 'fans');
        $fans = D('Follow')->getFans($uid, $page, array('avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $totalCount);
        $this->assign('fans', $fans);
        $this->assign('totalCount', $totalCount);

        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $this->setTitle($str . "的个人粉丝页");
        $this->setKeywords($str . "，个人粉丝，Think OX");
        $this->setDescription($str . "的个人粉丝页");
        //四处一词 seo end

        $this->display();
    }

    public function following($uid = null, $page = 1)
    {
        $uid = isset($uid) ? $uid : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_tab_menu();

        $following = D('Follow')->getFollowing($uid, $page, array('avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $totalCount);
        $this->assign('following', $following);
        $this->assign('totalCount', $totalCount);
        $this->assign('tab', 'following');

        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $this->setTitle($str . "的个人关注页");
        $this->setKeywords($str . "，个人关注，Think OX");
        $this->setDescription($str . "的个人关注页");
        //四处一词 seo end

        $this->display();
    }

    public function rank($uid = null)
    {
        $uid = isset($uid) ? $uid : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_tab_menu();

        $rankList = D('rank_user')->where(array('uid' => $uid, 'status' => 1))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo'] = $rank['logo'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rank');

        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $this->setTitle($str . "的头衔列表页");
        $this->setKeywords($str . "，个人头衔，Think OX");
        $this->setDescription($str . "的头衔列表页");
        //四处一词 seo end

        $this->display('rank');
    }

    public function rankVerifyFailure()
    {
        $uid = isset($uid) ? $uid : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_tab_menu();

        $rankList = D('rank_user')->where(array('uid' => $uid, 'status' => -1))->field('id,rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo'] = $rank['logo'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerifyFailure');

        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $this->setTitle($str . "的被驳回头衔申请列表页");
        $this->setKeywords($str . "，个人头衔，Think OX");
        $this->setDescription($str . "的被驳回头衔申请列表页");
        //四处一词 seo end

        $this->display('rank');
    }

    public function rankVerifyWait()
    {
        $uid = isset($uid) ? $uid : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_tab_menu();

        $rankList = D('rank_user')->where(array('uid' => $uid, 'status' => 0))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo'] = $rank['logo'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerifyWait');

        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $this->setTitle($str . "的待审核头衔申请列表页");
        $this->setKeywords($str . "，个人头衔，Think OX");
        $this->setDescription($str . "的待审核头衔申请列表页");
        //四处一词 seo end

        $this->display('rank');
    }

    public function rankVerifyCancel($rank_id = null)
    {
        $rank_id = intval($rank_id);
        if (is_login() && $rank_id) {
            $map['rank_id'] = $rank_id;
            $map['uid'] = is_login();
            $map['status'] = 0;
            $result = D('rank_user')->where($map)->delete();
            if ($result) {
                D('Message')->sendMessageWithoutCheckSelf(is_login(), '头衔申请取消成功', '取消头衔申请', U('Usercenter/Message/message', array('tab' => 'system')));
                $this->success('取消成功', U('Usercenter/Index/rankVerifyWait'));
            } else {
                $this->error('取消失败');
            }
        }
    }

    public function rankVerify($rank_user_id = null)
    {
        $uid = isset($uid) ? $uid : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_tab_menu();

        $rank_user_id = intval($rank_user_id);
        $map_already['uid'] = $uid;
        //重新申请头衔
        if ($rank_user_id) {
            $model = D('rank_user')->where(array('id' => $rank_user_id));
            $old_rank_user = $model->field('id,rank_id,reason')->find();
            if (!$old_rank_user) {
                $this->error('请正确选择要重新申请的头衔');
            }
            $this->assign('old_rank_user', $old_rank_user);
            $map_already['id'] = array('neq', $rank_user_id);
            D('Message')->sendMessageWithoutCheckSelf(is_login(), '你将进行头衔的重新申请', '头衔重新申请', U('Usercenter/Message/message', array('tab' => 'system')));
        }
        $alreadyRank = D('rank_user')->where($map_already)->field('rank_id')->select();
        $alreadyRank = array_column($alreadyRank, 'rank_id');
        if ($alreadyRank) {
            $map['id'] = array('not in', $alreadyRank);
        }
        $map['types'] = 1;
        $rankList = D('rank')->where($map)->select();
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerify');

        //四处一词 seo
        $str = '{$user_info.nickname|op_t}';
        $this->setTitle($str . "的头衔申请页");
        $this->setKeywords($str . "，个人头衔，头衔申请，Think OX");
        $this->setDescription($str . "的头衔申请页");
        //四处一词 seo end

        $this->display('rank_verify');
    }

    public function verify($rank_id = null, $reason = null, $rank_user_id = 0)
    {
        $rank_id = intval($rank_id);
        $reason = op_t($reason);
        $rank_user_id = intval($rank_user_id);
        if (!$rank_id) {
            $this->error('请选择要申请的头衔');
        }
        if ($reason == null || $reason == '') {
            $this->error('请填写申请理由');
        }
        $data['rank_id'] = $rank_id;
        $data['reason'] = $reason;
        $data['uid'] = is_login();
        $data['is_show'] = 1;
        $data['create_time'] = time();
        $data['status'] = 0;
        if ($rank_user_id) {
            $model = D('rank_user')->where(array('id' => $rank_user_id));
            if (!$model->select()) {
                $this->error('请正确选择要重新申请的头衔');
            }
            $result = D('rank_user')->where(array('id' => $rank_user_id))->save($data);
        } else {
            $result = D('rank_user')->add($data);
        }
        if ($result) {
            D('Message')->sendMessageWithoutCheckSelf(is_login(), '头衔申请成功,等待管理员审核', '头衔申请', U('Usercenter/Message/message', array('tab' => 'system')));
            $this->success('申请成功,等待管理员审核', U('Usercenter/Index/rankVerify'));
        } else {
            $this->error('申请失败');
        }
    }

    /**
     * @param $apps
     * @param $vals
     * @return mixed
     * @auth 陈一枭
     */
    private function sortApps($apps)
    {
        return $this->multi_array_sort($apps,'sort',SORT_DESC);
    }
    function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
        if(is_array($multi_array)){
            foreach ($multi_array as $row_array){
                if(is_array($row_array)){
                    $key_array[] = $row_array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_array,$sort,$multi_array);
        return $multi_array;
    }
} 