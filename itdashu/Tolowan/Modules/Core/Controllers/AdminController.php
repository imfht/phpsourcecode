<?php
namespace Modules\Core\Controllers;

use Core\Config;
use Core\File;
use Core\Mvc\Controller;
use Modules\Core\Library\Module;
use Modules\Core\Library\Theme;
use Phalcon\Version;

class AdminController extends Controller
{

    public function frameAction()
    {
        extract($this->variables['router_params']);
        $this->variables['siteConfig'] = Config::get('site');
        $this->variables += array(
            'title' => '后台管理框架',
        );
        $this->variables += array(
            '#templates' => 'adminFrame',
            'fullUrl' => $this->request->getHTTPReferer(),
            'adminMenu' => array(
                '#templates' => 'adminMenu',
                'data' => Config::cache('menuData'),
                'hierarchy' => Config::cache('menuHierarchy'),
            ),
        );
    }

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $this->variables = array(
            'description' => '管理概况',
            'title' => '控制台',
            'breadcrumb' => array(),
            'content' => &$content,
        );
        // 添加编辑菜单
        $content['serverInfo'] = array(
            '#templates' => 'box',
            'title' => '服务器信息',
            'max' => false,
            'wrapper' => true,
            'color' => 'primary',
            'size' => '6',
            'content' => array(
                '#templates' => array(
                    'table',
                ),
                'theadDisplay' => false,
                'thead' => array(
                    '项目',
                    '信息',
                ),
                'checkAll' => false,
                'data' => array(
                    'serverInfo' => array(
                        '操作系统：',
                        php_uname('s') . ' ' . php_uname('r'),
                    ),
                    'phpVersion' => array(
                        'php版本：',
                        phpversion(),
                    ),
                    'serverIp' => array(
                        '服务器IP：',
                        $this->request->getServerAddress(),
                    ),
                    'phpMemory' => array(
                        'php可用内存：',
                        get_cfg_var("memory_limit"),
                    ),
                    'phalconVersion' => array(
                        'phalcon版本',
                        Version::get(),
                    ),
                ),
            ),
        );
    }

    public function modulesAction()
    {
        extract($this->variables['router_params']);
        $enabledModules = Config::get('modules');
        $modulesList = Module::listInfo();
        $this->variables += array(
            'title' => '模块',
            'description' => '本地模块列表，启用、禁用、删除',
            '#templates' => 'page',
            'breadcrumb' => array(
                'module' => array(
                    'name' => '模块',
                ),
            ),
            'content' => array(
                'modulesList' => array(
                    '#templates' => 'adminModules',
                    'data' => $modulesList,
                    'enabledModules' => $enabledModules
                ),
            ),
        );
    }

    public function securityAction()
    {
        extract($this->variables['router_params']);
        $securityList = Config::cache('securityAction');
        $security = Config::get('security');
        $roles = Config::get('m.user.entityUserContentModelList');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $security = array();
            foreach ($securityList as $sk => $sv) {
                $security[$sk] = array();
                foreach ($roles as $rk => $rv) {
                    if (isset($data[$sk][$rk])) {
                        $security[$sk][$rk] = true;
                    } else {
                        $security[$sk][$rk] = false;
                    }
                }
            }
            if (Config::set('security', $security)) {
                $this->flashSession->success('权限策略保存成功');
            } else {
                $this->flashSession->error('权限策略保存失败');
            }
        }
        $this->variables += array(
            'module' => '系统',
            'title' => '安全设置',
            'description' => '权限策略设置',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
            ),
            'content' => array(),
        );
        $this->variables['content']['list'] = array(
            '#templates' => 'box',
            'wrapper' => true,
            'title' => '权限设置',
            'max' => false,
            'color' => 'success',
            'size' => '12',
            'content' => array(
                '#templates' => 'security',
                'securityList' => $securityList,
                'roles' => $roles,
                'security' => $security,
            ),
        );
    }

    public function modulesUninstallAction()
    {
        extract($this->variables['router_params']);
        Module::uninstall($module);
        return $this->temMoved(['for'=>'adminModules']);
    }

    public function modulesEnableAction()
    {
        extract($this->variables['router_params']);
        Module::enable($module);
        return $this->temMoved(['for'=>'adminModules']);
    }

    public function modulesDisableAction()
    {
        extract($this->variables['router_params']);
        Module::disable($module);
        return $this->temMoved(['for'=>'adminModules']);
    }

    public function themesAction()
    {
        extract($this->variables['router_params']);
        $actionModules = array();
        $themesList = Theme::listInfo();
        $enableThemes = Config::get('themes');
        $this->variables += array(
            'title' => '主题',
            'description' => '本地主题列表，启用、禁用、删除',
            '#templates' => 'page',
            'breadcrumb' => array(
                'module' => array(
                    'name' => '主题',
                ),
            ),
            'content' => array(
                'modulesList' => array(
                    '#templates' => 'adminThemes',
                    'data' => $themesList,
                    'enableThemes' => $enableThemes
                ),
            ),
        );
    }

    public function themesEnableAction()
    {
        extract($this->variables['router_params']);
        Theme::enable($theme,$controller);
        return $this->temMoved(['for'=>'adminThemes']);
    }

    public function themesUninstallAction()
    {
        extract($this->variables['router_params']);
        Theme::uninstall($theme);
        return $this->temMoved(['for'=>'adminThemes']);
    }

    public function cacheAction()
    {
        extract($this->variables['router_params']);
        $cacheList = Config::get('cache');
        if ($handle == 'clear') {
            if (isset($cacheList[$type])) {
                $state = File::clearCache($type);
                if (!$state) {
                    $this->flash->error($state);
                } else {
                    $this->flash->success('缓存删除成功');
                }
            }
        }
        $this->variables = array(
            '#templates' => 'page',
            'title' => '缓存',
            'description' => '缓存列表',
            'pageTabs' => array(),
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'cache' => array(
                    'name' => '缓存',
                ),
            ),
            'content' => array(
                '#templates' => 'cache',
                'cacheList' => $cacheList,
            ),
        );
    }
}
