<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/5
 * Time: 16:23
 */

namespace application\modules\report\controllers;


use application\core\utils\Ibos;

/**
 * Class ManagerController
 * @package application\modules\report\controllers
 * 管理模板
 */
class ManagerController extends BaseController
{

    public function actionIndex()
    {
        $this->setPageTitle(Ibos::lang('Manager template'));
        $this->setPageState('breadCrumbs',
            array(
                array('name' => Ibos::lang('Personal Office')),
                array('name' => Ibos::lang('Work report'), 'url' => $this->createUrl('manager/index')),
                array('name' => Ibos::lang('My template list'))
            ));
        $this->render('index');
    }

    public function actionAdd()
    {
        $this->setPageTitle(Ibos::lang('Manager template'));
        $this->setPageState('breadCrumbs',
            array(
                array('name' => Ibos::lang('Personal Office')),
                array('name' => Ibos::lang('Work report'), 'url' => $this->createUrl('manager/add')),
                array('name' => Ibos::lang('My template list'))
            ));
        $this->render('add');
    }

    public function actionCreate()
    {
        $this->setPageTitle(Ibos::lang('Manager template'));
        $this->setPageState('breadCrumbs',
            array(
                array('name' => Ibos::lang('Personal Office')),
                array('name' => Ibos::lang('Work report'), 'url' => $this->createUrl('manager/create')),
                array('name' => Ibos::lang('My template list'))
            ));
        $this->render('create');
    }

    public function actionEdit()
    {
        $this->setPageTitle(Ibos::lang('Manager template'));
        $this->setPageState('breadCrumbs',
            array(
                array('name' => Ibos::lang('Personal Office')),
                array('name' => Ibos::lang('Work report'), 'url' => $this->createUrl('manager/edit')),
                array('name' => Ibos::lang('My template list'))
            ));
        $this->render('edit');
    }
}