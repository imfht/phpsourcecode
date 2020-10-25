<?php

/**
 * 后台默认控制器文件
 *
 * @author banyanCheung <banyan@ibos.com.cn>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2012-2013 IBOS Inc
 */
/**
 * 后台模块默认控制器类
 *
 * @package application.modules.dashboard.controllers
 * @author banyanCheung <banyan@ibos.com.cn>
 * @version $Id$
 */

namespace application\modules\dashboard\controllers;

use application\core\model\Log;
use application\core\model\Module;
use application\core\utils\Env;
use application\core\utils\Ibos;
use application\core\utils\StringUtil;
use application\modules\dashboard\model\Menu;
use application\modules\dashboard\utils\SyncWx;
use application\modules\main\utils\Main as MainUtil;
use application\modules\user\components\UserIdentity;
use CHtml;

class DefaultController extends BaseController
{

    /**
     * 登陆处理
     * @return void
     */
    public function actionLogin()
    {
        $access = $this->getAccess();
        $defaultUrl = 'default/index';
        $wxBinding = SyncWx::getInstance()->checkBindingWxAndAuthContact();
        // 已登录即跳转
        if ($access > 0) {
            $this->success(Ibos::lang('Login succeed'), $this->createUrl($defaultUrl));
        }
        // $referStr = Env::getRequest('refer');
        // $referArray = array_filter(explode('&', $referStr));
        // $refer = array_shift($referArray);
        // 显示登陆页面
        if (!Env::submitCheck('formhash')) {
            $data = array(
                'userName' => !empty($this->user) ? $this->user['username'] : '',
                'isbindingwx' => $wxBinding['isBindingWx'],
                // 'refer' => urlencode($refer)
            );
            $this->render('login', $data);
        } else {
            $userName = Env::getRequest('username');
            $passWord = Env::getRequest('password');
            if (!$passWord || $passWord != CHtml::encode($passWord)) {
                $this->error(Ibos::lang('Passwd illegal'));
            }
            // 开始验证
            // 登录类型
            if (StringUtil::isMobile($userName)) {
                $loginType = 4;
            } else if (StringUtil::isEmail($userName)) {
                $loginType = 2;
            } else {
                $loginType = 1;
            };
            //添加对userName的转义，防止SQL错误
            $userName = CHtml::encode($userName);
            $identity = new UserIdentity($userName, $passWord, $loginType);
            $result = $identity->authenticate(true);
            if ($result > 0) {
                Ibos::app()->user->login($identity);
                $refer = '';
                $httpRefer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                $httpPath = parse_url($httpRefer);
                if (isset($httpPath['query'])) {
                    parse_str($httpPath['query'], $arr);
                    if (isset($arr['refer'])) {
                        $refer = $arr['refer'];
                    }
                }

                if (empty($refer)) {
                    $redirectUrl = $this->createUrl($defaultUrl);
                } else {
                    $redirectUrl = Ibos::app()->getBaseUrl() . $refer;
                }
                $this->success(Ibos::lang('Login succeed')
                    , $redirectUrl);
            } else {
                // 记录登录错误日志
                // 加密密码字符串
                $passWord = preg_replace("/^(.{" . round(strlen($passWord) / 4) .
                    "})(.+?)(.{" . round(strlen($passWord) / 6) . "})$/s", "\\1***\\3", $passWord);
                $log = array(
                    'user' => $userName,
                    'password' => $passWord,
                    'ip' => Ibos::app()->setting->get('clientip'),
                    'result' => $result
                );
                Log::write($log, 'illegal', 'module.dashboard.login');
                switch ($result) {
                    case UserIdentity::USER_NO_ACCESS:
                        $msg = Ibos::lang('Login failed, not admin');
                        break;
                    case UserIdentity::USER_PASSWORD_INCORRECT:
                        $msg = Ibos::lang('Passwd illegal');
                        break;
                    default:
                        $msg = Ibos::lang('Login failed');
                }
                $this->error($msg);
            }
        }
    }

