<?php
namespace system\datalevels\common\test;

use workerbase\classs\datalevels\BaseCrudRdbDao;

/**
 * 测试DAO
 * @author fukaiyao
 */
class Test extends BaseCrudRdbDao
{
    //设置表名
    protected function tableName()
    {
        return 'test';
    }

}