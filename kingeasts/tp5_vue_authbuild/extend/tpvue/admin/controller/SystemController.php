<?php
// 系统配置控制器       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\controller;


use tpvue\admin\validate\ConfigValidate;
use think\facade\Config;
use tpvue\admin\model\ConfigModel;

class SystemController extends BaseController
{

    protected $middleware = ['MemberLogin','Auth'];

    /**
     * [index 空操作提醒]
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * [setConfig 配置界面读取]
     */
    public function setConfig()
    {

        $this->assign('meta_title', '网站设置');

        $configs = Config::get('config_group_list.');
        foreach ($configs as $id=>&$g) {
            $title = $g;
            $g = [];
            $g['id'] = $id;
            $g['title'] = $title;
            $g['items'] = [];
        }
        unset($g);

        $list = ConfigModel::where(['status' => 1])
            ->field('id,group,name,title,extra,value,remark,type,default,placeholder')
            ->order('sort')
            ->select()
            ->toArray();

        foreach ($list as $item) {
            if (isset($configs[$item['group']])) {
                $configs[$item['group']]['items'][] = $item;
            }
        }
        unset($list);

        $this->assign('list', $configs);
        $this->assign('active_id', $this->request->get('active', 0, 'intval'));
        return $this->fetch('system/setConfig');
    }

    /**
     * [configList 配置列表]
     */
    public function configList()
    {
        $this->assign('meta_title','配置列表');
        $configList = ConfigModel::order('sort', 'asc')->paginate(15,false,['query' => request()->param()]);
        $this->assign('list', $configList);
        return $this->fetch('system/list');
    }

    /**
     * [addconfig 添加配置]
     */
    public function addConfig()
    {
        if($this->request->isPost()){
            $validate = new ConfigValidate();
            if (!$validate->scene('addConfig')->check($this->param)) {
                $this->error($validate->getError());
            } else {
                if (ConfigModel::allowField(true)->isUpdate(false)->data($this->param)->save()) {
                    $this->success('添加成功！');
                } else {
                    $this->error(ConfigModel::getError());
                }
            }
        } else {
            $this->assign('meta_title','新增配置');
            return $this->fetch('system/addConfig');
        }
    }

    /**
     * [editconfig 修改配置]
     */
    public function editConfig($id)
    {
        if ($this->request->isPost()) {
            $validate = new ConfigValidate;
            if (!$validate->scene('editConfig')->check($this->param)) {
                $this->error($validate->getError());
            } else {
                if (ConfigModel::allowField(true)->isUpdate(true)->save($this->param)) {
                    $this->success('修改成功！');
                } else {
                    $this->error(ConfigModel::getError());
                }
            }

        } else {
            $this->assign('meta_title','编辑配置');
            if (!$id) $this->error('参数错误', url('admin/system/configList'));
            $configData = ConfigModel::where('id', (int) $id)->find();
            $this->assign('data',$configData);
            return $this->fetch('system/editConfig');
        }
    }

    public function delConfig()
    {
        if ($this->request->isPost()) {
            if (!$this->param['ids']) return json( ['msg' => '参数错误!', 'code' => '0', 'url' => url('System/configList')] );
            
            if (ConfigModel::destroy($this->param['ids'])) {
                return json( ['msg' => '删除成功！', 'code' => '1', 'url' => url('admin/system/configList')] );
            } else {
                return json( ['msg' => '删除失败！', 'code' => '0', 'url' => url('admin/system/configList')] );
            }
        } else {
            $this->fuck();
        }
    }

    /**
     * [saveAllConfig 批量保存配置]
     */
    public function saveAllConfig()
    {
        if ($this->request->isPost()) {
            $form = $this->request->post('config/a');
            if($form && is_array($form)){
                foreach ($form as $name => $value) {
                    ConfigModel::where('name', $name)->setField('value', $value);
                }
            }
            $this->success('修改成功！', 'admin/system/setConfig');
        }
    }
}