    /**
     * 外层框架主页
     * @return void
     */
    public function actionIndex()
    {
        // 视图变量
        $data = array();
        $data['moduleMenu'] = Menu::model()->filterWeiboModule();
        // 控制器连接生成
        foreach ($this->getControllerMap() as $category => $routes) {
            foreach ($routes as $routeName => $routeConfig) {
                $data['routes'][$category][$routeName] = array(
                    'url' => $this->createUrl(strtolower($routeName)),
                    'config' => $routeConfig,
                );
            }
        }
        $refer = Env::getRequest('refer');
        if ($refer == $this->createUrl('default/index')) {
            $refer = $this->createUrl('index/index');
        }
        $def = !empty($refer) ? $refer : $this->createUrl('index/index');
        $data['def'] = $def;
        $data['cateConfig'] = $this->returnCateConfig();
        $this->render('index', $data);
    }

    /**
     * 查询后台操作
     * @return void
     */
    public function actionSearch()
    {
        if (Env::submitCheck('formhash')) {
            $data = array();
            $keywords = trim($_POST['keyword']);
            $kws = array_map('trim', explode(' ', $keywords));
            $keywords = implode(' ', $kws);
            if ($keywords) {
                $searchIndex = Ibos::getLangSource('dashboard.searchIndex');
                $result = $html = array();
                // 查找关键字所在的项目
                foreach ($searchIndex as $skey => $items) {
                    foreach ($kws as $kw) {
                        foreach ($items['text'] as $k => $text) {
                            if (strpos(strtolower($text), strtolower($kw)) !== false) {
                                $result[$skey][] = $k;
                            }
                        }
                    }
                }
                // 处理好引号给前台用以高亮显示关键字
                $data['kws'] = array_map((function ($item) {
                    return sprintf('"%s"', $item);
                }), $kws);
                if ($result) {
                    $totalCount = 0;
                    $item = Ibos::lang('Item');
                    foreach ($result as $skey => $tkeys) {
                        // 具体项目的链接
                        $tmp = array();
                        foreach ($searchIndex[$skey]['index'] as $title => $url) {
                            $tmp[] = '<a href="' . $url . '" target="_self">' . $title . '</a>';
                        }
                        $links = implode(' &raquo; ', $tmp);
                        $texts = array();
                        $tkeys = array_unique($tkeys);
                        foreach ($tkeys as $tkey) {
                            $texts[] = '<li><span data-class="highlight">' . $searchIndex[$skey]['text'][$tkey] . '</span></li>';
                        }
                        $texts = implode('', array_unique($texts));
                        $totalCount += $count = count($tkeys);
                        $html[] = <<<EOT
								<div class="ctb">
									<h2 class="st">{$count} {$item}</h2>
									<div>
										<strong>{$links}</strong>
										<ul class="tipsblock">{$texts}</ul>
									</div>
								</div>
EOT;
                    }
                    if ($totalCount) {
                        $data['total'] = $totalCount;
                        $data['html'] = $html;
                    } else {
                        $data['msg'] = Ibos::lang('Search result noexists');
                    }
                } else {
                    $data['msg'] = Ibos::lang('Search result noexists');
                }
            } else {
                $data['msg'] = Ibos::lang('Search keyword noexists');
            }
            $this->render('search', $data);
        }
    }

