<?php
namespace app\admin\Controller;

use app\admin\controller\Admin;
use think\Db;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use app\common\model\ModuleModel;

class Module extends Admin
{
    protected $moduleModel;
    //protected $cloudModel;

    function _initialize()
    {
        $this->moduleModel = model('Module');

        parent::_initialize();
    }


    public function index()
    {

        $this->setTitle(lang('_MODULE_MANAGEMENT_'));
        $aType = input('type', 'installed', 'text');
        $this->assign('type', $aType);

        /*刷新模块列表时清空缓存*/
        $aRefresh = input('refresh', 0, 'intval');
        if ($aRefresh == 1) {
            cache('admin_modules', null);
            model('Module')->reload();
        } else if ($aRefresh == 2) {
            cache('admin_modules', null);
            model('Module')->cleanModulesCache();
        }
        /*刷新模块列表时清空缓存 end*/

        //$modules = cache('admin_modules');
        //if ($modules === false) {
            $modules = model('Module')->getAll();
            //cache('admin_modules', $modules);
        //}

        foreach ($modules as $key => $m) {
            switch ($aType) {
                case 'all':
                    break;
                case 'installed':
                    if(!empty($m['can_uninstall'])){
                        if ($m['can_uninstall'] && $m['is_setup']) {
                        } else unset($modules[$key]);
                    }
                    break;
                case 'uninstalled':
                    if ($m['can_uninstall'] && $m['is_setup'] == 0) {
                    } else unset($modules[$key]);
                    break;
                case 'core':
                    if ($m['can_uninstall'] == 0) {
                    } else unset($modules[$key]);
                    break;
            }
        }
        unset($m);
        $this->assign('modules', $modules);
        return $this->fetch();
    }

    /**
     * 编辑模块
     */
    public function edit()
    {
        if (request()->isPost()) {
            $aName = input('name', '', 'text');
            $module['id'] = input('id', 0, 'intval');
            $module['name'] = empty($aName) ? $this->error(lang('_MODULE_NAME_CAN_NOT_BE_EMPTY_')) : $aName;
            $aAlias = input('alias', '', 'text');
            $module['alias'] = empty($aAlias) ? $this->error(lang('_MODULE_CHINESE_NAME_CAN_NOT_BE_EMPTY_')) : $aAlias;
            $aIcon = input('icon', '', 'text');
            $module['icon'] = empty($aIcon) ? $this->error(lang('_ICONS_CANT_BE_EMPTY_')) : $aIcon;
            $aSummary = input('summary', '', 'text');
            $module['summary'] = empty($aSummary) ? $this->error(lang('_THE_INTRODUCTION_CAN_NOT_BE_EMPTY_')) : $aSummary;
            
            //$aToken=input('token','','text');
            //$aToken=trim($aToken);

            //if($aToken!=''){
            //    if(model('common/Module')->setToken($module['name'],$aToken)){
            //        $tokenStr=lang('_TOKEN_WRITE_SUCCESS_');
            //    }else{
            //        $tokenStr=lang('_TOKEN_WRITE_FAILURE_');
            //    }

            //}


            if ($this->moduleModel->save($module,['id'=>$module['id']]) == false) {
                $this->error(lang('_EDIT_MODULE_FAILED_'));
            } else {
                $this->moduleModel->cleanModuleCache($aName);
                $this->moduleModel->cleanModulesCache();
                $this->success(lang('_EDIT_MODULE_'),Url('module/index'));
            }
        } else {
            $aName = input('name', '', 'text');
            $module = $this->moduleModel->getModule($aName);
            //$module['token']=model('common/Module')->getToken($module['name']);
            $builder = new AdminConfigBuilder();
            $builder->title(lang('_MODULE_EDIT_') . $module['alias']);
            $builder
                ->keyId()
                ->keyReadOnly('name', lang('_MODULE_NAME_'))->keyText('alias', lang('_MODULE_CHINESE_NAME_'))
                ->keyReadOnly('version', lang('_VERSION_'))
                ->keyText('icon', lang('_ICON_'))
                ->keyTextArea('summary', lang('_MODULE_INTRODUCTION_'))
                ->keyReadOnly('developer', lang('_DEVELOPER_'))
                ->keyText('entry', lang('_FRONT_ENTRANCE_'))
                ->keyText('admin_entry', lang('_BACKGROUND_ENTRY_'));
                //->keyText('token', lang('_MODULE_KEY_TOKEN_'),lang('_MODULE_KEY_TOKEN_VICE_'));

            $builder
            ->data($module)
            ->buttonSubmit()
            ->buttonBack()
            ->display();
        }

    }

