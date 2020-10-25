<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Options as OptionsModel;
use app\common\widget\Widget;
use think\facade\Cache;
use think\facade\Env;

class Options extends Base
{

    /**
     * 站点设置
     * @throws
     */
    public function baseIndex()
    {
        //主题get
        $tpls   = get_themes();
        $options_model = new OptionsModel();
        $sys    = $options_model->getOptions('base', $this->lang);
        $widget = new Widget();
        return $widget
            ->addGroup(
                [
                    //基本设置
                    [
                        'title' => '基本设置',
                        'href'  => '',
                        'items' => [
                            ['text', 'site_name', '站点名称', isset($sys['site_name']) ? $sys['site_name'] : '', ' *', 'required'],
                            ['text', 'site_host', '站点网址', isset($sys['site_host']) ? $sys['site_host'] : '', ' *', 'required'],
                            ['select', 'site_tpl', '站点主题(PC)', $tpls, isset($sys['site_tpl']) ? $sys['site_tpl'] : '', ' ', '', ['default' => '']],
                            ['select', 'site_tpl_m', '站点主题(手机)', $tpls, isset($sys['site_tpl_m']) ? $sys['site_tpl_m'] : '', ' ', '', ['default' => '']],
                            ['image', 'site_logo', '网站logo', $sys['site_logo']],
                            ['text', 'site_icp', '备案信息', $sys['site_icp']],
                            ['textarea', 'site_tongji', '统计代码', $sys['site_tongji'], '字数限制500', '', ['maxlength' => 500, 'autosize' => true]],
                            ['textarea', 'site_copyright', '版权信息', $sys['site_copyright'], '字数限制150', '', ['maxlength' => 150, 'autosize' => true]],
                        ]
                    ],
                    //联系方式
                    [
                        'title' => '联系方式',
                        'href'  => '',
                        'items' => [
                            ['text', 'site_co_name', '公司名称', $sys['site_co_name'], '<a class="btn btn-minier btn-yellow btn-get-map" href="' . url('admin/Ajax/getMap') . '">获取map</a>'],
                            ['text', 'site_address', '公司地址', $sys['site_address'], '<a class="btn btn-minier btn-yellow btn-get-map" href="' . url('admin/Ajax/getMap') . '">获取map</a>'],
                            ['text', 'map_lat', '地图lat', $sys['map_lat']],
                            ['text', 'map_lng', '地图lng', $sys['map_lng']],
                            ['text', 'site_tel', '联系电话', $sys['site_tel']],
                            ['text', 'site_admin_email', '站长邮箱', $sys['site_admin_email'], '', '', 'email'],
                            ['text', 'site_qq', '站长QQ', $sys['site_qq'], '', '', 'number']
                        ]
                    ],
                    //SEO设置
                    [
                        'title' => 'SEO设置',
                        'href'  => '',
                        'items' => [
                            ['text', 'site_seo_title', '首页SEO标题', $sys['site_seo_title']],
                            ['textarea', 'site_seo_keywords', '首页SEO关键字', $sys['site_seo_keywords'], '字数限制100,多个关键字以英文 , 号隔开', '', ['maxlength' => 100, 'autosize' => true]],
                            ['textarea', 'site_seo_description', '首页SEO描述', $sys['site_seo_description'], '字数限制200', '', ['maxlength' => 200, 'autosize' => true]]
                        ]
                    ]
                ]
            )
            ->setUrl(url('baseUpdate'))
            ->setTemplate(Env::get('app_path') . 'common/widget/form/layout.html')
            ->setTabHomeTitle('站点设置')
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 保存
     */
    public function baseUpdate()
    {
        $data = input('');
        $options_model = new OptionsModel();
        $options_model->setOptions($data, $this->lang);
        $options_model->delCache('', 'base', $this->lang);
        $this->success('保存成功', 'baseIndex');
    }

    /**
     * 日志设置
     */
    public function logIndex()
    {
        $log             = config('yfcmf.log');
        $log['level']    = empty($log['level']) ? join(',', ['sql', 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency']) : join(',', $log['level']);
        $log['clear_on'] = isset($log['clear_on']) ? $log['clear_on'] : 0;
        $log['timebf']   = isset($log['timebf']) ? $log['timebf'] : 0;
        $options         = [
            ['value' => 'sql', 'name' => 'sql', 'subtext' => 'sql日志'],
            ['value' => 'debug', 'name' => 'debug', 'subtext' => '调试日志'],
            ['value' => 'info', 'name' => 'info', 'subtext' => '信息日志'],
            ['value' => 'notice', 'name' => 'notice', 'subtext' => '注意日志'],
            ['value' => 'warning', 'name' => 'warning', 'subtext' => '警告日志'],
            ['value' => 'error', 'name' => 'error', 'subtext' => '错误日志'],
            ['value' => 'critical', 'name' => 'critical', 'subtext' => '关键日志'],
            ['value' => 'alert', 'name' => 'alert', 'subtext' => '警戒日志'],
            ['value' => 'emergency', 'name' => 'emergency', 'subtext' => '紧急日志'],
        ];
        $widget          = new Widget();
        return $widget
            ->addSwitch('clear_on', '定时清理日志', $log['clear_on'])
            ->addText('timebf', '清理多久前日志', $log['timebf'], '单位秒')
            ->addSelects('log_level[]', '日志记录', $options, $log['level'], '', '', [], ['multiple' => 1])
            ->setUrl(url('logUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 日志设置保存
     */
    public function logUpdate()
    {
        $log_level       = input('log_level/a');
        $log['clear_on'] = input('clear_on', 0, 'intval') ? true : false;
        $log['timebf']   = input('timebf', 2592000, 'intval');
        $log['level']    = (count($log_level) == 9 || empty($log_level)) ? [] : $log_level;
        sys_config_setbykey('log', $log);
        Cache::clear();
        $this->success('日志设置成功', 'logIndex');
    }

    /**
     * 多语言设置显示
     */
    public function langIndex()
    {
        $widget = new Widget();
        return $widget
            ->addSwitch('lang_switch_on', '是否开启多语言', config('yfcmf.lang_switch_on'))
            ->addText('default_lang', '默认多语言', config('yfcmf.default_lang'))
            ->setUrl(url('langUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 多语言设置保存
     */
    public function langUpdate()
    {
        $lang_switch_on = input('lang_switch_on', 0, 'intval') ? true : false;
        $default_lang   = input('default_lang', '');
        sys_config_setbykey('lang_switch_on', $lang_switch_on);
        sys_config_setbykey('default_lang', $default_lang);
        Cache::clear();
        cookie('think_var', null);
        $this->success('多语言设置成功', 'langIndex');
    }

    /**
     * 发送邮件设置显示
     */
    public function emailIndex()
    {
        $options_model = new OptionsModel();
        $sys     = $options_model->getOptions('email_options', $this->lang);
        $options = [
            ''    => '普通连接方式',
            'ssl' => 'SSL连接方式',
            'tls' => 'TLS连接方式'
        ];
        $widget  = new Widget();
        return $widget
            ->addSwitch('email_open', '是否开启邮箱', $sys['email_open'])
            ->addText('email_rename', '发件人姓名', $sys['email_rename'])
            ->addText('email_name', '设置发送邮箱', $sys['email_name'])
            ->addText('email_smtpname', 'smtp服务器的名称', $sys['email_smtpname'])
            ->addSelect('smtpsecure', '连接方式', $options, $sys['smtpsecure'] ?: 'ssl')
            ->addText('smtp_port', 'SMTP服务器端口', $sys['smtp_port'] ?: 465)
            ->addText('email_emname', '设置邮箱登录名', $sys['email_emname'])
            ->addText('email_pwd', '设置邮箱密码', $sys['email_pwd'], '一般为授权码,网页登录邮箱设置里查看')
            ->setUrl(url('emailUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 发送邮件设置保存
     */
    public function emailUpdate()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'emailIndex');
        } else {
            $data = input('');
            $options_model = new OptionsModel();
            $options_model->setOptions($data, $this->lang);
            $options_model->delCache('', 'email_options', $this->lang);
            $this->success('邮箱设置保存成功', 'emailIndex');
        }
    }

    /**
     * 帐号激活设置显示
     */
    public function activeIndex()
    {
        $options_model = new OptionsModel();
        $sys    = $options_model->getOptions('active_options', $this->lang);
        $widget = new Widget();
        return $widget
            ->addSwitch('email_active', '是否开启邮箱激活', $sys['email_active'])
            ->addText('email_title', '邮件标题', $sys['email_title'])
            ->addUeditor('email_tpl', '模板内容', $sys['email_tpl'], '请用http://#link#代替激活链接，#username#代替用户名')
            ->setUrl(url('activeUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 帐号激活设置保存
     */
    public function activeUpdate()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'activeIndex');
        } else {
            $data              = input('');
            $data['email_tpl'] = htmlspecialchars_decode($data['email_tpl']);
            $options_model = new OptionsModel();
            $options_model->setOptions($data, $this->lang);
            $options_model->delCache('', 'email_active', $this->lang);
            $this->success('帐号激活设置保存成功', 'activeIndex');
        }
    }

    /**
     * 第三方登录设置
     */
    public function oauthIndex()
    {
        $oauth_qq       = sys_config_get('think_sdk_qq');
        $oauth_sina     = sys_config_get('think_sdk_sina');
        $oauth_weixin   = sys_config_get('think_sdk_weixin');
        $oauth_wechat   = sys_config_get('think_sdk_wechat');
        $oauth_facebook = sys_config_get('think_sdk_facebook');
        $oauth_google   = sys_config_get('think_sdk_google');
        $widget         = new Widget();
        return $widget
            ->addGroup(
                [
                    [
                        'title' => 'QQ',
                        'href'  => '',
                        'items' => [
                            ['switch', 'qq_display', '是否开启', $oauth_qq['display'] ? 1 : 0, '默认关闭'],
                            ['text', 'qq_appid', 'QQ APPID', isset($oauth_qq['qq_appid']) ? $oauth_qq['qq_appid'] : '', ' *'],
                            ['text', 'qq_appkey', 'QQ APP KEY', isset($oauth_qq['qq_asecret']) ? $oauth_qq['qq_secret'] : '', ' *']
                        ]
                    ],
                    [
                        'title' => 'Sina',
                        'href'  => '',
                        'items' => [
                            ['switch', 'sina_display', '是否开启', $oauth_sina['display'] ? 1 : 0, '默认关闭'],
                            ['text', 'sina_appid', '新浪微博 APPID', isset($oauth_sina['sina_appid']) ? $oauth_sina['sina_appid'] : '', ' *'],
                            ['text', 'sina_appkey', '新浪微博 APP KEY', isset($oauth_sina['sina_secret']) ? $oauth_sina['sina_secret'] : '', ' *']
                        ]
                    ],
                    [
                        'title' => '微信扫码',
                        'href'  => '',
                        'items' => [
                            ['switch', 'weixin_display', '是否开启', $oauth_weixin['display'] ? 1 : 0, '默认关闭'],
                            ['text', 'weixin_appid', '微信扫码 APPID', isset($oauth_weixin['weixin_appid']) ? $oauth_weixin['weixin_appid'] : '', ' *'],
                            ['text', 'weixin_appkey', '微信扫码 APP KEY', isset($oauth_weixin['weixin_secret']) ? $oauth_weixin['weixin_secret'] : '', ' *']
                        ]
                    ],
                    [
                        'title' => '微信公众登录',
                        'href'  => '',
                        'items' => [
                            ['switch', 'wechat_display', '是否开启', $oauth_wechat['display'] ? 1 : 0, '默认关闭'],
                            ['text', 'wechat_appid', '微信公众 APPID', isset($oauth_wechat['wechat_appid']) ? $oauth_wechat['wechat_appid'] : '', ' *'],
                            ['text', 'wechat_appkey', '微信公众 APP KEY', isset($oauth_wechat['wechat_secret']) ? $oauth_wechat['wechat_secret'] : '', ' *']
                        ]
                    ],
                    [
                        'title' => 'Google',
                        'href'  => '',
                        'items' => [
                            ['switch', 'google_display', '是否开启', $oauth_google['display'] ? 1 : 0, '默认关闭'],
                            ['text', 'google_appid', 'Google APPID', isset($oauth_google['google_appid']) ? $oauth_google['google_appid'] : '', ' *'],
                            ['text', 'google_appkey', 'Google APP KEY', isset($oauth_google['google_secret']) ? $oauth_google['google_secret'] : '', ' *']
                        ]
                    ],
                    [
                        'title' => 'Facebook',
                        'href'  => '',
                        'items' => [
                            ['switch', 'facebook_display', '是否开启', $oauth_facebook['display'] ? 1 : 0, '默认关闭'],
                            ['text', 'facebook_appid', 'Facebook APPID', isset($oauth_facebook['facebook_appid']) ? $oauth_facebook['facebook_appid'] : '', ' *'],
                            ['text', 'facebook_appkey', 'Facebook APP KEY', isset($oauth_facebook['facebook_secret']) ? $oauth_facebook['facebook_secret'] : '', ' *']
                        ]
                    ]
                ]
            )
            ->setUrl(url('oauthUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 第三方登录设置保存
     */
    public function oauthUpdate()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确', 'oauthIndex');
        } else {
            $host = get_host();
            $data = [
                'think_sdk_qq'       => [
                    'app_key'    => input('qq_appid'),
                    'app_secret' => input('qq_appkey'),
                    'display'    => input('qq_display', 0, 'intval') ? true : false,
                    'callback'   => $host . url('home/Oauth/callback', 'type=qq'),
                ],
                'think_sdk_weixin'   => [
                    'app_key'    => input('weixin_appid'),
                    'app_secret' => input('weixin_appkey'),
                    'display'    => input('weixin_display', 0, 'intval') ? true : false,
                    'callback'   => $host . url('home/Oauth/callback', 'type=weixin'),
                ],
                'think_sdk_wechat'   => [
                    'app_key'    => input('wechat_appid'),
                    'app_secret' => input('wechat_appkey'),
                    'display'    => input('wechat_display', 0, 'intval') ? true : false,
                    'callback'   => $host . url('home/Oauth/callback', 'type=wechat'),
                ],
                'think_sdk_google'   => [
                    'app_key'    => input('google_appid'),
                    'app_secret' => input('google_appkey'),
                    'display'    => input('google_display', 0, 'intval') ? true : false,
                    'callback'   => $host . url('home/Oauth/callback', 'type=google'),
                ],
                'think_sdk_facebook' => [
                    'app_key'    => input('facebook_appid'),
                    'app_secret' => input('facebook_appkey'),
                    'display'    => input('facebook_display', 0, 'intval') ? true : false,
                    'callback'   => $host . url('home/Oauth/callback', 'type=facebook'),
                ],
                'think_sdk_sina'     => [
                    'app_key'    => input('sina_appid'),
                    'app_secret' => input('sina_appkey'),
                    'display'    => input('sina_display', 0, 'intval') ? true : false,
                    'callback'   => $host . url('home/Oauth/callback', 'type=sina'),
                ],
            ];
            $rst  = sys_config_setbyarr($data);
            if ($rst) {
                Cache::clear();
                $this->success('设置保存成功', 'oauthIndex');
            } else {
                $this->error('设置保存失败', 'oauthIndex');
            }
        }
    }

    /**
     * 缓存设置
     */
    public function cacheIndex()
    {
        $cache = config('cache.');
        if (isset($cache['default'])) {
            $cache_default = $cache['default']['type'];
        } elseif ($cache['type'] != 'complex') {
            $cache_default = $cache['type'];
        } else {
            $cache_default = 'file';
        }
        $is_complex = $cache['type'] == 'complex' ? true : false;
        $disabled   = [];
        if (!extension_loaded('redis')) {
            $is_redis   = false;
            $disabled[] = 'redis';
        } else {
            $is_redis = true;
        }
        if (!extension_loaded('memcache')) {
            $is_memcache = false;
            $disabled[]  = 'memcache';
        } else {
            $is_memcache = true;
        }
        if (!extension_loaded('memcached')) {
            $is_memcached = false;
            $disabled[]   = 'memcached';
        } else {
            $is_memcached = true;
        }
        $widget = new Widget();
        return $widget
            ->addGroup(
                [
                    [
                        'title' => '基本设置',
                        'href'  => '',
                        'items' => [
                            ['radio', 'cache_type', '缓存类型：', ['single' => '单一缓存', 'complex' => '复合缓存'], $cache['type'] == 'complex' ?: 'single'],
                            ['radio', 'cache_default', '缓存方式(默认)：', ['file' => 'File', 'redis' => 'Redis', 'memcache' => 'Memcache', 'memcached' => 'Memcached'], $cache_default, $disabled],
                            ['text', 'expire', '有效期：', ($is_complex ? $cache['default']['expire'] : $cache['expire']), '', '', 'number'],
                            ['text', 'prefix', '前缀：', ($is_complex ? $cache['default']['prefix'] : $cache['prefix'])],
                        ]
                    ],
                    [
                        'title' => 'File',
                        'href'  => '',
                        'items' => [
                            ['switch', 'file_open', '是否开启：', (isset($cache['file']) || $cache_default == 'file') ? 1 : 0],
                            ['text', 'file_path', '缓存路径：', ($is_complex ? (isset($cache['file']) ? $cache['file']['path'] : ($cache_default == 'file' ? $cache['default']['path'] : '../runtime/cache/')) : ($cache_default == 'file' ? $cache['path'] : '../runtime/cache/'))]
                        ]
                    ],
                    [
                        'title' => 'Redis',
                        'href'  => '',
                        'items' => [
                            ['switch', 'redis_open', '是否开启', ($is_redis && (isset($cache['redis']) || $cache_default == 'redis')) ? 1 : 0, '', (!$is_redis ? ['disabled' => true] : [])],
                            ['text', 'redis_host', 'HOST：', ($is_complex ? (isset($cache['redis']) ? $cache['redis']['host'] : ($cache_default == 'redis' ? $cache['default']['host'] : '127.0.0.1')) : ($cache_default == 'redis' ? $cache['host'] : '127.0.0.1'))],
                            ['text', 'redis_port', 'PORT：', ($is_complex ? (isset($cache['redis']) ? $cache['redis']['port'] : ($cache_default == 'redis' ? $cache['default']['port'] : 6379)) : ($cache_default == 'redis' ? $cache['port'] : 6379))]
                        ]
                    ],
                    [
                        'title' => 'Memcache',
                        'href'  => '',
                        'items' => [
                            ['switch', 'memcache_open', '是否开启', ($is_memcache && (isset($cache['memcache']) || $cache_default == 'memcache')) ? 1 : 0, '', (!$is_memcache ? ['disabled' => true] : [])],
                            ['text', 'memcache_host', 'HOST：', ($is_complex ? (isset($cache['memcache']) ? $cache['memcache']['host'] : ($cache_default == 'memcache' ? $cache['default']['host'] : '127.0.0.1')) : ($cache_default == 'memcache' ? $cache['host'] : '127.0.0.1'))],
                            ['text', 'memcache_port', 'PORT：', ($is_complex ? (isset($cache['memcache']) ? $cache['memcache']['port'] : ($cache_default == 'memcache' ? $cache['default']['port'] : 11211)) : ($cache_default == 'memcache' ? $cache['port'] : 11211))]
                        ]
                    ],
                    [
                        'title' => 'Memcached',
                        'href'  => '',
                        'items' => [
                            ['switch', 'memcached_open', '是否开启', ($is_memcached && (isset($cache['memcached']) || $cache_default == 'memcached')) ? 1 : 0, '', (!$is_memcached ? ['disabled' => true] : [])],
                            ['text', 'memcached_host', 'HOST：', ($is_complex ? (isset($cache['memcached']) ? $cache['memcached']['host'] : ($cache_default == 'memcached' ? $cache['default']['host'] : '127.0.0.1')) : ($cache_default == 'memcached' ? $cache['host'] : '127.0.0.1'))],
                            ['text', 'memcached_port', 'PORT：', ($is_complex ? (isset($cache['memcached']) ? $cache['memcached']['port'] : ($cache_default == 'memcached' ? $cache['default']['port'] : 11211)) : ($cache_default == 'memcached' ? $cache['port'] : 11211))]
                        ]
                    ]
                ]
            )
            ->setTrigger('file_open', 1, 'file_path')
            ->setTrigger('redis_open', 1, 'redis_host')
            ->setTrigger('redis_open', 1, 'redis_port')
            ->setTrigger('memcache_open', 1, 'memcache_host')
            ->setTrigger('memcache_open', 1, 'memcache_port')
            ->setTrigger('memcached_open', 1, 'memcached_host')
            ->setTrigger('memcached_open', 1, 'memcached_port')
            ->setUrl(url('cacheUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 缓存设置
     */
    public function cacheUpdate()
    {
        $cache_type    = input('cache_type', 'single');
        $cache_default = input('cache_default', 'file');
        $expire        = input('expire', 0, 'intval');
        $prefix        = input('prefix', 'think');
        if ($cache_type == 'single') {
            $cache = [
                'type'   => $cache_default,
                'expire' => $expire,
                'prefix' => $prefix,
            ];
            switch ($cache_default) {
                case 'file':
                    $cache['path'] = input('file_path', '../runtime/cache/');
                    break;
                case 'redis':
                    $cache['host'] = input('redis_host', '127.0.0.1');
                    $cache['port'] = input('redis_port', 6379, 'intval');
                    break;
                case 'memcache':
                case 'memcached':
                    $cache['host'] = input($cache_default . '_host', '127.0.0.1');
                    $cache['port'] = input($cache_default . '_port', 11211, 'intval');
                    break;
            }
        } else {
            $cache['type']    = 'complex';
            $cache['default'] = [
                'type'   => $cache_default,
                'expire' => $expire,
                'prefix' => $prefix,
            ];
            switch ($cache_default) {
                case 'file':
                    $cache['default']['path'] = input('file_path', '../runtime/cache/');
                    break;
                case 'redis':
                    $cache['default']['host'] = input('redis_host', '127.0.0.1');
                    $cache['default']['port'] = input('redis_port', 6379, 'intval');
                    break;
                case 'memcache':
                case 'memcached':
                    $cache['default']['host'] = input($cache_default . '_host', '127.0.0.1');
                    $cache['default']['port'] = input($cache_default . '_port', 11211, 'intval');
                    break;
            }
            if (input('file_open')) {
                $cache['file'] = [
                    'type'   => 'file',
                    'expire' => $expire,
                    'prefix' => $prefix,
                    'path'   => input('file_path', '../runtime/cache/')
                ];
            }
            if (input('redis_open') && extension_loaded('redis')) {
                $cache['redis'] = [
                    'type'   => 'redis',
                    'expire' => $expire,
                    'prefix' => $prefix,
                    'host'   => input('redis_host', '127.0.0.1'),
                    'port'   => input('redis_port', 6379, 'intval')
                ];
            }
            if (input('memcache_open') && extension_loaded('memcache')) {
                $cache['memcache'] = [
                    'type'   => 'memcache',
                    'expire' => $expire,
                    'prefix' => $prefix,
                    'host'   => input('memcache_host', '127.0.0.1'),
                    'port'   => input('memcache_port', 11211, 'intval')
                ];
            }
            if (input('memcached_open') && extension_loaded('memcached')) {
                $cache['memcached'] = [
                    'type'   => 'memcached',
                    'expire' => $expire,
                    'prefix' => $prefix,
                    'host'   => input('memcached_host', '127.0.0.1'),
                    'port'   => input('memcached_port', 11211, 'intval')
                ];
            }
        }
        //保存
        $rst = sys_config_setbyarr(['cache' => $cache]);
        if ($rst) {
            Cache::clear();
            $this->success('设置保存成功', 'cacheIndex');
        } else {
            $this->error('设置保存失败', 'cacheIndex');
        }
    }

    /**
     * 云存储设置
     */
    public function storageIndex()
    {
        $storage = config('yfcmf.storage');
        $widget  = new Widget();
        return $widget
            ->addSwitch('storage_open', '是否开启云存储', (isset($storage['storage_open']) ? $storage['storage_open'] : 0))
            ->addText('accesskey', 'AccessKey', (isset($storage['accesskey']) ? $storage['accesskey'] : ''))
            ->addText('secretkey', 'SecretKey', (isset($storage['secretkey']) ? $storage['secretkey'] : ''))
            ->addText('bucket', '存储空间(bucket)', (isset($storage['bucket']) ? $storage['bucket'] : ''))
            ->addText('domain', '访问域名(domain)', (isset($storage['domain']) ? $storage['domain'] : ''), '必须以"/"结尾')
            ->setUrl(url('storageUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 云存储设置保存
     */
    public function storageUpdate()
    {
        $storage = [
            'storage_open' => input('storage_open', 0) ? true : false,
            'accesskey'    => input('accesskey', ''),
            'secretkey'    => input('secretkey', ''),
            'bucket'       => input('bucket', ''),
            'domain'       => input('domain', '')
        ];
        if ($storage['storage_open'] && (!$storage['accesskey'] || !$storage['secretkey'] || !$storage['bucket'] || !$storage['domain'])) {
            $this->error('配置全不为空才能开启', 'storageIndex');
        }
        $rst     = sys_config_setbyarr(['storage' => $storage]);
        if ($rst) {
            Cache::clear();
            $this->success('设置保存成功', 'storageIndex');
        } else {
            $this->error('设置保存失败', 'storageIndex');
        }
    }
}