    /**
     * getter方法,获取控制器映射数组
     * @return array
     */
    protected function getControllerMap()
    {
        $map = array(
            'index' => array(
                'index/index' => array(
                    'lang' => 'Management center home page',
                    'isShow' => false,
                ),
//                'status/index' => array(
//                    'lang' => 'System state',
//                    'isShow' => false,
//                ),
//                'announcement/setup' => array(
//                    'lang' => 'System announcement',
//                    'isShow' => ENGINE === 'SAAS' ? false : true,
//                ),
//                'upgrade/index' => array(
//                    'lang' => 'Online upgrade',
//                    'isShow' => ENGINE === 'SAAS' ? false : true,
//                ),
//                'unit/index' => array(
//                    'lang' => 'Unit management',
//                    'isShow' => true,
//                ),
            ),
//            'binding' => array(
//                 'wxbinding/index' => array(
//                    'lang' => 'Weixin binding',
//                    'isShow' => true,
//                ), 'cobinding/index' => array(
//                    'lang' => 'Co binding',
//                    'isShow' => true,
//                ), 'im/index' => array(
//                    'lang' => 'Company QQ',
//                    'isShow' => ENGINE === 'SAAS' ? false : true,
//                ),
//            ),
            'global' => array(
                'approval/index' => array(
                    'lang' => 'Verfiy Definition',
                    'isShow' => true,
                ),
//                'date/index' => array(
//                    'lang' => 'Time and date format',
//                    'isShow' => false,
//                ),
                'credit/setup' => array(
                    'lang' => 'Integral set',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
                'usergroup/index' => array(
                    'lang' => 'User group',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
//                'optimize/cache' => array(
//                    'lang' => 'Performance optimization',
//                    'isShow' => false,
//                ),
                'upload/index' => array(
                    'lang' => 'Upload file limit',
                    'isShow' => true,
                ),
//                'sms/manager' => array(
//                    'lang' => 'Sms setting',
//                    'isShow' => false,
//                ),
                'syscode/index' => array(
                    'lang' => 'System code setting',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
                'email/setup' => array(
                    'lang' => 'Email setting',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
//                'security/setup' => array(
//                    'lang' => 'Security setting',
//                    'isShow' => false,
//                ),
                'sysstamp/index' => array(
                    'lang' => 'System stamp',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
//                'notify/setup' => array(
//                    'lang' => 'Notify setup',
//                    'isShow' => false,
//                ),
                'database/backup' => array(
                    'lang' => 'Database',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
                'cron/index' => array(
                    'lang' => 'Scheduled task',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
                'nav/index' => array(
                    'lang' => 'Navigation setting',
                    'isShow' => false,
                ),
                'quicknav/index' => array(
                    'lang' => 'Quicknav setting',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
                'background/index' => array(
                    'lang' => 'Nav Blendent',
                    'isShow' => true,
                ),
                'login/index' => array(
                    'lang' => 'Login background',
                    'isShow' => true,
                ),
                'unit/index' => array(
                    'lang' => 'Modify Corp Info',
                    'isShow' => true,
                ),
                'update/index' => array(
                    'lang' => 'Update cache',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
                'upgrade/index' => array(
                    'lang' => 'Online upgrade',
                    'isShow' => ENGINE === 'SAAS' ? false : true,
                ),
            ),
            'organization' => array(
                'user/index' => array(
                    'lang' => 'Department personnel management',
                    'isShow' => true,
                ),
                'position/index' => array(
                    'lang' => 'Position management',
                    'isShow' => true,
                ),
                'role/index' => array(
                    'lang' => 'Front Module Manager Auth',
                    'isShow' => true,
                ),
                'roleadmin/index' => array(
                    'lang' => 'Background Module Manager Auth',
                    'isShow' => true,
                ),
            ),
//            'interface' => array(
//                'nav/index' => array(
//                    'lang' => 'Navigation setting',
//                    'isShow' => true,
//                ), 'quicknav/index' => array(
//                    'lang' => 'Quicknav setting',
//                    'isShow' => ENGINE === 'SAAS' ? false : true,
//                ), 'login/index' => array(
//                    'lang' => 'System background setting',
//                    'isShow' => true,
//                ),
//            ),
            'module' => array(
                'module/manager' => array(
                    'lang' => 'Module manager',
                    'isShow' => true,
                ),
                'permissions/setup' => array(
                    'lang' => 'Permissions setup',
                    'isShow' => false,
                ),
            ),
//            'manager' => array(
//                'update/index' => array(
//                    'lang' => 'Update cache',
//                    'isShow' => true,
//                ), 'announcement/setup' => array(
//                    'lang' => 'System announcement',
//                    'isShow' => true,
//                ),
//                'database/backup' => array(
//                    'lang' => 'Database',
//                    'isShow' => ENGINE === 'SAAS' ? false : true,
//                ), 'split/index' => array(
//                    'lang' => 'Table archive',
//                    'isShow' => false,
//                ), 'cron/index' => array(
//                    'lang' => 'Scheduled task',
//                    'isShow' => true,
//                ), 'fileperms/index' => array(
//                    'lang' => 'Check file permissions',
//                    'isShow' => false,
//                ), 'upgrade/index' => array(
//                    'lang' => 'Online upgrade',
//                    'isShow' => ENGINE === 'SAAS' ? false : true,
//                ),
//            ),
        );
//        $map['module'] = $this->getModuleInstallConfig();
//        if (ENGINE !== 'SAAS') {
//            $map['service'] = array(
//                'service/index' => array(
//                    'lang' => 'Shop',
//                    'isShow' => true,
//                ),
//            );
//        }
        return $map;
    }

//    private  function getModuleInstallConfig()
//    {
//        $menus = Menu::model()->fetchAllRootMenu();
//        $returnMenu = array();
//        if (!empty($menus)){
//            foreach ($menus as $menu){
//                $returnMenu[$menu['m'].'/'.$menu['c'].'/'.$menu['a']] = array(
//                    'lang' => 'Role management',
//                    'isShow' => true,
//                );
//            }
//        }
//        return $returnMenu;
//    }
    /**
     * 登出操作
     * @return void
     */
    public function actionLogout()
    {
        Ibos::app()->user->logout();
        $this->showMessage(Ibos::lang('Logout succeed'), Ibos::app()->urlManager->createUrl($this->loginUrl));
    }

    /**
     * 设置侧栏配置
     * @return type
     */
    private function returnCateConfig()
    {
        $cate = array(
            'index' => array(
                'lang' => 'Home page',
                'url' => $this->createUrl('index/index'),
                'id' => 'index',
            ),
            'module' => array(
                'lang' => 'Module Setting',
                'url' => $this->getFirstInstallModuleUrl(),
                'id' => 'module',
            ),
            'organization' => array(
                'lang' => 'Contact Manager',
                'url' => $this->createUrl('user/index'),
                'id' => 'user',
            ),
            'global' => array(
                'lang' => 'Common Setting',
                'url' => $this->createUrl('approval/index'),
                'id' => 'global',
            ),
//            'interface' => array(
//                'lang' => 'Interface',
//                'url' => $this->createUrl('nav/index'),
//                'id' => 'interface',
//            ),
//            'binding' => array(
//                'lang' => 'Connect',
//                'url' => $this->createUrl('wxbinding/index'),
//                'id' => 'binding',
//            ),

//            'manager' => array(
//                'lang' => 'Manage',
//                'url' => $this->createUrl('update/index'),
//                'id' => 'manage',
//            ),
        );
//        if (ENGINE !== 'SAAS') {
//            $cate['service'] = array(
//                'lang' => 'Service',
//                'url' => $this->createUrl('service/index'),
//                'id' => 'services',
//            );
//        }
        return $cate;
    }

    /**
     * 得到第一个安装应用的后台url
     * @return string
     */
    protected function getFirstInstallModuleUrl()
    {
        $url = Module::model()->getFirstInstallClientModule();
        if (!empty($url)){
            //对crm的数据进行容错处理
            if ($url['m'] == 'crm'){
                return Ibos::app()->urlManager->createUrl('crm/dashboard/preferences');
            }else{
                return  Ibos::app()->urlManager->createUrl($url['m']. '/'. $url['c']. '/'. $url['a']);
            }
        }else{
            return $this->createUrl('wxsync/app');
        }
    }
}
