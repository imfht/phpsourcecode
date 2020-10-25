<?php
/**
 * 模板预处理
 */
namespace Common\Behavior;
use Core\Model\Addon;
use Core\Model\Site;
use Think\Model;

class WebTemplateBehavior {
    public function run(&$params) {
        Site::loadSettings();
        if(MODULE_NAME == 'Bench') {
            $theme = I('cookie.template_theme');
            $themes = array(
                'cerulean',
                'cyborg',
                'simplex',
                'darkly',
                'lumen',
                'slate',
                'spacelab',
                'united'
            );
            if(!empty($theme) && in_array($theme, $themes)) {
                C('TMPL_PARSE_STRING.{__TEMPLATE_THEME__}', '-' . $theme);
            }

            $frames = $this->getBenchFrames();
            $name = C('FRAME_ACTIVE');
            $names = array();
            $names[] = 'summary';
            $names[] = 'cms';
            $names[] = 'uc';
            $names[] = 'extend';
            $names[] = 'addons';
            $names[] = 'analyze';
            $name = in_array($name, $names) ? $name : 'summary';
        }
        if(MODULE_NAME == 'Control') {
            $frames = $this->getControlFrames();
            $name = C('FRAME_ACTIVE');
            $names = array();
            $names[] = 'common';
            $names[] = 'webapp';
            $names[] = 'member';
            $names[] = 'access';
            $names[] = 'extend';
            $names[] = 'addons';
            $names[] = 'store';
            $name = in_array($name, $names) ? $name : 'common';
        }

        $allAddons = array();
        if($name == 'extend') {
            $types = Addon::types();
            foreach($types as $type) {
                $addons = Addon::getAddons($type['name']);
                $allAddons = array_merge($allAddons, coll_key($addons, 'name'));
                $items = array();
                if(!empty($addons)) {
                    foreach($addons as $a) {
                        $addon = new Addon($a);
                        $entryType = strtolower(MODULE_NAME);
                        $entries = $addon->getEntries($entryType);
                        if(!empty($entries)) {
                            $addonName = parse_name($a['name']);
                            $items[] = array('icon' => 'plus', 'title' => $a['title'], 'url' => U("/{$entryType}/extend/{$addonName}"));
                        }
                    }
                }
                if(!empty($items)) {
                    $frames['extend'][] = array(
                        'title' => $type['title'],
                        'items' => $items
                    );
                }
            }
        }
        if($name == 'addons') {
            if(defined('ADDON_NAME')) {
                $a = C('ADDON_INSTANCE');
                $addon = $a->getCurrentAddon();
                $entries = $a->getEntries(strtolower(MODULE_NAME));
                $items = array();
                foreach($entries as $entry) {
                    $items[] = array(
                        'icon'  => 'plus',
                        'url'   => $entry['url'],
                        'title' => $entry['title'],
                    );
                }
                $frames['addons'][] = array(
                    'title' => $addon['title'],
                    'items' => $items
                );
            }
        }

        C('FRAME_ACTIVE', $name);
        $set = $frames[$name];
        $url = C('FRAME_CURRENT');
        if(empty($url)) {
            $url = $_SERVER['REQUEST_URI'];
            C('FRAME_CURRENT', $url);
        }
        foreach($set as &$row) {
            foreach($row['items'] as &$item) {
                if($item['url'] == $url) {
                    $item['current'] = true;
                    if(!C('FRAME_TITLE')) {
                        C('FRAME_TITLE', $item['title']);
                    }
                }
                if(!empty($item['items'])) {
                    foreach($item['items'] as &$sub) {
                        if($sub['url'] == $url) {
                            $sub['current'] = true;
                            if(!C('FRAME_TITLE')) {
                                C('FRAME_TITLE', $sub['title']);
                            }
                        }
                    }
                }
            }
        }
        C('FRAME_SETS', $set);
    }

