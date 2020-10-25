<?php
namespace app\common\dao;


use app\common\base\BaseDao;

interface UserInterface extends BaseDao
{
    public function getDetail($data);  //查询数据
}