    public function uninstall()
    {
        $aId = input('id', 0, 'intval');
        $aNav = input('remove_nav', 0, 'intval');
        $moduleModel = model('module');

        $module = $moduleModel->getModuleById($aId);
        //dump($module);
        if (request()->isPost()) {
            $aWithoutData = input('withoutData', 1, 'intval');//是否保留数据
            $res = $this->moduleModel->uninstall($aId, $aWithoutData);
            if ($res == true) {
                if ($aNav) {
                    Db::name('Channel')->where(['url' => $module['entry']])->delete();
                    cache('common_nav', null);
                }
                cache('admin_modules', null);
                //删除module表中记录
                $this->moduleModel->where(['id' => $aId])->delete();
                $this->success(lang('_THE_SUCCESS_OF_THE_UNLOADING_MODULE_'), Url('index'));
            } else {
                $this->error(lang('_FAILURE_OF_THE_UNLOADING_MODULE_') . $this->moduleModel->getError());
            }

        }else{
            $builder = new AdminConfigBuilder();
            $builder->title($module['alias'] . lang('_DASH_').lang('_UNLOADING_MODULE_'));
            $module['remove_nav'] = 1;
            $builder->keyReadOnly('id', lang('_MODULE_NUMBER_'));
            $builder->suggest('<span class="text-danger">'.lang('_OPERATE_CAUTION_').'</span>');
            $builder->keyReadOnly('alias', lang('_UNINSTALL_MODULE_'));
            $builder->keyBool('withoutData', lang('_KEEP_DATA_MODULE_').'?', lang('_DEFAULT_RESERVATION_MODULE_DATA_'))->keyBool('remove_nav', lang('_REMOVE_NAVIGATION_'), lang('_UNINSTALL_AUTO_UNINSTALL_MENU_',array('link'=>Url('channel/index'))));

            $module['withoutData'] = 1;
            $builder->data($module);
            $builder->buttonSubmit();
            $builder->buttonBack();
            $builder->display();
        }
    }

    /**
     * 安装模块
     * @return [type] [description]
     */
    public function install()
    {
        $aName = input('name', '', 'text');
        $aNav = input('add_nav', 0, 'intval');
        $module = $this->moduleModel->getModule($aName);

        if (request()->isPost()) {
            //执行guide中的内容
            $res = $this->moduleModel->install($module['id']);

            if ($res === true) {
                if ($aNav) {
                    $channel['title'] = $module['alias'];
                    $channel['url'] = $module['entry'];
                    $channel['sort'] = 100;
                    $channel['status'] = 1;
                    $channel['icon'] = $module['icon'];
                    Db::name('Channel')->insert($channel);
                    cache('common_nav', null);
                }
                cache('ADMIN_MODULES_' . is_login(), null);
                $this->success(lang('_INSTALLATION_MODULE_SUCCESS_'), Url('index'));
            } else {
                $this->error(lang('_SETUP_MODULE_FAILED_') . $this->moduleModel->getError());
            }

        } else {

            $role_list = model("admin/Role")->selectByMap(['status' => 1]);
            $auth_role_array=array_combine(array_column($role_list,'id'),array_column($role_list,'title'));
            $this->assign('role_list', $role_list);

            $builder = new AdminConfigBuilder();
            $builder->title($module['alias'] . lang('_DASH_') . lang('_GUIDE_MODULE_INSTALL_'));
            $builder
                ->keyId()
                ->keyReadOnly('name', lang('_MODULE_NAME_'))
                ->keyText('alias', lang('_MODULE_CHINESE_NAME_'))
                ->keyReadOnly('version', lang('_VERSION_'))
                ->keyText('icon', lang('_ICON_'))
                ->keyTextArea('summary', lang('_MODULE_INTRODUCTION_'))
                ->keyReadOnly('developer', lang('_DEVELOPER_'))
                ->keyText('entry', lang('_FRONT_ENTRANCE_'))
                ->keyText('admin_entry', lang('_BACKGROUND_ENTRY_'))
                ->keyCheckBox('auth_role', '允许身份前台访问', '都不选表示非登录状态也可访问', $auth_role_array)
                ->keyRadio('mode', lang('_INSTALLATION_MODE_'), '', array('install' => lang('_COVER_INSTALLATION_MODE_')));
                //, 'repair' => lang('_FIX_MODE_')修复模式不会导入模块专用数据表，只导入菜单、权限、行为、行为限制
            if ($module['entry']) {
                $_Link_ = Url('channel/index');
                $builder->keyBool('add_nav', lang('_ADD_NAVIGATION_'), lang('_INSTALL_AUTO_ADD_MENU_'));
            }

            $builder->group(lang('_INSTALL_OPTION_'), 'name,version,mode,add_nav,auth_role');
            
            $module['mode'] = 'install';
            $builder->data($module);
            $builder->buttonSubmit();
            $builder->buttonBack();
            $builder->display();
        }
    }

} 