<?php
namespace app\common\model\emp;

use app\common\model\OitBase;
use think\Db;
use think\Log;

/**
 * 员工
 * Class Emp
 * @package app\emp\model
 */
class Emp extends OitBase {
    public $table = 'emp';
    public $pk = 'emp_id';
    public $pk_name = 'name';
    protected $resultSetType = 'collection'; // 以数组返回

    public $owner_fmt_id = 'emp';
    public $fmt_id = 'emp';

    public $fmt_field_list = [
        ['field' => 'name', 'width' => 80, 'title' => '姓名'],
        ['field' => 'emp_id', 'width' => 65, 'title' => '工号'],
        ['field' => 'dept_name', 'width' => 80, 'title' => '部门'],
        ['field' => 'sex', 'width' => 30, 'title' => '性别'],
        ['field' => 'technical', 'width' => 50, 'title' => '职称'],
        ['field' => 'mobile', 'width' => 50, 'title' => '手机'],
        ['field' => 'hire_date', 'width' => 120, 'title' => '入职日期'],
        ['field' => 'dept_post_name', 'width' => 80, 'title' => '岗位'],
        ['field' => 'state', 'width' => 60, 'title' => '状态'],
    ];

    public $field_dict_need = [
        'dept', 'sex', 'emp_state'
    ];

    // 返回了值覆盖了编码，那么前台过滤的检索方式就有可能会失效
    public $field_dict_def = [
        [
            'field_id' => 'dept_id',
            'dict_id' => 'dept',
            'r_field_id' => 'dept_name'
        ],
        [
            'field_id' => 'sex',
            'dict_id' => 'sex',
            'r_field_id' => 'sex'
        ],
        [
            'field_id' => 'state',
            'dict_id' => 'emp_state',
            'r_field_id' => 'state'
        ],
    ];
}
