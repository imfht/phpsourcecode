<?php
/**
 * 用户管理
 */
namespace Control\Controller;
use Core\Model\Account;
use Core\Model\Member;
use Think\Controller;

class MemberController extends Controller {

    public function _initialize() {
        C('FRAME_ACTIVE', 'member');
    }

    public function settingAction() {
        Member::loadSettings();
        $setting = C('MS');
        if(IS_POST) {
            if($setting[Member::OPT_POLICY] == Member::OPT_POLICY_UNION) {
                $setting[Member::OPT_POLICY] = Member::OPT_POLICY_CLASSICAL;
            } else {
                $setting[Member::OPT_POLICY] = Member::OPT_POLICY_UNION;
                //需要处理用户合并
            }
            if(Member::saveSettings($setting)) {
                $this->success('处理成功');
                exit;
            }
        }
        $this->assign('setting', $setting);
        $this->display();
    }

    public function groupsAction() {
        $m = new Member();
        $groups = $m->getGroups();
        $groups = coll_key($groups, 'id');

        if(IS_POST && I('post.batch')) {
            $def = I('post.default');
            if(!empty($groups[$def])) {
                $m->table('__MMB_GROUPS__')->data(array('isdefault' => '0'))->where("`id`!={$def}")->save();
                $m->table('__MMB_GROUPS__')->data(array('isdefault' => '1'))->where("`id`={$def}")->save();
            }

            $select = I('post.orderlist');
            if(!empty($select)) {
                foreach($select as $k => $v) {
                    if(!empty($groups[$k])) {
                        $v = util_limit($v, 0, 255);
                        $m->table('__MMB_GROUPS__')->data(array('orderlist' => $v))->where("`id`={$k}")->save();
                    }
                }
            }
            $this->success('操作成功');
            exit;
        }
        $id = I('get.id');
        if(!empty($id)) {
            $id = intval($id);
            if($id > 0) {
                $group = $groups[$id];
                $this->assign('entity', $group);
                if(!empty($group)) {
                    if(I('get.do') == 'delete') {
                        if($m->removeGroup($id)) {
                            $this->success('成功删除会员组', U('control/member/groups'));
                            exit;
                        } else {
                            $this->error('操作失败, 请稍后重试');
                        }
                    }
                }
            }
            if(IS_POST) {
                $input = coll_elements(array('title',  'remark'), I('post.'));
                $input['title'] = trim($input['title']);
                if(empty($input['title'])) {
                    $this->error('请输入会员组名称');
                }

                if(!empty($group)) {
                    //编辑组
                    $ret = $m->table('__MMB_GROUPS__')->data($input)->where("`id`={$id}")->save();
                    if(empty($ret)) {
                        $this->error('保存会员组失败, 请稍后重试');
                    } else {
                        $this->success('成功保存会员组', U('control/member/groups'));
                        exit;
                    }
                } else {
                    //新增组
                    $input['orderlist'] = '0';
                    $input['isdefault'] = '0';
                    $ret = $m->table('__MMB_GROUPS__')->data($input)->add();
                    if(empty($ret)) {
                        $this->error('保存新增会员组组失败, 请稍后重试');
                    } else {
                        $this->success('成功新增会员组', U('control/member/groups'));
                        exit;
                    }
                }
            }
        }

        $this->assign('groups', $groups);
        C('FRAME_CURRENT', U('control/member/groups'));
        $this->display();
    }

    public function creditAction() {
        $do = I('get.do') == 'policy' ? 'policy' : 'list';
        Member::loadSettings();
        $setting = C('MS');
        $credits = $setting[Member::OPT_CREDITS];
        $credits = coll_key($credits, 'name');
        if($do == 'list') {
            if(IS_POST) {
                $titles = I('post.title');
                $enableds = I('post.enabled');
                foreach($titles as $key => $value){
                    if($key == 'credit1' || $key == 'credit2') {
                        $credits[$key]['enabled'] = '1';
                    } else {
                        $credits[$key]['enabled'] = isset($enableds[$key]) ? '1' : '0';
                    }
                    $credits[$key]['title'] = trim($value);
                }
                $activity = $setting[Member::OPT_CREDITPOLICY][Member::OPT_CREDITPOLICY_ACTIVITY];
                $currency = $setting[Member::OPT_CREDITPOLICY][Member::OPT_CREDITPOLICY_CURRENCY];
                if(empty($credits[$activity]['enabled']) || empty($credits[$currency]['enabled']) ) {
                    $this->error('要禁用的积分被积分策略中使用, 请检查.', U('control/member/credit'));
                }

                $setting[Member::OPT_CREDITS] = $credits;
                if(Member::saveSettings($setting)) {
                    $this->success('积分信息更新成功！');
                    exit;
                } else {
                    $this->error('积分信息更新失败, 请稍后重试！');
                }
            }

            $this->assign('credits', $credits);
        }
        if($do == 'policy') {
            if(IS_POST) {
                $activity = I('post.activity');
                $currency = I('post.currency');
                if($activity == $currency) {
                    $this->error('营销积分和交易积分不能相同!');
                }
                if(empty($credits[$activity]['enabled']) || empty($credits[$currency]['enabled'])) {
                    $this->error('无效的积分选项');
                }
                $setting[Member::OPT_CREDITPOLICY][Member::OPT_CREDITPOLICY_ACTIVITY] = $activity;
                $setting[Member::OPT_CREDITPOLICY][Member::OPT_CREDITPOLICY_CURRENCY] = $currency;

                if(Member::saveSettings($setting)) {
                    $this->success('积分策略更新成功！');
                    exit;
                } else {
                    $this->error('积分策略更新失败, 请稍后重试！');
                }
            }

            $policy = $setting[Member::OPT_CREDITPOLICY];
            $this->assign('policy', $policy);
        }
        $this->assign('do', $do);
        C('FRAME_CURRENT', U('control/member/credit'));
        $this->display();
    }
    
    public function passportAction() {
        $a = new Account();
        $accounts = array();
        $weixins = $a->table('__PLATFORM_WEIXIN__')->field('id')->where("`level`=2")->select();
        if(!empty($weixins)) {
            $ids = coll_neaten($weixins, 'id');
            if(IS_POST) {
                $select = I('post.select');
                if($select == '0' || in_array($select, $ids)) {
                    Member::loadSettings();
                    $setting = C('MS');
                    $setting[Member::OPT_AUTH_WEIXIN] = $select;
                    Member::saveSettings($setting);
                    $this->success('处理成功');
                    exit;
                }
            }
            $accounts = $a->table('__PLATFORMS__')->where('`id` IN (' . implode(',', $ids) . ')')->select();
        }
        Member::loadSettings();
        $setting = C('MS');
        $auth = $setting[Member::OPT_AUTH_WEIXIN];
        
        $this->assign('auth', $auth);
        $this->assign('accounts', $accounts);
        C('FRAME_CURRENT', U('control/member/passport'));
        $this->display();
    }
}