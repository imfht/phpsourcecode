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

    /**
     * 用户个人主页
     * @param  [type]  $uid  [description]
     * @param  integer $page [description]
     * @return [type]        [description]
     */
    public function index($uid = null)
    {
        $appArr = $this->_tab_menu();
        if (!$appArr) {
            $this->redirect('ucenter/Index/information', array('uid' => $uid));
        }
        $type = key($appArr);
        if (!isset ($appArr [$type])) {
            $this->error(lang('_ERROR_PARAM_').lang('_EXCLAMATION_').lang('_EXCLAMATION_'));
        }
        
        $this->assign('uid',$uid);
        $this->assign('type', $type);
        $this->assign('module',$appArr[$type]['data-id']);
        $this->setTitle($this->userInfo($uid)['nickname'] . lang('_INDEX_TITLE_'));

        return $this->fetch();
    }

    private function userInfo($uid = null)
    {
        $user_info = query_user(array('avatar32','avatar64', 'nickname', 'uid', 'space_url', 'score', 'title', 'fans', 'following', 'signature'), $uid);
        //获取用户封面id
        $map = getUserConfigMap('user_cover','',$uid);
        $map['role_id']=0;
        $config_model = model('ucenter/UserConfig');
        $cover = $config_model->findData($map);

        $user_info['cover_id']=$cover['value'];
        $user_info['cover_path']=getThumbImageById($cover['value'],1140,230);

        $user_info['tags']=model('ucenter/UserTagLink')->getUserTag($uid);

        $this->assign('user_info', $user_info);
        return $user_info;
    }

    public function information($uid = null)
    {
        //调用API获取基本信息
        
        $user = query_user(array('nickname', 'signature', 'email', 'mobile', 'sex', 'pos_province', 'pos_city', 'pos_district', 'pos_community'), $uid);

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
        $result['field_data'] = text($result['field_data']);
        return $result;
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
        $fans = model('common/Follow')->getFans($uid, ['avatar128', 'uid', 'nickname', 'fans', 'following', 'space_url', 'title']);
        $this->assign('fans', $fans);

        return $this->fetch();
    }

    public function following($uid = null, $page = 1)
    {
        $uid = isset($uid) ? $uid : is_login();

        $following = model('common/Follow')->getFollowing($uid,['avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title']);
        
        $this->assign('following', $following);

        $this->assign('tab', 'following');

        return $this->fetch();
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
                    $key_array[] = $row_array['sort'];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        array_multisort($key_array, SORT_ASC, $apps);

        return $apps;
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