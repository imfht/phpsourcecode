<?php
namespace app\ucenter\Controller;

use think\Controller;
use think\Db;
use app\ucenter\controller\Base;
use app\ucenter\model\UserConfig;

class Index extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $uid = isset($_GET['uid']) ? text($_GET['uid']) : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_fans_and_following($uid);
        $this->_tab_menu();
    }

    public function index($uid = null,$page=1)
    {
        $appArr = $this->_tab_menu();
        if (!$appArr) {
            $this->redirect('ucenter/Index/information', array('uid' => $uid));
        }
        $type=key($appArr);
        if (!isset ($appArr [$type])) {
            $this->error(lang('_ERROR_PARAM_').lang('_EXCLAMATION_').lang('_EXCLAMATION_'));
        }
        $this->assign('type', $type);
        $this->assign('module',$appArr[$type]['data-id']);
        $this->assign('page',$page);

        ////四处一词 seo
        //$str = '{$user_info.nickname|text}';
        //$str_app = '{$appArr.'.$type.'.title|text}';
        //$this->setTitle($str . lang('_INDEX_TITLE_'));
        //$this->setKeywords($str . lang('_PAGE_PERSON_') . $str_app);
        //$this->setDescription($str . lang('_DE_PERSON_') . $str_app . lang('_PAGE_'));
        //四处一词 seo end
        return $this->fetch();
    }



    private function userInfo($uid = null)
    {
        $user_info = query_user(array('avatar32','avatar64', 'nickname', 'uid', 'space_url', 'score', 'title', 'fans', 'following', 'rank_link', 'signature'), $uid);
        //dump($user_info);exit;
        //获取用户封面id
        $map=getUserConfigMap('user_cover','',$uid);
        $map['role_id']=0;
        $config_model = model('ucenter/UserConfig');
        $cover = $config_model->findData($map);

        $user_info['cover_id']=$cover['value'];
        $user_info['cover_path']=getThumbImageById($cover['value'],1140,230);

        $user_info['tags']=model('ucenter/UserTagLink')->getUserTag($uid);

        $this->assign('user_info', $user_info);
        
        //return $user_info;
    }

    public function information($uid = null)
    {
        //调用API获取基本信息
        
        $user = query_user(array('nickname', 'signature', 'email', 'mobile', 'rank_link', 'sex', 'pos_province', 'pos_city', 'pos_district', 'pos_community'), $uid);

        //TODO tox 获取省市区数据
        if ($user['pos_province'] != 0) {
            $user['pos_province'] = Db::name('district')->where(array('id' => $user['pos_province']))->value('name');
            $user['pos_city'] = Db::name('district')->where(array('id' => $user['pos_city']))->value('name');
            $user['pos_district'] = Db::name('district')->where(array('id' => $user['pos_district']))->value('name');
            $user['pos_community'] = Db::name('district')->where(array('id' => $user['pos_community']))->value('name');
        }
        //显示页面
        $this->assign('user', $user);
        $this->getExpandInfo($uid);
        
        return $this->fetch();
    }

    /**获取用户扩展信息
     * @param null $uid
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
     */
    public function _profile_group_list($uid = null)
    {
        $profile_group_list=array();
        $fields_list=$this->getRoleFieldIds($uid);
        if($fields_list){
            $fields_group_ids=Db::name('FieldSetting')->where(array('id'=>array('in',$fields_list),'status' => '1'))->field('profile_group_id')->select();
            if($fields_group_ids){
                $fields_group_ids=array_unique(array_column($fields_group_ids,'profile_group_id'));
                $map['id']=array('in',$fields_group_ids);

                if (isset($uid) && $uid != is_login()) {
                    $map['visiable'] = 1;
                }
                $map['status'] = 1;
                $profile_group_list = D('field_group')->where($map)->order('sort asc')->select();
            }
        }
        return $profile_group_list;
    }

    private function getRoleFieldIds($uid=null){
        $role_id=get_role_id($uid);
        $fields_list=cache('Role_Expend_Info_'.$role_id);
        if(!$fields_list){
            $map_role_config=getRoleConfigMap('expend_field',$role_id);
            $fields_list=Db::name('RoleConfig')->where($map_role_config)->value('value');
            if($fields_list){
                $fields_list=explode(',',$fields_list);
                cache('Role_Expend_Info_'.$role_id,$fields_list,600);
            }
        }
        return $fields_list;
    }

    /**分组下的字段信息及相应内容
     * @param null $id
     * @param null $uid
     * @return null
     */
    public function _info_list($id = null, $uid = null)
    {
        $fields_list=$this->getRoleFieldIds($uid);
        $info_list = null;

        if (isset($uid) && $uid != is_login()) {
            //查看别人的扩展信息
            $field_setting_list = Db::name('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'visiable' => '1','id'=>array('in',$fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = $uid;
        } else if (is_login()) {
            $field_setting_list = Db::name('field_setting')->where(array('profile_group_id' => $id, 'status' => '1','id'=>array('in',$fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = is_login();

        } else {
            $this->error(lang('_ERROR_PLEASE_LOGIN_').lang('_EXCLAMATION_'));
        }
        foreach ($field_setting_list as &$val) {
            $map['field_id'] = $val['id'];
            $field = Db::name('field')->where($map)->find();
            $val['field_content'] = $field;
            unset($map['field_id']);
            $info_list[$val['id']] = $this->_get_field_data($val);
            //当用户扩展资料为数组方式的处理@MingYangliu
            $vlaa = explode('|', $val['form_default_value']);
            $needle =':';//判断是否包含a这个字符
            $tmparray = explode($needle,$vlaa[0]);
            if(count($tmparray)>1){
                foreach ($vlaa as $kye=>$vlaas){
                    if(count($tmparray)>1){
                        $vlab[] = explode(':', $vlaas);
                        foreach ($vlab as $key=>$vlass){
                            $items[$vlass[0]] = $vlass[1];
                        }
                    }
                    continue;
                }
                $info_list[$val['id']]['field_data'] = $items[$info_list[$val['id']]['field_data']];
            }
            //当扩展资料为join时，读取数据并进行处理再显示到前端@MingYang
            if($val['child_form_type'] == "join"){
                $j = explode('|',$val['form_default_value']);
                $a = explode(' ',$info_list[$val['id']]['field_data']);
                $info_list[$val['id']]['field_data'] = get_userdata_join($a,$j[0],$j[1]);
            }
        }
        return $info_list;
    }

    public function _get_field_data($data = null)
    {
        $result = null;
        $result['field_name'] = $data['field_name'];
        $result['field_data'] = lang('');
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

    public function appList($uid = null, $page = 1, $tab = null)
    {

        $appArr = $this->_tab_menu();

        if (!$appArr) {
            $this->redirect('ucenter/Index/information', array('uid' => $uid));
        }

        $type = text($_GET['type']);
        if (!isset ($appArr [$type])) {
            $this->error(lang('_ERROR_PARAM_').lang('_EXCLAMATION_').lang('_EXCLAMATION_'));
        }
        $this->assign('type', $type);
        $this->assign('module',$appArr[$type]['data-id']);
        $this->assign('page',$page);
        $this->assign('tab',$tab);

        $this->fetch('index');
    }

    /**
     * 个人主页标签导航
     * @return void
     */
    public function _tab_menu()
    {
        $modules = model('common/Module')->getAll();
        $apps = [];
        
        foreach ($modules as $m) {
            if ($m['is_setup'] == 1 && $m['entry'] != '') {
                if (file_exists(APP_PATH . $m['name'] . '/widget/UcenterBlock.php')) {
                    $apps[] = array('data-id' => $m['name'], 'title' => $m['alias'],'sort'=>$m['sort'],'key'=>strtolower($m['name']));
                }
            }
        }
        if($apps){
            $apps = $this->sortApps($apps);
            $apps=array_combine(array_column($apps,'key'),$apps); 
        }
        $this->assign('appArr', $apps);  
        return $apps;
    }


    public function _fans_and_following($uid = null)
    {
        $uid = isset($uid) ? $uid : is_login();
        //我的粉丝展示
        $map['follow_who'] = $uid;
        $fans_default = Db::name('Follow')->where($map)->field('who_follow')->order('create_time desc')->limit(8)->select();
        $fans_totalCount = Db::name('Follow')->where($map)->count();
        foreach ($fans_default as &$user) {
            $user['user'] = query_user(array('avatar64', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['who_follow']);
        }
        unset($user);
        $this->assign('fans_totalCount', $fans_totalCount);
        $this->assign('fans_default', $fans_default);

        //我关注的展示
        $map_follow['who_follow'] = $uid;
        $follow_default = Db::name('Follow')->where($map_follow)->field('follow_who')->order('create_time desc')->limit(8)->select();
        $follow_totalCount = Db::name('Follow')->where($map_follow)->count();
        foreach ($follow_default as &$user) {
            $user['user'] = query_user(array('avatar64', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['follow_who']);
        }
        unset($user);
        $this->assign('follow_totalCount', $follow_totalCount);
        $this->assign('follow_default', $follow_default);
    }

    public function fans($uid = null)
    {
        $uid = isset($uid) ? $uid : is_login();

        $this->assign('tab', 'fans');
        $fans = model('Follow')->getFans($uid, ['avatar128', 'uid', 'nickname', 'fans', 'following', 'space_url', 'title']);
        $this->assign('fans', $fans);

        return $this->fetch();
    }

    public function following($uid = null, $page = 1)
    {
        $uid = isset($uid) ? $uid : is_login();

        $following = model('Follow')->getFollowing($uid,['avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title']);
        
        $this->assign('following', $following);

        $this->assign('tab', 'following');

        return $this->fetch();
    }

    public function rank($uid = null)
    {
        $uid = isset($uid) ? $uid : is_login();

        $rankList = Db::name('rank_user')->where(array('uid' => $uid, 'status' => 1))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = Db::name('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(Db::name('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rank');

        return $this->fetch('rank');
    }

    public function rankVerifyFailure()
    {
        $uid = isset($uid) ? $uid : is_login();

        $rankList = Db::name('rank_user')->where(array('uid' => $uid, 'status' => -1))->field('id,rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = Db::name('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(Db::name('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerifyFailure');
        return $this->fetch('rank');
    }

    public function rankVerifyWait()
    {
        $uid = isset($uid) ? $uid : is_login();

        $rankList = Db::name('rank_user')->where(array('uid' => $uid, 'status' => 0))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = Db::name('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(Db::name('picture')->where('id=' . $rank['logo'])->field('path')->value('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerifyWait');

        return $this->fetch('rank');
    }

    public function rankVerifyCancel($rank_id = null)
    {
        $rank_id = intval($rank_id);
        if (is_login() && $rank_id) {
            $map['rank_id'] = $rank_id;
            $map['uid'] = is_login();
            $map['status'] = 0;
            $result = Db::name('rank_user')->where($map)->delete();
            if ($result) {
                model('Message')->sendMessageWithoutCheckSelf(is_login(),lang('_MESSAGE_RANK_CANCEL_1_'),  lang('_MESSAGE_RANK_CANCEL_2_'), 'ucenter/Message/message', array('tab' => 'system'));
                $this->success(lang('_SUCCESS_CANCEL_'), Url('ucenter/Index/rankVerifyWait'));
            } else {
                $this->error(lang('_FAIL_CANCEL_'));
            }
        }
    }

    public function rankVerify($rank_user_id = null)
    {
        $uid = isset($uid) ? $uid : is_login();

        $rank_user_id = intval($rank_user_id);
        $map_already['uid'] = $uid;
        //重新申请头衔
        if ($rank_user_id) {
            $model = Db::name('rank_user')->where(array('id' => $rank_user_id));
            $old_rank_user = $model->field('id,rank_id,reason')->find();
            if (!$old_rank_user) {
                $this->error(lang('_ERROR_RANK_RE_SELECT_'));
            }
            $this->assign('old_rank_user', $old_rank_user);
            $map_already['id'] = array('neq', $rank_user_id);
            model('Message')->sendMessageWithoutCheckSelf(is_login(), lang(''),lang(''),  'ucenter/Message/message', array('tab' => 'system'));
        }
        $alreadyRank = Db::name('rank_user')->where($map_already)->field('rank_id')->select();
        $alreadyRank = array_column($alreadyRank, 'rank_id');
        if ($alreadyRank) {
            $map['id'] = array('not in', $alreadyRank);
        }
        $map['types'] = 1;
        $rankList = Db::name('rank')->where($map)->select();
        foreach($rankList as &$rank){
            $rank['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
        }
        unset($rank);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerify');
        return $this->fetch('rank_verify');
    }

    public function verify($rank_id = null, $reason = null, $rank_user_id = 0)
    {
        $rank_id = intval($rank_id);
        $reason = text($reason);
        $rank_user_id = intval($rank_user_id);
        if (!$rank_id) {
            $this->error(lang('_ERROR_RANK_SELECT_'));
        }
        if ($reason == null || $reason == '') {
            $this->error(lang('_ERROR_RANK_REASON_'));
        }
        $data['rank_id'] = $rank_id;
        $data['reason'] = $reason;
        $data['uid'] = is_login();
        $data['is_show'] = 1;
        $data['create_time'] = time();
        $data['status'] = 0;
        if ($rank_user_id) {
            $model = Db::name('rank_user')->where(array('id' => $rank_user_id));
            if (!$model->select()) {
                $this->error(lang('_ERROR_RANK_RE_SELECT_'));
            }
            $result = Db::name('rank_user')->where(array('id' => $rank_user_id))->save($data);
        } else {
            $result = Db::name('rank_user')->insert($data);
        }
        if ($result) {
            model('Message')->sendMessageWithoutCheckSelf(is_login(),lang('_MESSAGE_RANK_APPLY_1_'),lang('_MESSAGE_RANK_APPLY_2_'),  'ucenter/Message/message', array('tab' => 'system'));
            $this->success(lang('_SUCCESS_RANK_APPLY_'), Url('ucenter/Index/rankVerify'));
        } else {
            $this->error(lang('_FAIL_RANK_APPLY_'));
        }
    }

    /**
     * @param $apps
     * @param $vals
     * @return mixed
     */
    private function sortApps($apps)
    {
        if (is_array($apps)) {
            foreach ($apps as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        array_multisort($key_array, SORT_ASC, $apps);
        return $apps;

        //return $this->multi_array_sort($apps, 'sort', SORT_DESC);
    }

    public function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
    {
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
    }

}