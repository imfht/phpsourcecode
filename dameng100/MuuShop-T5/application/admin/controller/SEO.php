<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminSortBuilder;

class Seo extends Admin
{
    public function index($page = 1, $r = 20)
    {
        //读取规则列表
        $aApp=input('get.app','','text');
        $map = array('status' => array('EGT', 0));
        if($aApp!=''){
            $map['app']=$aApp;
        }
        //$model = Db::name('SeoRule');
        //$ruleList = $model->where($map)->order('sort asc')->select();
        list($ruleList,$page) = $this->lists('SeoRule', $map, 'sort asc');
        $page = $ruleList->render();

        $module = model('common/Module')->getAll();
        $app = array();
        foreach ($module as $m) {
            if ($m['is_setup'])
                $app[] =array('id'=>$m['name'],'value'=>$m['alias']) ;
        }
        $ruleList = $ruleList->toArray()['data'];
        //显示页面
        $builder = new AdminListBuilder();
        $builder->setSelectPostUrl(Url('index'));
        $builder
            ->title(lang('_SEO_RULE_CONFIGURATION_'))
            ->setStatusUrl(Url('setRuleStatus'))
            ->buttonEnable()
            ->buttonDisable()
            ->buttonDelete()
            ->buttonNew(Url('editRule'))
            ->buttonSort(Url('sortRule'))
            ->keyId()
            ->keyTitle()
            ->keyText('app', lang('_MODULE_PLAIN_'))
            ->keyText('controller', lang('_CONTROLLER_'))
            ->keyText('action', lang('_METHOD_'))
            ->keyText('seo_title', lang('_SEO_TITLE_'))
            ->keyText('seo_keywords', lang('_SEO_KEYWORD_'))
            ->keyText('seo_description', lang('_SEO_DESCRIPTION_'))
            ->select(lang('_MODULE_BELONGED_').lang('_COLON_'), 'app', 'select', '', '', '', array_merge(array(array('id' => '', 'value' => lang('_ALL_'))), $app))
            ->keyStatus()
            ->keyDoActionEdit('editRule?id=###')
            ->data($ruleList)
            ->page($page)
            ->display();
    }

    public function ruleTrash()
    {
        //读取规则列表
        $map = array('status' => -1);
        //$model = Db::name('SeoRule');
        //$ruleList = Db::name('SeoRule')->where($map)->order('sort asc')->select();
        list($ruleList,$page) = $this->lists('SeoRule', $map, 'sort asc');
        $page = $ruleList->render();


        //显示页面
        $builder = new AdminListBuilder();
        $builder
        ->title(lang('_SEO_RULE_RECYCLING_STATION_'))
            ->setStatusUrl(Url('setRuleStatus'))
            ->setDeleteTrueUrl(Url('doClear'))
            ->buttonRestore()
            ->buttonDeleteTrue()
            ->keyId()
            ->keyTitle()
            ->keyText('app', lang('_MODULE_PLAIN_'))
            ->keyText('controller', lang('_CONTROLLER_'))
            ->keyText('action', lang('_METHOD_'))
            ->keyText('seo_title', lang('_SEO_TITLE_'))
            ->keyText('seo_keywords', lang('_SEO_KEYWORD_'))
            ->keyText('seo_description', lang('_SEO_DESCRIPTION_'))
            ->data($ruleList)
            ->page($page)
            ->display();
    }

    public function setRuleStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('SeoRule', $ids, $status);
    }

    public function doClear($ids)
    {
        $builder = new AdminListBuilder();
        $builder->doDeleteTrue('SeoRule', $ids);
    }

    public function sortRule()
    {
        //读取规则列表
        $list = Db::name('SeoRule')->where(array('status' => array('EGT', 0)))->order('sort asc')->select();

        //显示页面
        $builder = new AdminSortBuilder();
        $builder->title(lang('_SORT_SEO_RULE_'))
            ->data($list)
            ->buttonSubmit(Url('doSortRule'))
            ->buttonBack()
            ->display();
    }

    public function doSortRule($ids)
    {
        $builder = new AdminSortBuilder();
        $builder->doSort('SeoRule', $ids);
    }

    public function editRule($id = null)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;

        //读取规则内容
        if ($isEdit) {
            $rule = Db::name('SeoRule')->where(['id' => $id])->find();
        } else {
            $rule = [
                'status' => 1,
                'action' => '',
                'summary'=> ''
            ];
        }
        $rule['action2'] = $rule['action'];
        $rule['summary']=nl2br($rule['summary']);

        //显示页面
        $builder = new AdminConfigBuilder();
        $modules = model('Module')->getAll();

        $app = ['' => lang('_MODULE_ALL_')];
        foreach ($modules as $m) {
            if ($m['is_setup']) {
                $app[$m['name']] = lcfirst($m['alias']);//首字母改小写，兼容V1
            }
        }

        $builder
            ->title($isEdit ? lang('_EDIT_RULES_') : lang('_ADD_RULE_'))
            ->keyId()
            ->keyText('title', lang('_NAME_'), lang('_RULE_NAME,_CONVENIENT_MEMORY_'))
            ->keySelect('app', lang('_MODULE_NAME_'), lang('_NOT_FILLED_IN_ALL_MODULES_'), $app)
            ->keyText('controller', lang('_CONTROLLER_'), lang('_DO_NOT_FILL_IN_ALL_CONTROLLERS_'))
            ->keyText('action2', lang('_METHOD_'), lang('_DO_NOT_FILL_OUT_ALL_THE_METHODS_'))
            ->keyText('seo_title', lang('_SEO_TITLE_'), lang('_DO_NOT_FILL_IN_THE_USE_OF_THE_NEXT_RULE,_SUPPORT_VARIABLE_'))
            ->keyText('seo_keywords', lang('_SEO_KEYWORD_'), lang('_DO_NOT_FILL_IN_THE_USE_OF_THE_NEXT_RULE,_SUPPORT_VARIABLE_'))
            ->keyTextArea('seo_description', lang('_SEO_DESCRIPTION_'), lang('_DO_NOT_FILL_IN_THE_USE_OF_THE_NEXT_RULE,_SUPPORT_VARIABLE_'))
            ->keyReadOnly('summary',lang('_VARIABLE_DESCRIPTION_'),lang('_VARIABLE_DESCRIPTION_VICE_'))
            ->keyStatus()
            ->data($rule)
            ->buttonSubmit(url('doEditRule'))
            ->buttonBack()
            ->display();
    }

    public function doEditRule($id = null, $title, $app, $controller, $action2, $seo_title, $seo_keywords, $seo_description, $status)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;
        //写入数据库
        $data = [
            'title' => $title, 
            'app' => $app, 
            'controller' => $controller, 
            'action' => $action2, 
            'seo_title' => $seo_title, 
            'seo_keywords' => $seo_keywords,
            'seo_description' => $seo_description, 
            'status' => $status
        ];

        if ($isEdit) {
            $result = Db::name('SeoRule')->where(['id' => $id])->update($data);
        } else {
            $result = Db::name('SeoRule')->insert($data);
        }
        $cacheKey = "seo_meta_{$app}_{$controller}_{$action2}";
        cache($cacheKey,null);
        //如果失败的话，显示失败消息
        if (!$result) {
            $this->error($isEdit ? lang('_EDIT_FAILED_') : lang('_CREATE_FAILURE_'));
        }

        //显示成功信息，并返回规则列表
        $this->success($isEdit ? lang('_EDIT_SUCCESS_') : lang('_CREATE_SUCCESS_'), Url('index'));
    }
}