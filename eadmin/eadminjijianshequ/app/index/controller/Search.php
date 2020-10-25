<?php

namespace app\index\controller;

use app\common\controller\HomeBase;
use esclass\database;


class Search extends HomeBase
{


    public function _initialize()
    {
        parent::_initialize();

    }

    public function index()
    {

        empty($this->param['q']) ? $keyword = '' : $keyword = $this->param['q'];
        $this->assign('keyword', htmlspecialchars($keyword));
        $uid  = is_login();
        $info = db('searchword')->where(['name' => htmlspecialchars($keyword), 'uid' => $uid])->getRow();
        if ($info) {
            $data['num'] = $info['num'] + 1;

            $data['update_time'] = time();
            db('searchword')->update($data, ['id' => $info['id']]);
        } else {
            $data['num']         = 1;
            $data['uid']         = $uid;
            $data['name']        = htmlspecialchars($keyword);
            $data['create_time'] = time();
            db('searchword')->insert($data);

        }


        return $this->fetch();

    }
}
