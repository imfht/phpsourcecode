<?php

namespace addon\superlinks\controller;

use app\common\controller\AddonBase;


class Superlinks extends AddonBase
{

    private $model = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        $this->model = $this->commonLogic->setname('superlinks');

    }

    /* 添加友情连接 */
    public function superlinksAdd()
    {

        IS_POST && $this->jump($this->model->dataAdd($this->param, ['\\addon\\superlinks\\validate\\superlinks'], '', '添加友情链接成功'));

        return $this->addonTemplate('superlinks_add');


    }

    /* 编辑友情连接 */
    public function superlinksEdit()
    {

        $info = $this->model->getDataInfo(['id' => $this->param['id']]);

        IS_POST && $this->jump($this->model->dataEdit($this->param, ['id' => $this->param['id']], ['\\addon\\superlinks\\validate\\superlinks'], '', '编辑友情链接成功'));

        $this->assign('info', $info);

        return $this->addonTemplate('superlinks_edit');

    }

    /* 禁用友情连接 */
    public function superlinksForbidden()
    {

        $this->jump($this->model->setDataValue(['id' => $this->param['id']], 'status', 0, '', '友情链接禁用成功'));


    }

    /* 启用友情连接 */
    public function superlinksOff()
    {

        $this->jump($this->model->setDataValue(['id' => $this->param['id']], 'status', 1, '', '友情链接启用成功'));

    }

    /* 删除友情连接 */
    public function superlinksDel()
    {
        $this->jump($this->model->dataDel(['id' => $this->param['id']], '删除成功', true));
    }

    /* 批量删除友情连接 */
    public function superlinksAlldel()
    {
        $this->jump($this->model->dataDel(['id' => $this->param['ids']], '删除成功', true));
    }
}