    private function getControlFrames() {
        $frames = array();
        $frames['common'] = array(
            array(
                'title' => '运营渠道入口',
                'items' => array(
                    array(
                        'icon'  => 'paypal',
                        'title' => '支付宝服务窗',
                        'url'   => U('control/platform/alipay')
                    ),
                    array(
                        'icon'  => 'comments',
                        'title' => '微信公众号',
                        'url'   => U('control/platform/weixin')
                    ),
                    array(
                        'icon'  => 'mobile-phone',
                        'title' => '定制自有App',
                        'url'   => U('control/platform/open')
                    ),
                )
            ),
            array(
                'title'     => '系统维护',
                'items'     => array(
                    array(
                        'icon'  => 'cog',
                        'title' => '站点设置',
                        'url'   => U('control/site/common')
                    ),
                    array(
                        'icon'  => 'level-up',
                        'title' => '升级系统',
                        'url'   => U('control/site/update')
                    ),
                    array(
                        'icon'  => 'refresh',
                        'title' => '更新缓存',
                        'url'   => U('control/site/flush')
                    ),
                    array(
                        'icon'  => 'database',
                        'title' => '数据库',
                        'url'   => U('control/database/backup')
                    )
                )
            ),
        );
        $frames['webapp'] = array(
            array(
                'title'     => '手机Web应用',
                'items'     => array(
                    array(
                        'icon'  => 'cog',
                        'title' => 'Web应用基本参数',
                        'url'   => U('control/app/setting')
                    ),
                    array(
                        'icon'  => 'credit-card',
                        'title' => '支付功能',
                        'url'   => U('control/app/payment')
                    )
                )
            ),
            array(
                'title' => '创建WebApp',
                'items' => array(
                    array(
                        'icon'  => 'plug',
                        'title' => '已安装WebApp模板',
                        'url'   => U('control/app/create?tool=template')
                    ),
                    array(
                        'icon'  => 'paw',
                        'title' => '使用百度轻工厂',
                        'url'   => U('control/app/create?tool=baidu')
                    ),
                    array(
                        'icon'  => 'cloud',
                        'title' => '使用云起轻应用',
                        'url'   => U('control/app/create?tool=cloud7')
                    ),
                    array(
                        'icon'  => 'puzzle-piece',
                        'title' => '使用其他微站工具',
                        'url'   => U('control/app/create?tool=other')
                    ),
                )
            )
        );
        $frames['member'] = array(
            array(
                'title'     => '会员中心',
                'items'     => array(
                    array(
                        'icon'  => 'user',
                        'title' => '会员中心参数',
                        'url'   => U('control/member/setting')
                    ),
                    array(
                        'icon'  => 'users',
                        'title' => '会员组管理',
                        'url'   => U('control/member/groups')
                    ),
                    array(
                        'icon'  => 'cc-visa',
                        'title' => '积分参数',
                        'url'   => U('control/member/credit')
                    ),
                    array(
                        'icon'  => 'credit-card',
                        'title' => '电子会员卡设置',
                        'url'   => U('control/member/card')
                    ),
                    array(
                        'icon'  => 'retweet',
                        'title' => '会员资料整合',
                        'url'   => U('control/member/passport')
                    ),
                )
            ),
            array(
                'title'     => '通知中心',
                'items'     => array(
                    array(
                        'icon'  => 'bell-o',
                        'title' => '通知参数',
                        'url'   => U('control/notify/setting')
                    ),
                )
            )
        );
        $frames['access'] = array(
            array(
                'title'     => '用户管理',
                'items'     => array(
                    array(
                        'icon'  => 'users',
                        'title' => '用户列表',
                        'url'   => U('control/user/list')
                    ),
                    array(
                        'icon'  => 'lock',
                        'title' => '用户组及权限',
                        'url'   => U('control/acl/roles')
                    ),
                )
            ),
            array(
                'title' => '行为统计',
                'items' => array(
                    array(
                        'icon'  => 'book',
                        'title' => '用户操作日志',
                        'url'   => U('control/log/user')
                    ),
                    array(
                        'icon'  => 'cog',
                        'title' => '用户日志参数',
                        'url'   => U('control/log/setting?type=user')
                    ),
                )
            )
        );
        $frames['extend'] = array(
            array(
                'title' => '管理扩展',
                'items' => array(
                    array(
                        'icon'  => 'plug',
                        'title' => '已安装的扩展',
                        'url'   => U('control/extend/list')
                    ),
                    array(
                        'icon'  => 'plus',
                        'title' => '安装本地扩展',
                        'url'   => U('control/extend/install')
                    ),
                )
            ),
        );
        $frames['store'] = array(
            array(
                'title' => '应用商店',
                'items' => array(
                    array(
                        'icon'  => 'mobile-phone',
                        'title' => 'WebApp',
                        'url'   => U('control/store/navigator?type=app')
                    ),
                    array(
                        'icon'  => 'briefcase',
                        'title' => '营销活动',
                        'url'   => U('control/store/navigator?type=activity')
                    ),
                    array(
                        'icon'  => 'heart',
                        'title' => '客户关系',
                        'url'   => U('control/store/navigator?type=crm')
                    ),
                    array(
                        'icon'  => 'gamepad',
                        'title' => '游戏',
                        'url'   => U('control/store/navigator?type=game')
                    ),
                    array(
                        'icon'  => 'gavel',
                        'title' => '工具',
                        'url'   => U('control/store/navigator?type=tool')
                    ),
                )
            ),
            array(
                'title' => '众筹平台',
                'items' => array(
                    array(
                        'icon'  => 'users',
                        'title' => '众筹功能',
                        'url'   => U('control/store/raise')
                    ),
                )
            ),
            array(
                'title' => '站点信息',
                'items' => array(
                    array(
                        'icon'  => 'globe',
                        'title' => '站点信息',
                        'url'   => U('control/store/site')
                    ),
                )
            ),
        );
        return $frames;
    }

