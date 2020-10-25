<?php
namespace app\common\model\emp;

use think\Model;
use think\Db;
use think\Log;

/**
 * 员工部门
 * Class EmpDept
 * @package app\emp\model
 */
class EmpDept extends Model {
    public $table = 'emp_dept';
    public $pk = 'dept_id';
    public $pk_name = 'dept_name';
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = '';
    public $fmt_id = '';

    public $fmt_field_list = [];

}
