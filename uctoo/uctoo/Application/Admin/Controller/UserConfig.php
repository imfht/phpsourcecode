<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\builder\AdminConfigBuilder;
use app\ucenter\widget\RegStepWidget;
use think\Hook;

/**
 * Class UserConfigController  后台用户配置控制器
 * @package Admin\Controller
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
class UserConfig extends Admin
{


    public function index()
    {

        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

        $mStep = controller('ucenter/RegStep','widget')->mStep;
        $step = array();
        foreach ($mStep as $key => $v) {
            $step[] = array('data-id' => $key, 'title' => $v);
        }

        $default = array(array('data-id' => 'disable', 'title' => lang('_DISABLE_'), 'items' => $step), array('data-id' => 'enable', 'title' => lang('_ENABLE_'), 'items' => array()));
        if (array_key_exists('REG_STEP', $data)) {
            $data['REG_STEP'] = $admin_config->parseKanbanArray($data['REG_STEP'],$step,$default);
        }else{
            $data['REG_STEP'] = null;
        }

        $modules = model('Common/Module')->getAll();
        $menu = array();
        foreach ($modules as $m) {
            if ($m['is_setup'] == 1 && $m['entry'] != '') {
                if (file_exists(APP_PATH . $m['name'] . '/widget/UcenterBlock.php')) {
                    $menu[] = array('data-id' => $m['name'], 'title' => $m['alias'], 'sort' => $m['sort'], 'key' => strtolower($m['name']));
                }
            }
        }

        $apps[] = array('data-id' => 'info', 'sort' => '0', 'title' => '资料', 'key' => 'info');
        $apps[] = array('data-id' => 'rank_title', 'sort' => '0', 'title' => lang('_RANK_TITLE_'), 'key' => 'rank_title');
        $apps[] = array('data-id' => 'follow', 'sort' => '0', 'title' => lang('_FOLLOWERS_NO_SPACE_') . '/粉丝', 'key' => 'follow');
        $apps[] = array('data-id' => 'topic_list', 'sort' => '0', 'title' => '关注的话题', 'key' => 'topic_list');
        $apps = $this->sortApps($apps);

        $default1 = array(array('data-id' => 'disable', 'title' => lang('_DISABLE_'), 'items' => $menu), array('data-id' => 'enable', 'title' =>lang('_ENABLE_'), 'items' => $apps));
        $menu = array_merge($menu, $apps);
        if (array_key_exists('UCENTER_KANBAN', $data)) {
            $data['UCENTER_KANBAN'] = $admin_config->parseKanbanArray($data['UCENTER_KANBAN'],$menu,$default1);
        }else{
            $data['UCENTER_KANBAN'] = null;
        }

        empty($data['LEVEL']) && $data['LEVEL'] = <<<str
0:Lv1 实习
50:Lv2 试用
100:Lv3 转正
200:Lv4 助理
400:Lv5 经理
800:Lv6 董事
1600:Lv7 董事长
str;
        empty($data['OPEN_QUICK_LOGIN']) && $data['OPEN_QUICK_LOGIN'] = 0;

        empty($data['LOGIN_SWITCH']) && $data['LOGIN_SWITCH'] = 'username';

        $addons = Hook::get('sms');
        $opt = array('none' => lang('_NONE_'));
        foreach ($addons as $name) {
            if (class_exists($name)) {
                $class = new $name();
                $config = $class->getConfig();
                if ($config['switch']) {
                    $opt[$class->info['name']] = $class->info['title'];
                }
            }
        }

        $admin_config->title(lang('_USER_CONFIGURATION_'))->data($data)
            ->keyCheckBox('REG_SWITCH', lang('_REGISTRATION_SWITCH_'), lang('_THE_REGISTRATION_OPTION_THAT_ALLOWS_THE_USE_OF_THE_REGISTRATION_IS_CLOSED_'), array( 'wechat' => lang('_WECHAT_'), 'mobile' => lang('_MOBILE_PHONE_')))
            ->keyRadio('EMAIL_VERIFY_TYPE', lang('_MAILBOX_VERIFICATION_TYPE_'), lang('_TYPE_MAILBOX_VERIFICATION_'), array(0 => lang('_NOT_VERIFIED_'), 1 => lang('_POST_REGISTRATION_ACTIVATION_MAIL_'), 2 => lang('_EMAIL_VERIFY_SEND_BEFORE_REG_')))
            ->keyRadio('MOBILE_VERIFY_TYPE', lang('_MOBILE_VERIFICATION_TYPE_'), lang('_TYPE_OF_CELL_PHONE_VERIFICATION_'), array(0 => lang('_NOT_VERIFIED_'), 1 => lang('_REGISTER_BEFORE_SENDING_A_VALIDATION_MESSAGE_')))
            ->keyText('NEW_USER_FOLLOW', lang('_NEW_USER_ATTENTION_'), lang('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyText('NEW_USER_FANS', lang('_NEW_USER_FANS_'), lang('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyText('NEW_USER_FRIENDS', lang('_NEW_FRIENDS_'), lang('_ID_INPUT_SEPARATE_COMMA_'))

            ->keyKanban('REG_STEP', lang('_REGISTRATION_STEP_'), lang('_STEPS_TO_BE_MADE_AFTER_REGISTRATION_'))//看板

            ->keyCheckBox('REG_CAN_SKIP', lang('_WHETHER_THE_REGISTRATION_STEP_CAN_BE_SKIPPED_'), lang('_CHECK_TO_SKIP_AND_YOU_CANT_SKIP_THE_DEFAULT_'),$mStep)

            ->keyEditor('REG_EMAIL_VERIFY', lang('_MAILBOX_VERIFICATION_TEMPLATE_'), lang('_PLEASE_EMAIL_VERIFY_'),'all')
            ->keyEditor('REG_EMAIL_ACTIVATE', lang('_MAILBOX_ACTIVATION_TEMPLATE_'), lang('_PLEASE_USER_ACTIVE_'))

            ->keySelect('SMS_HOOK', lang('_SMS_SENDING_SERVICE_PROVIDER_'), lang('_SMS_SEND_SERVICE_PROVIDERS_NEED_TO_INSTALL_THE_PLUG-IN_'), $opt)
            ->keyText('SMS_RESEND', lang('_THE_MESSAGE_RETRANSMISSION_TIME_'), lang('_THE_MESSAGE_RETRANSMISSION_TIME_'))

            ->keyText('SMS_UID', lang('_SMS_PLATFORM_ACCOUNT_NUMBER_'), lang('_SMS_PLATFORM_ACCOUNT_NUMBER_'))
            ->keyText('SMS_PWD', lang('_SMS_PLATFORM_PASSWORD_'), lang('_SMS_PLATFORM_PASSWORD_'))
            ->keyTextArea('SMS_CONTENT', lang('_MESSAGE_CONTENT_'), lang('_MSG_VERICODE_ACCOUNT_'))
            ->keyCheckBox('RANK_LIST', '排行榜', '排行榜显示', array('fans' =>'粉丝', 'con_check' =>'连签', 'total_check' =>'累签', 'score' =>'积分'))->keyDefault('RANK_LIST', 'fans,con_check,total_check,score')
            ->keyTextArea('LEVEL', lang('_HIERARCHY_'), lang('_ONE_PER_LINE_BETWEEN_THE_NAME_AND_THE_INTEGRAL_BY_A_COLON_'))
            ->keyInteger('NICKNAME_MIN_LENGTH', lang('_NICKNAME_LENGTH_MINIMUM_'))->keyDefault('NICKNAME_MIN_LENGTH',2)
            ->keyInteger('NICKNAME_MAX_LENGTH', lang('_NICKNAME_LENGTH_MAXIMUM_'))->keyDefault('NICKNAME_MAX_LENGTH',32)
            ->keyInteger('USERNAME_MIN_LENGTH', lang('_USERNAME_LENGTH_MINIMUM_'))->keyDefault('USERNAME_MIN_LENGTH',2)
            ->keyInteger('USERNAME_MAX_LENGTH', lang('_USERNAME_LENGTH_MAXIMUM_'))->keyDefault('USERNAME_MAX_LENGTH',32)
            ->keyKanban('UCENTER_KANBAN', lang('_UCENTER_KANBAN_'), lang('_SET_TO_SHOW_UCENTER_'))//基础配置 个人主页看板

            ->keyRadio('OPEN_QUICK_LOGIN',lang('_QUICK_LOGIN_'),lang('_BY_DEFAULT_AFTER_THE_USER_IS_LOGGED_IN_THE_USER_IS_LOGGED_IN_'), array(0 => lang('_OFF_'), 1 => lang('_OPEN_')))


            ->keyCheckBox('LOGIN_SWITCH', lang('_LOGIN_PROMPT_SWITCH_'), lang('_JUST_THE_TIP_OF_THE_LOGIN_BOX_'), array('email' => lang('_MAILBOX_'), 'mobile' => lang('_MOBILE_PHONE_')))
            ->keyText('SYNC_LOGIN_EMAIL_SUFFIX','第三方登录邮箱后缀','格式:@xx.xxx')
            ->group(lang('_REGISTER_CONFIGURATION_'), 'REG_SWITCH,EMAIL_VERIFY_TYPE,MOBILE_VERIFY_TYPE,REG_STEP,REG_CAN_SKIP,NEW_USER_FOLLOW,NEW_USER_FANS,NEW_USER_FRIENDS')
            ->group(lang('_LOGIN_CONFIGURATION_'), 'OPEN_QUICK_LOGIN,LOGIN_SWITCH,SYNC_LOGIN_EMAIL_SUFFIX')
            ->group(lang('_MAILBOX_VERIFICATION_TEMPLATE_'), 'REG_EMAIL_VERIFY')
            ->group(lang('_MAILBOX_ACTIVATION_TEMPLATE_'), 'REG_EMAIL_ACTIVATE')
            ->group(lang('_SMS_CONFIGURATION_'), 'SMS_HTTP,SMS_UID,SMS_PWD,SMS_CONTENT,SMS_HOOK,SMS_RESEND')
            ->group(lang('_BASIC_SETTINGS_'), 'RANK_LIST,LEVEL,NICKNAME_MIN_LENGTH,NICKNAME_MAX_LENGTH,USERNAME_MIN_LENGTH,USERNAME_MAX_LENGTH,UCENTER_KANBAN')
            ->buttonSubmit('', lang('_SAVE_'))
            ->keyDefault('REG_EMAIL_VERIFY',lang('_VERICODE_ACCOUNT_').lang('_PERIOD_'))
            ->keyDefault('REG_EMAIL_ACTIVATE',lang('_LINK_ACTIVE_IS_'))
            ->keyDefault('SYNC_LOGIN_EMAIL_SUFFIX','@uctoo.com')
            ->keyDefault('SMS_CONTENT',lang('_VERICODE_ACCOUNT_'))
            ->keyDefault('SMS_RESEND','60');
        return $admin_config->fetch();
    }

    private function sortApps($apps)
    {
        return $this->multi_array_sort($apps, 'sort', SORT_DESC);
    }

    function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
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
