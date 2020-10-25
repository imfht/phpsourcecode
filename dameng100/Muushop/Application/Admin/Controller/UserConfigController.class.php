<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;


use Admin\Builder\AdminConfigBuilder;

use Ucenter\Widget\RegStepWidget;

/**
 * Class UserConfigController  后台用户配置控制器
 * @package Admin\Controller
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
class UserConfigController extends AdminController
{

    public function index()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

        $mStep = A('Ucenter/RegStep', 'Widget')->mStep;
        $step = array();
        foreach ($mStep as $key => $v) {
            $step[] = array('data-id' => $key, 'title' => $v);
        }

        $default = array(array('data-id' => 'disable', 'title' => L('_DISABLE_'), 'items' => $step), array('data-id' => 'enable', 'title' => L('_ENABLE_'), 'items' => array()));
        //$default=array(L('_DISABLE_')=>$step,L('_ENABLE_AND_SKIP_')=>array(),L('_ENABLE_BUT_NOT_SKIP_')=>array());
        $data['REG_STEP'] = $admin_config->parseKanbanArray($data['REG_STEP'],$step,$default);

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

        $admin_config->title(L('_USER_CONFIGURATION_'))->data($data)
            //注册配置
            ->keyCheckBox('REG_SWITCH', L('_REGISTRATION_SWITCH_'), L('_THE_REGISTRATION_OPTION_THAT_ALLOWS_THE_USE_OF_THE_REGISTRATION_IS_CLOSED_'), array('username' => L('_USER_NAME_'),'email' => L('_MAILBOX_'), 'mobile' => L('_MOBILE_PHONE_')))
            ->keyRadio('EMAIL_VERIFY_TYPE', L('_MAILBOX_VERIFICATION_TYPE_'), L('_TYPE_MAILBOX_VERIFICATION_'), array(0 => L('_NOT_VERIFIED_'), 1 => L('_POST_REGISTRATION_ACTIVATION_MAIL_'), 2 => L('_EMAIL_VERIFY_SEND_BEFORE_REG_')))
            ->keyRadio('MOBILE_VERIFY_TYPE', L('_MOBILE_VERIFICATION_TYPE_'), L('_TYPE_OF_CELL_PHONE_VERIFICATION_'), array(0 => L('_NOT_VERIFIED_'), 1 => L('_REGISTER_BEFORE_SENDING_A_VALIDATION_MESSAGE_')))
            ->keyText('NEW_USER_FOLLOW', L('_NEW_USER_ATTENTION_'), L('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyText('NEW_USER_FANS', L('_NEW_USER_FANS_'), L('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyText('NEW_USER_FRIENDS', L('_NEW_FRIENDS_'), L('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyKanban('REG_STEP', L('_REGISTRATION_STEP_'), L('_STEPS_TO_BE_MADE_AFTER_REGISTRATION_'))
            ->keyCheckBox('REG_CAN_SKIP', L('_WHETHER_THE_REGISTRATION_STEP_CAN_BE_SKIPPED_'), L('_CHECK_TO_SKIP_AND_YOU_CANT_SKIP_THE_DEFAULT_'),$mStep)

            //登陆配置
            ->keyRadio('OPEN_QUICK_LOGIN',L('_QUICK_LOGIN_'),L('_BY_DEFAULT_AFTER_THE_USER_IS_LOGGED_IN_THE_USER_IS_LOGGED_IN_'), array(0 => L('_OFF_'), 1 => L('_OPEN_')))
            ->keyCheckBox('LOGIN_SWITCH', L('_LOGIN_PROMPT_SWITCH_'), L('_JUST_THE_TIP_OF_THE_LOGIN_BOX_'), array('username' => L('_USER_NAME_'), 'email' => L('_MAILBOX_'), 'mobile' => L('_MOBILE_PHONE_')))
            ->keyRadio('OPEN_WECHAT_AUTH',L('_OPEN_WECHAT_AUTH_SWITCH_'),L('_OPEN_WECHAT_AUTH_SWITCH_INFO_'),array(0 => L('_OFF_'), 1 => L('_OPEN_')))

            //邮件验证配置
            ->keyEditor('REG_EMAIL_VERIFY', L('_MAILBOX_VERIFICATION_TEMPLATE_'), L('_PLEASE_EMAIL_VERIFY_'))
            //邮箱验证
            ->keyEditor('REG_EMAIL_ACTIVATE', L('_MAILBOX_ACTIVATION_TEMPLATE_'), L('_PLEASE_USER_ACTIVE_'))

            //短信验证内容
            ->keyTextArea('SMS_CONTENT', L('_MESSAGE_CONTENT_'), L('_MSG_VERICODE_ACCOUNT_'))
            ->keyDefault('SMS_CONTENT',L('_VERICODE_ACCOUNT_'))
            ->keyDefault('SMS_RESEND','60')
            ->keyText('SMS_RESEND', L('_THE_MESSAGE_RETRANSMISSION_TIME_'), L('_THE_MESSAGE_RETRANSMISSION_TIME_'))
            
            //基础配置
            ->keyTextArea('LEVEL', L('_HIERARCHY_'), L('_ONE_PER_LINE_BETWEEN_THE_NAME_AND_THE_INTEGRAL_BY_A_COLON_'))
            ->keyInteger('NICKNAME_MIN_LENGTH', L('_NICKNAME_LENGTH_MINIMUM_'))->keyDefault('NICKNAME_MIN_LENGTH',2)
            ->keyInteger('NICKNAME_MAX_LENGTH', L('_NICKNAME_LENGTH_MAXIMUM_'))->keyDefault('NICKNAME_MAX_LENGTH',32)
            ->keyInteger('USERNAME_MIN_LENGTH', L('_USERNAME_LENGTH_MINIMUM_'))->keyDefault('USERNAME_MIN_LENGTH',2)
            ->keyInteger('USERNAME_MAX_LENGTH', L('_USERNAME_LENGTH_MAXIMUM_'))->keyDefault('USERNAME_MAX_LENGTH',32)

            //分组
            ->group(L('_REGISTER_CONFIGURATION_'), 'REG_SWITCH,EMAIL_VERIFY_TYPE,MOBILE_VERIFY_TYPE,REG_STEP,REG_CAN_SKIP,NEW_USER_FOLLOW,NEW_USER_FANS,NEW_USER_FRIENDS')
            ->group(L('_LOGIN_CONFIGURATION_'), 'OPEN_QUICK_LOGIN,LOGIN_SWITCH,OPEN_WECHAT_AUTH')
            ->group(L('_MAILBOX_VERIFICATION_ACTIVATION_'), 'REG_EMAIL_VERIFY,REG_EMAIL_ACTIVATE')
            //->group(L('_MAILBOX_ACTIVATION_TEMPLATE_'), 'REG_EMAIL_ACTIVATE')
            ->group(L('_SMS_VERIFICATION_CONFIG_'), 'SMS_CONTENT,SMS_RESEND')
            
            ->group(L('_BASIC_SETTINGS_'), 'LEVEL,NICKNAME_MIN_LENGTH,NICKNAME_MAX_LENGTH,USERNAME_MIN_LENGTH,USERNAME_MAX_LENGTH')
            ->buttonSubmit('', L('_SAVE_'))
            ->keyDefault('REG_EMAIL_VERIFY',L('_VERICODE_ACCOUNT_').L('_PERIOD_'))
            ->keyDefault('REG_EMAIL_ACTIVATE',L('_LINK_ACTIVE_IS_'));
            
        $admin_config->display();
    }
}
