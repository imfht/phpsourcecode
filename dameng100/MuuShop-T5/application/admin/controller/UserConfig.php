<?php
namespace app\admin\Controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminConfigBuilder;
use app\ucenter\widget\RegStepWidget;

/**
 * Class UserConfigController  后台用户配置控制器
 */
class UserConfig extends Admin
{

    public function index()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

        $mStep = controller('ucenter/RegStep', 'widget')->mStep;
        $step = [];
        foreach ($mStep as $key => $v) {
            $step[] = ['id' => $key, 'title' => $v];
        }
        $default = [
            [
                'id' => 'disable',
                'title' => lang('_DISABLE_'), 
                'items' => $step
            ],
            [
                'id' => 'enable', 
                'title' => lang('_ENABLE_'), 
                'items' => []
             ],
        ];
        //$default=array(lang('_DISABLE_')=>$step,lang('_ENABLE_AND_SKIP_')=>array(),lang('_ENABLE_BUT_NOT_SKIP_')=>array());
        empty($data['REG_STEP']) && $data['REG_STEP'] = '';
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

        
        $admin_config
        
            ->title(lang('_USER_CONFIGURATION_'))
            ->data($data)
            //注册配置
            ->keyCheckBox('REG_SWITCH', lang('_REGISTRATION_SWITCH_'), lang('_THE_REGISTRATION_OPTION_THAT_ALLOWS_THE_USE_OF_THE_REGISTRATION_IS_CLOSED_'), array('username' => lang('_USER_NAME_'),'email' => lang('_MAILBOX_'), 'mobile' => lang('_MOBILE_PHONE_')))

            ->keyRadio('EMAIL_VERIFY_TYPE', lang('_MAILBOX_VERIFICATION_TYPE_'), lang('_TYPE_MAILBOX_VERIFICATION_'), array(0 => lang('_NOT_VERIFIED_'), 1 => lang('_POST_REGISTRATION_ACTIVATION_MAIL_'), 2 => lang('_EMAIL_VERIFY_SEND_BEFORE_REG_')))