    private function getBenchFrames() {
        $frames = array();
        $frames['summary'] = array(
            array(
                'title' => '系统概览',
                'items' => array(
                    array(
                        'icon'  => 'pie-chart',
                        'title' => '数据概览',
                        'url'   => U('control/account/list')
                    ),
                    array(
                        'icon'  => 'list',
                        'title' => '业界动态',
                        'url'   => U('control/account/open')
                    ),
                    array(
                        'icon'  => 'bookmark-o',
                        'title' => '待办事项',
                        'url'   => U('control/account/open')
                    ),
                )
            ),
        );
        $frames['cms'] = array(
            array(
                'title'     => '文章内容',
                'items'     => array(
                    array(
                        'icon'  => 'list',
                        'title' => '文章列表',
                        'url'   => U('control/app/setting')
                    ),
                    array(
                        'icon'  => 'th-list',
                        'title' => '分类管理',
                        'url'   => U('control/app/setting')
                    ),
                    array(
                        'icon'  => 'list-alt',
                        'title' => '专题',
                        'url'   => U('control/app/setting')
                    )
                )
            ),
            array(
                'title'     => '自媒体功能',
                'items'     => array(
                    array(
                        'icon'  => 'bullhorn',
                        'title' => '发布推送内容',
                        'url'   => U('control/app/setting')
                    ),
                    array(
                        'icon'  => 'list-ol',
                        'title' => '发布历史',
                        'url'   => U('control/app/setting')
                    ),
                    array(
                        'icon'  => 'line-chart',
                        'title' => '效果分析',
                        'url'   => U('control/app/setting')
                    ),
                )
            ),
        );
        $frames['uc'] = array(
            array(
                'title'     => '会员中心',
                'items'     => array(
                    array(
                        'icon'  => 'user',
                        'title' => '会员',
                        'url'   => U('control/member/user')
                    ),
                    array(
                        'icon'  => 'cc-visa',
                        'title' => '积分',
                        'url'   => U('control/group/list')
                    ),
                    array(
                        'icon'  => 'credit-card',
                        'title' => '电子会员卡',
                        'url'   => U('control/member/credit')
                    ),
                )
            ),
            array(
                'title'     => '营销功能',
                'items'     => array(
                    array(
                        'icon'  => 'ticket',
                        'title' => '折扣券',
                        'url'   => U('control/notify/setting')
                    ),
                    array(
                        'icon'  => 'newspaper-o',
                        'title' => '代金券',
                        'url'   => U('control/notify/setting')
                    ),
                    array(
                        'icon'  => 'bullhorn',
                        'title' => '群发通知&消息',
                        'url'   => U('control/notify/setting')
                    ),
                )
            ),
            array(
                'title'     => '积分兑换',
                'items'     => array(
                    array(
                        'icon'  => 'gift',
                        'title' => '礼品维护',
                        'url'   => U('control/notify/setting')
                    ),
                    array(
                        'icon'  => 'exchange',
                        'title' => '兑换订单',
                        'url'   => U('control/notify/setting')
                    )
                )
            )
        );
        $frames['extend'] = array();
        $frames['analyze'] = array(
            array(
                'title' => '会员',
                'items' => array(
                    array(
                        'icon'  => 'line-chart',
                        'title' => '会员增长',
                        'url'   => U('control/store/navigator?type=app')
                    ),
                    array(
                        'icon'  => 'pie-chart',
                        'title' => '会员分布',
                        'url'   => U('control/store/navigator?type=activity')
                    ),
                )
            ),
            array(
                'title' => '内容',
                'items' => array(
                    array(
                        'icon'  => 'area-chart',
                        'title' => '访问量',
                        'url'   => U('control/store/navigator?type=app')
                    ),
                    array(
                        'icon'  => 'bar-chart',
                        'title' => '转发量',
                        'url'   => U('control/store/navigator?type=activity')
                    ),
                    array(
                        'icon'  => 'line-chart',
                        'title' => '转换会员情况',
                        'url'   => U('control/store/navigator?type=activity')
                    ),
                )
            ),
            array(
                'title' => '营销',
                'items' => array(
                    array(
                        'icon'  => 'area-chart',
                        'title' => '优惠券使用',
                        'url'   => U('control/store/navigator?type=app')
                    ),
                    array(
                        'icon'  => 'bar-chart',
                        'title' => '积分兑换',
                        'url'   => U('control/store/navigator?type=activity')
                    ),
                    array(
                        'icon'  => 'line-chart',
                        'title' => '利润分析',
                        'url'   => U('control/store/navigator?type=activity')
                    ),
                )
            ),
        );
        return $frames;
    }
}