<?php

namespace app\index\controller;

use app\common\controller\HomeBase;


class Article extends HomeBase
{


    public function _initialize()
    {
        parent::_initialize();


    }

    public function index($id)
    {

        if ($id > 0) {

            self::$datalogic->setname('article')->setIncOrDec(['id' => $id], 'view', 1);

            $info = self::$datalogic->setname('article')->getDataInfo(['id' => $id]);

            $this->assign('info', $info);

        } else {

            $this->error('非法操作', es_url('index/index'));

        }
        return $this->fetch();

    }


}