            ->keyRadio('MOBILE_VERIFY_TYPE', lang('_MOBILE_VERIFICATION_TYPE_'), lang('_TYPE_OF_CELL_PHONE_VERIFICATION_'), array(0 => lang('_NOT_VERIFIED_'), 1 => lang('_REGISTER_BEFORE_SENDING_A_VALIDATION_MESSAGE_')))
            ->keyText('NEW_USER_FOLLOW', lang('_NEW_USER_ATTENTION_'), lang('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyText('NEW_USER_FANS', lang('_NEW_USER_FANS_'), lang('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyText('NEW_USER_FRIENDS', lang('_NEW_FRIENDS_'), lang('_ID_INPUT_SEPARATE_COMMA_'))
            ->keyKanban('REG_STEP', lang('_REGISTRATION_STEP_'), lang('_STEPS_TO_BE_MADE_AFTER_REGISTRATION_'))
            ->keyCheckBox('REG_CAN_SKIP', lang('_WHETHER_THE_REGISTRATION_STEP_CAN_BE_SKIPPED_'), lang('_CHECK_TO_SKIP_AND_YOU_CANT_SKIP_THE_DEFAULT_'),$mStep)

            //登陆配置
            ->keyRadio('OPEN_QUICK_LOGIN',lang('_QUICK_LOGIN_'),lang('_BY_DEFAULT_AFTER_THE_USER_IS_LOGGED_IN_THE_USER_IS_LOGGED_IN_'), array(0 => lang('_OFF_'), 1 => lang('_OPEN_')))
            ->keyCheckBox('LOGIN_SWITCH', lang('_LOGIN_PROMPT_SWITCH_'), lang('_JUST_THE_TIP_OF_THE_LOGIN_BOX_'), array('username' => lang('_USER_NAME_'), 'email' => lang('_MAILBOX_'), 'mobile' => lang('_MOBILE_PHONE_')))
            ->keyRadio('OPEN_WECHAT_AUTH',lang('_OPEN_WECHAT_AUTH_SWITCH_'),lang('_OPEN_WECHAT_AUTH_SWITCH_INFO_'),array(0 => lang('_OFF_'), 1 => lang('_OPEN_')))

            //邮件验证配置
            ->keyEditor('REG_EMAIL_VERIFY', lang('_MAILBOX_VERIFICATION_TEMPLATE_'), lang('_PLEASE_EMAIL_VERIFY_'),'wangeditor')
            //邮箱验证
            ->keyEditor('REG_EMAIL_ACTIVATE', lang('_MAILBOX_ACTIVATION_TEMPLATE_'), lang('_PLEASE_USER_ACTIVE_'),'wangeditor')

            //短信验证内容
            ->keyTextArea('SMS_CONTENT', lang('_MESSAGE_CONTENT_'), lang('_MSG_VERICODE_ACCOUNT_'))
            ->keyDefault('SMS_CONTENT',lang('_VERICODE_ACCOUNT_'))
            ->keyDefault('SMS_RESEND','60')
            ->keyText('SMS_RESEND', lang('_THE_MESSAGE_RETRANSMISSION_TIME_'), lang('_THE_MESSAGE_RETRANSMISSION_TIME_'))
            
            //基础配置
            ->keyTextArea('LEVEL', lang('_HIERARCHY_'), lang('_ONE_PER_LINE_BETWEEN_THE_NAME_AND_THE_INTEGRAL_BY_A_COLON_'))
            ->keyInteger('NICKNAME_MIN_LENGTH', lang('_NICKNAME_LENGTH_MINIMUM_'))->keyDefault('NICKNAME_MIN_LENGTH',2)
            ->keyInteger('NICKNAME_MAX_LENGTH', lang('_NICKNAME_LENGTH_MAXIMUM_'))->keyDefault('NICKNAME_MAX_LENGTH',32)
            ->keyInteger('USERNAME_MIN_LENGTH', lang('_USERNAME_LENGTH_MINIMUM_'))->keyDefault('USERNAME_MIN_LENGTH',2)
            ->keyInteger('USERNAME_MAX_LENGTH', lang('_USERNAME_LENGTH_MAXIMUM_'))->keyDefault('USERNAME_MAX_LENGTH',32)

            //分组
            ->group(lang('_REGISTER_CONFIGURATION_'), 'REG_SWITCH,EMAIL_VERIFY_TYPE,MOBILE_VERIFY_TYPE,REG_STEP,REG_CAN_SKIP,NEW_USER_FOLLOW,NEW_USER_FANS,NEW_USER_FRIENDS')
            ->group(lang('_LOGIN_CONFIGURATION_'), 'OPEN_QUICK_LOGIN,LOGIN_SWITCH,OPEN_WECHAT_AUTH')
            ->group(lang('_MAILBOX_VERIFICATION_ACTIVATION_'), 'REG_EMAIL_VERIFY,REG_EMAIL_ACTIVATE')
            //->group(lang('_MAILBOX_ACTIVATION_TEMPLATE_'), 'REG_EMAIL_ACTIVATE')
            ->group(lang('_SMS_VERIFICATION_CONFIG_'), 'SMS_CONTENT,SMS_RESEND')
            
            ->group(lang('_BASIC_SETTINGS_'), 'LEVEL,NICKNAME_MIN_LENGTH,NICKNAME_MAX_LENGTH,USERNAME_MIN_LENGTH,USERNAME_MAX_LENGTH')
            ->buttonSubmit('', lang('_SAVE_'))
            ->keyDefault('REG_EMAIL_VERIFY',lang('_VERICODE_ACCOUNT_').lang('_PERIOD_'))
            ->keyDefault('REG_EMAIL_ACTIVATE',lang('_LINK_ACTIVE_IS_'));
            
        $admin_config->display();
    }
}
